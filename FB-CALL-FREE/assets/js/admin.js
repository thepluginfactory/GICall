/**
 * FB Call Now - Admin JavaScript
 * Version: 4.0.0
 * Theme: SaaS Premium (Buttonizer-inspired)
 */

(function ($) {
    'use strict';

    // Wait for DOM to be ready
    $(document).ready(function () {
        initAdminSettings();
    });

    /**
     * Initialize admin settings functionality
     */
    function initAdminSettings() {
        // Initialize live preview
        initLivePreview();

        // Initialize device toggles
        initDeviceToggles();

        // Initialize button preview for Settings page
        initButtonPreview();

        // Initialize debug log functions
        initDebugLogFunctions();

        // Initialize phone number validation
        initPhoneValidation();

        // Initialize time window validation
        initTimeValidation();

        // Initialize form enhancements
        initFormEnhancements();

        // Initialize settings page navigation
        initSettingsNavigation();
    }

    /**
     * Initialize Device Toggles (Mobile/Desktop)
     */
    function initDeviceToggles() {
        $('.fbcn-device-btn').on('click', function (e) {
            e.preventDefault();

            // UI State
            $('.fbcn-device-btn').removeClass('active');
            $(this).addClass('active');

            // Logic
            var device = $(this).data('device');
            var $stage = $('#fbcn-preview-stage');

            $stage.removeClass('device-mobile device-desktop');
            $stage.addClass('device-' + device);

            // Handle Browser Chrome visibility
            if (device === 'desktop') {
                $('.fbcn-browser-chrome').fadeIn(300);
            } else {
                $('.fbcn-browser-chrome').hide();
            }

            // Re-calculate button position after transition
            setTimeout(updatePreview, 600);
        });
    }

    /**
     * Initialize live preview functionality (Mock UI)
     */
    function initLivePreview() {
        var $previewButton = $('#fbcn-live-button');

        if ($previewButton.length === 0) {
            return;
        }

        // Store reference globally
        window.fbcnPreviewButton = $previewButton;

        // Trigger initial update
        updatePreview();

        // Update preview when settings change
        // We bind to 'change' and 'input' on text fields, and 'change' on selects
        $('#button_text, [name="fbcn_basic_settings[button_shape]"], [name="fbcn_basic_settings[horizontal_position]"], [name="fbcn_basic_settings[vertical_position]"]').on('input change', function () {
            updatePreview();
        });

        // Phone number input specific listener
        $('#phone_number').on('input change', function () {
            // In pro version, phone might affect visibility or other logic, but standard button doesn't display it directly in text
            // If we wanted to validate live, we could, but updatePreview mostly handles visuals
        });

        // Note: Color pickers are handled in initColorPickers via their own callbacks

        // Initialize scroll simulation for Desktop Preview
        // We listen to the mock site container which handles the scrolling now
        var isScrolled = false;
        var scrollTimer;

        $('.fbcn-mock-site').on('scroll', function () {
            clearTimeout(scrollTimer);
            var $container = $(this);

            scrollTimer = setTimeout(function () {
                var $stage = $('#fbcn-preview-stage');
                // Apply scroll effect for BOTH Mobile and Desktop now
                // if (!$stage.hasClass('device-desktop')) { ... } REMOVED


                var scrollTop = $container.scrollTop();
                var $button = $('#fbcn-live-button');
                var scrollThreshold = 50;

                if (scrollTop > scrollThreshold && !isScrolled) {
                    isScrolled = true;

                    // Smooth transition: fix width first
                    var currentWidth = $button.outerWidth();
                    $button.css('width', currentWidth + 'px');

                    setTimeout(function () {
                        $button.addClass('scrolled');
                    }, 10);

                } else if (scrollTop <= scrollThreshold && isScrolled) {
                    isScrolled = false;

                    // Smooth expansion
                    // Remove class first to trigger expansion
                    $button.removeClass('scrolled');

                    // After transition, reset width to auto (handled by updatePreview usually, but we need to clear our fixed width)
                    // The CSS transition is 0.3s
                    setTimeout(function () {
                        $button.css('width', '');
                        // Re-run update preview to ensure correct auto-width/padding is restored if needed
                        updatePreview();
                    }, 350);
                }
            }, 10);
        });
    }

    /**
     * Update live preview with current form values
     */
    function updatePreview() {
        var $preview = $('#fbcn-live-button');
        if ($preview.length === 0) return;

        var buttonText = $('#button_text').val() || 'Call Now';
        var buttonColor = $('#button_color').val() || '#007cba';
        var textColor = $('#text_color').val() || '#ffffff';

        // Position Logic
        var horizPos = $('[name="fbcn_basic_settings[horizontal_position]"]').val() || 'right';
        var vertPos = parseInt($('[name="fbcn_basic_settings[vertical_position]"]').val() || 10);
        
        // Shape Logic
        var buttonShape = $('[name="fbcn_basic_settings[button_shape]"]').val() || 'pill';
        var borderRadiusValue = '34px';
        if (buttonShape === 'rectangular') borderRadiusValue = '0';
        if (buttonShape === 'rounded') borderRadiusValue = '8px';

        // Calculate bottom percentage based on 1-10 scale
        // Mapping 1 (top) to 10 (bottom)
        // 1 = 10%, 10 = 90% (ish)
        var topPercent = (vertPos * 8) + 5; // Simple linear mapping for mock preview

        // Refined positioning per device context
        var device = $('#fbcn-preview-stage').hasClass('device-mobile') ? 'mobile' : 'desktop';
        var sideSpacing = device === 'mobile' ? '20px' : '40px';

        // Explicitly reconstruct content to ensure order AND match frontend DOM structure
        // New Order: Icon + Text
        $preview.html(
            '<span class="dashicons dashicons-phone fbcn-button-icon"></span>' +
            '<span class="fbcn-button-text">' + buttonText + '</span>'
        );

        // Update Styles - MATCHING FRONTEND.CSS EXACTLY
        // frontend.css: .fbcn-call-button { display: inline-flex; ... }
        $preview.css({
            'background-color': buttonColor,
            'color': textColor,
            'padding': device === 'mobile' ? '12px 24px' : '12px 20px', // Match standard padding (Desktop: 12px 20px)
            'font-size': device === 'mobile' ? '16px' : '20px', // Match standard font size (Desktop: 20px)
            'border-radius': borderRadiusValue,
            'top': topPercent + '%',
            'font-weight': '600',
            'display': 'inline-flex', // Modern Flex
            'align-items': 'center',
            'justify-content': 'center',
            'gap': '0px', // Remove gap so hidden icon doesn't affect padding
            'text-align': 'center',
            'position': 'absolute', // It is absolute in the preview container
            'box-shadow': '0 4px 20px rgba(0, 0, 0, 0.15)',
            'border': 'none',
            'text-decoration': 'none',
            'width': 'auto',
            'height': 'auto'
        });

        // Icon Styles - HIDDEN INITIALLY (Text Only Mode)
        // Relies on CSS classes for transition to visible
        $preview.find('.fbcn-button-icon').css({
            'color': textColor,
            'font-size': device === 'mobile' ? '16px' : '18px',
            'position': 'static',
            'transform': 'scale(0)', /* Hidden */
            'opacity': '0', /* Hidden */
            'margin': '0',
            'width': 'auto',
            'height': 'auto',
            'display': 'flex',
            'align-items': 'center',
            'justify-content': 'center',
            'max-width': '0', /* Ensure it takes no space */
            'overflow': 'hidden' /* CRITICAL */
        });

        // Text Styles - Visible
        $preview.find('.fbcn-button-text').css({
            'display': 'inline-block',
            'opacity': '1',
            'transform': 'none',
            'text-decoration': 'none',
            'line-height': '1',
            'max-width': '200px',
            'margin': '0'
        });

        // Horizontal Position
        if (horizPos === 'right') {
            $preview.css({ right: sideSpacing, left: 'auto' });
        } else {
            $preview.css({ left: sideSpacing, right: 'auto' });
        }
    }

    /**
     * Initialize phone number validation
     */
    function initPhoneValidation() {
        var $phoneField = $('#phone_number');

        if ($phoneField.length === 0) {
            return;
        }

        $phoneField.on('blur', function () {
            validatePhoneNumber($(this));
        });

        $phoneField.on('input', function () {
            // Remove error styling on input
            $(this).removeClass('fbcn-field-error');
            $('.fbcn-error-icon').remove();
            $('#phone_number_tooltip').hide();
        });
    }

    function validatePhoneNumber($field) {
        var phoneValue = $field.val().trim();
        var phoneRegex = /^\+1-\d{3}-\d{3}-\d{4}$/;

        // Remove existing error indicators
        $field.removeClass('fbcn-field-error');
        $field.siblings('.fbcn-error-icon').remove();
        $('#phone_number_tooltip').hide();

        if (phoneValue && !phoneRegex.test(phoneValue)) {
            $field.addClass('fbcn-field-error');
            // Show the tooltip
            $('#phone_number_tooltip').show();
            return false;
        }

        return true;
    }

    /**
     * Initialize time window validation
     */
    function initTimeValidation() {
        var $startTime = $('#start_time');
        var $endTime = $('#end_time');
        var $wrapCheckbox = $('[name="fbcn_pro_settings[wrap_to_next_day]"]');

        if ($startTime.length === 0 || $endTime.length === 0) {
            return; // Not on Pro Settings page
        }

        // Validate on change
        $startTime.add($endTime).add($wrapCheckbox).on('change', function () {
            validateTimeWindow();
        });
    }

    /**
     * Validate time window settings
     */
    function validateTimeWindow() {
        var $startTime = $('#start_time');
        var $endTime = $('#end_time');
        var $wrapCheckbox = $('[name="fbcn_pro_settings[wrap_to_next_day]"]');

        var startHour = parseInt($startTime.val().split(':')[0]);
        var endHour = parseInt($endTime.val().split(':')[0]);
        var wrapEnabled = $wrapCheckbox.is(':checked');

        // Remove existing error styling
        $startTime.add($endTime).removeClass('fbcn-field-error');
        $('.fbcn-time-error').remove();

        // Check if start time is later than end time (when not wrapping)
        if (!wrapEnabled && startHour > endHour) {
            $startTime.add($endTime).addClass('fbcn-field-error');
            $endTime.after('<div class="fbcn-time-error notice notice-error inline"><p>Start time cannot be later than end time unless "Wrap to Next Day" is enabled.</p></div>');
            return false;
        }

        return true;
    }

    /**
     * Initialize form enhancements
     */
    function initFormEnhancements() {
        // Add loading state to forms
        $('form').on('submit', function () {
            var $form = $(this);
            var $submitButton = $form.find('input[type="submit"], button[type="submit"]');

            $submitButton.addClass('fbcn-loading');
            $submitButton.prop('disabled', true);

            // Reset after 5 seconds (in case form submission fails)
            setTimeout(function () {
                $submitButton.removeClass('fbcn-loading');
                $submitButton.prop('disabled', false);
            }, 5000);
        });

        // Add confirmation for destructive actions
        $('[name="fbcn_basic_settings[delete_data_on_uninstall]"]').on('change', function () {
            if ($(this).is(':checked')) {
                var confirmed = confirm('Are you sure you want to delete all plugin data when uninstalling? This action cannot be undone.');
                if (!confirmed) {
                    $(this).prop('checked', false);
                }
            }
        });

        // Initialize WordPress color pickers
        initColorPickers();
    }

    /**
     * Initialize WordPress color pickers
     */
    function initColorPickers() {
        if ($.fn.wpColorPicker) {
            $('.fbcn-color-picker').wpColorPicker({
                change: function (event, ui) {
                    // Update live preview when color changes
                    // Use timeout to ensure value is committed to input
                    setTimeout(updatePreview, 50);
                },
                clear: function () {
                    setTimeout(updatePreview, 50);
                }
            });
        }
    }

    /**
     * Initialize settings page navigation
     */
    function initSettingsNavigation() {
        // Add active state to current menu item
        var currentPage = getUrlParameter('page');
        $('.wp-submenu a[href*="' + currentPage + '"]').parent().addClass('current');

        // Add smooth scrolling to anchor links
        $('a[href^="#"]').on('click', function (e) {
            e.preventDefault();
            var target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 50
                }, 500);
            }
        });
    }

    /**
     * Get URL parameter value
     */
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    /**
     * Validate entire form before submission
     */
    function validateForm($form) {
        var isValid = true;

        // Validate phone number if present
        var $phoneField = $form.find('#phone_number');
        if ($phoneField.length && !validatePhoneNumber($phoneField)) {
            isValid = false;
        }

        // Validate time window if present
        if ($form.find('#start_time').length && !validateTimeWindow()) {
            isValid = false;
        }

        return isValid;
    }

    /**
     * Add form validation on submit
     */
    $('form').on('submit', function (e) {
        if (!validateForm($(this))) {
            e.preventDefault();

            // Scroll to first error
            var $firstError = $('.fbcn-field-error').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 300);
                $firstError.focus();
            }

            return false;
        }
    });

    /**
     * Initialize button preview functionality for Settings page
     */
    function initButtonPreview() {
        var $preview = $('#fbcn-preview-button');

        if ($preview.length === 0) {
            return;
        }

        // Force immediate styling on page load
        updatePreview();
    }

    /**
     * Copy log function for Debug Log page
     */
    function initDebugLogFunctions() {
        // Make fbcnCopyLog available globally for the Debug Log page
        window.fbcnCopyLog = function (event) {
            const logContent = document.getElementById('fbcn-log-content');
            if (!logContent) return;

            const textArea = document.createElement('textarea');
            textArea.value = logContent.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            // Show temporary feedback
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = button.getAttribute('data-copied-text') || 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        };
    }

    /**
     * Public API for external access
     */
    window.FBCallNowAdmin = {
        validateForm: validateForm,
        validatePhoneNumber: validatePhoneNumber,
        validateTimeWindow: validateTimeWindow,
        updatePreview: updatePreview,
        initButtonPreview: initButtonPreview,
        initDebugLogFunctions: initDebugLogFunctions
    };

})(jQuery);

