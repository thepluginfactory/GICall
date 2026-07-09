/**
 * FB Call Now Pro - Premium Admin JS
 * Enhanced with smooth interactions and animations
 * Version: 2.0.0
 */
(function($) {
    'use strict';

    // Configuration
    const CONFIG = {
        animationDuration: 300,
        debounceDelay: 100
    };

    $(document).ready(function() {
        
        initDaySelector();
        initDeviceSelector();
        initTimeWindow();
        initFormValidation();
        initSmoothScrolling();
        updateStatusCard();

        // Re-update status card on any settings change
        $(document).on('change', 'input, select', function() {
            debounce(updateStatusCard, CONFIG.debounceDelay)();
        });
    });

    // ===== Day Selector =====
    function initDaySelector() {
        $('.fbcn-day-pill').on('click', function(e) {
            if (e.target.type !== 'checkbox') {
                e.preventDefault();
                var $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            }
        });

        $('.fbcn-day-pill input[type="checkbox"]').on('change', function() {
            var $pill = $(this).closest('.fbcn-day-pill');
            
            if ($(this).is(':checked')) {
                $pill.addClass('active');
                triggerPulse($pill);
            } else {
                $pill.removeClass('active');
            }
        });
    }

    // ===== Device Selector =====
    function initDeviceSelector() {
        $('.fbcn-device-card').on('click', function(e) {
            if (e.target.type !== 'checkbox') {
                e.preventDefault();
                var $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            }
        });

        $('.fbcn-device-card input[type="checkbox"]').on('change', function() {
            var $card = $(this).closest('.fbcn-device-card');
            
            if ($(this).is(':checked')) {
                $card.addClass('active');
                triggerPulse($card);
            } else {
                $card.removeClass('active');
            }
        });
    }

    // ===== Time Window =====
    function initTimeWindow() {
        $('#start_time, #end_time, [name="fbcn_pro_settings[wrap_to_next_day]"]').on('change', function() {
            validateTimeWindow();
        });
    }

    function validateTimeWindow() {
        var start = $('#start_time').val();
        var end = $('#end_time').val();
        var wrap = $('[name="fbcn_pro_settings[wrap_to_next_day]"]').is(':checked');

        // Reset to normal appearance
        $('#start_time, #end_time').removeClass('error');

        if (!wrap && start > end) {
            // Invalid: Start > End without wrap
            $('#start_time, #end_time').addClass('error');
            showFieldError('#end_time', 'End time must be after start time');
        } else {
            clearFieldError('#end_time');
        }
    }

    // ===== Form Validation =====
    function initFormValidation() {
        // Add visual feedback to form fields
        $('input, select, textarea').on('focus', function() {
            $(this).closest('.fbcn-control-group, .fbcn-pro-field-group, .fbcn-time-field').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.fbcn-control-group, .fbcn-pro-field-group, .fbcn-time-field').removeClass('focused');
        });

        // Form submission animation
        $('form.fbcn-main-form').on('submit', function() {
            var $btn = $(this).find('[type="submit"]');
            if ($btn.length) {
                $btn.css('opacity', '0.7').prop('disabled', true);
                setTimeout(function() {
                    $btn.css('opacity', '1');
                }, 1000);
            }
        });
    }

    // ===== Smooth Scrolling =====
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            if ($(target).length) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, CONFIG.animationDuration);
            }
        });
    }

    // ===== Status Card Update =====
    function updateStatusCard() {
        if (!window.fbcnProData) return;

        var serverTime = window.fbcnProData.serverTime; // "HH:MM"
        var currentDay = window.fbcnProData.currentDay; // "monday"
        
        var start = $('#start_time').val();
        var end = $('#end_time').val();
        var wrap = $('[name="fbcn_pro_settings[wrap_to_next_day]"]').is(':checked');
        
        // Check if day is selected
        var isDayActive = $('.fbcn-day-pill input[value="' + currentDay + '"]').is(':checked');
        
        // Check if current time is in window
        var isTimeActive = false;
        
        if (wrap) {
            // Wraps to next day: 23:00 to 07:00
            isTimeActive = (serverTime >= start || serverTime < end);
        } else {
            // Same day: 09:00 to 17:00
            isTimeActive = (serverTime >= start && serverTime < end);
        }
        
        var isVisible = isDayActive && isTimeActive;
        var $card = $('#fbcn-live-status-card');
        
        if (!$card.length) return;

        // Update status panel with animation
        if (isVisible) {
            $card.removeClass('status-offline').addClass('status-live');
            var $badge = $card.find('.fbcn-pulse-badge');
            $badge.find('.fbcn-pulse-badge').text('Live');
        } else {
            $card.removeClass('status-live').addClass('status-offline');
            var $badge = $card.find('.fbcn-pulse-badge');
            $badge.text('Offline');
        }
        
        // Update day check
        var $dayCheck = $('#check-day');
        if ($dayCheck.length) {
            updateCheckIcon($dayCheck, isDayActive);
        }
        
        // Update time check
        var $timeCheck = $('#check-time');
        if ($timeCheck.length) {
            updateCheckIcon($timeCheck, isTimeActive);
        }
        
        // Update visibility check
        var $visCheck = $('#check-visible');
        if ($visCheck.length) {
            updateCheckIcon($visCheck, isVisible);
        }
    }

    function updateCheckIcon($elem, isPass) {
        $elem.removeClass('passing failing');
        $elem.addClass(isPass ? 'passing' : 'failing');
        
        var $icon = $elem.find('.check-icon .dashicons');
        if ($icon.length) {
            $icon.removeClass('dashicons-no dashicons-yes');
            $icon.addClass(isPass ? 'dashicons-yes' : 'dashicons-no');
        }
    }

    // ===== Utility Functions =====
    
    function triggerPulse($elem) {
        $elem.css('animation', 'none');
        setTimeout(function() {
            $elem.css('animation', '');
        }, 10);
    }

    function showFieldError($fieldSelector, message) {
        var $field = $($fieldSelector);
        if (!$field.length) return;

        var $error = $field.next('.field-error');
        if ($error.length) {
            $error.text(message).fadeIn(CONFIG.animationDuration);
        } else {
            $('<div class="field-error" style="color: #ef4444; font-size: 12px; margin-top: 4px;">' + message + '</div>')
                .insertAfter($field);
        }
        
        $field.css('border-color', '#ef4444');
    }

    function clearFieldError($fieldSelector) {
        var $field = $($fieldSelector);
        if (!$field.length) return;

        $field.css('border-color', '');
        $field.next('.field-error').fadeOut(CONFIG.animationDuration, function() {
            $(this).remove();
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ===== Page Load Animation =====
    $(window).on('load', function() {
        // Animate cards on page load
        $('.fbcn-card').each(function(index) {
            $(this).delay(index * 100).fadeIn(300);
        });
    });

    // ===== Keyboard Navigation =====
    $(document).on('keydown', function(e) {
        // Allow Tab through form elements
        if (e.key === 'Enter' && $(e.target).is('input[type="text"], input[type="email"], select, textarea')) {
            // Allow default behavior
        }
    });

    // ===== Responsive Behavior =====
    $(window).on('resize', function() {
        debounce(function() {
            updateStatusCard();
        }, CONFIG.debounceDelay)();
    });

})(jQuery);