/**
 * Initialize Pro Settings interactive UI
 */
function initProSettings() {

    // Day Pill Toggle
    $(document).on('change', '.fbcn-day-checkbox', function () {
        var $pill = $(this).closest('.fbcn-day-pill');
        if ($(this).is(':checked')) {
            $pill.addClass('active');
        } else {
            $pill.removeClass('active');
        }
    });

    // Device Card Toggle
    $(document).on('change', '.fbcn-device-checkbox', function () {
        var $card = $(this).closest('.fbcn-device-card');
        if ($(this).is(':checked')) {
            $card.addClass('active');
        } else {
            $card.removeClass('active');
        }
    });

    // API Key Input Auto-Format (XXXX-XXXX-XXXX-XXXX)
    $(document).on('input', '#fbcn_api_key', function () {
        var value = $(this).val().replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        var formatted = value.match(/.{1,4}/g);
        if (formatted) {
            $(this).val(formatted.join('-').substring(0, 19));
        }
    });

    // API Key form - button loading state
    $('.fbcn-api-key-form').on('submit', function () {
        var $btn = $(this).find('.fbcn-activate-btn');
        $btn.addClass('fbcn-loading').prop('disabled', true);
        $btn.find('.dashicons').attr('class', 'dashicons dashicons-update fbcn-spin');
    });
}

// Hook into init
var _origInit = window.FBCallNowAdmin ? null : null;
$(document).ready(function () {
    initProSettings();
});