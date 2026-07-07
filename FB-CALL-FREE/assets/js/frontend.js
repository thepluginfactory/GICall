/**
 * FB Call Now - Frontend JavaScript with Scroll Animation
 * Version: 3.0.1
 */

(function ($) {
    'use strict';

    // Debug flag
    var debugMode = false;

    // Scroll state tracking
    var scrollThreshold = 50; // Pixels to scroll before animation triggers
    var isScrolled = false;

    // Wait for DOM to be ready
    $(document).ready(function () {
        initCallButton();
        initScrollAnimation();
    });

    /**
     * Initialize the call button functionality
     */
    function initCallButton() {
        var $button = $('.fbcn-call-button');

        // Check if debug mode is enabled
        if (typeof fbcnSettings !== 'undefined' && fbcnSettings.debug) {
            debugMode = true;
        }

        if ($button.length === 0) {
            return;
        }


        // Apply dynamic styles from PHP settings
        if (typeof fbcnSettings !== 'undefined') {
            applyDynamicStyles($button);
        }

        // Add click tracking and device-specific behavior
        $button.on('click', function (e) {
            var $this = $(this);
            var phoneNumber = $this.attr('href').replace('tel:', '');
            var buttonText = $this.find('.fbcn-button-text').text() || 'Call Us';

            // Check if device can make phone calls
            if (!canMakePhoneCalls()) {
                e.preventDefault();
                showPhoneModal(phoneNumber, buttonText);
            }

            trackButtonClick($this);
        });

        // Add keyboard support
        $button.on('keydown', function (e) {
            // Activate on Enter or Space
            if (e.keyCode === 13 || e.keyCode === 32) {
                e.preventDefault();
                $(this)[0].click();
            }
        });

        // Make button more accessible
        if (!$button.attr('tabindex')) {
            $button.attr('tabindex', '0');
        }

        // Handle device visibility changes (responsive)
        handleResponsiveVisibility($button);

        // Add loading state support
        addLoadingStateSupport($button);

        // Initial visibility check
        checkButtonVisibility($button);

        if (debugMode) {
            // Add debug info to button
            addDebugInfo($button);
        }
    }

    /**
     * Initialize scroll animation functionality
     */
    function initScrollAnimation() {
        var $button = $('.fbcn-call-button');
        if ($button.length === 0) {
            return;
        }

        // Check initial scroll position
        checkScrollPosition();

        // Monitor scroll events
        var scrollTimer;
        $(window).on('scroll', function () {
            // Debounce scroll event for performance
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function () {
                checkScrollPosition();
            }, 10);
        });
    }

    /**
     * Check scroll position and update button state
     */
    function checkScrollPosition() {
        var $button = $('.fbcn-call-button');
        var scrollTop = $(window).scrollTop();

        // Desktop check REMOVED - Enable for all devices
        // if (getCurrentDeviceType() !== 'desktop') { ... }

        if (scrollTop > scrollThreshold && !isScrolled) {
            // User scrolled down - prepare for smooth contraction (same technique as expansion)
            isScrolled = true;

            // Store the original width before contracting
            if (!$button.data('original-width')) {
                var originalWidth = $button.outerWidth();
                $button.data('original-width', originalWidth);

                if (debugMode) {
                    console.log('FB Call Now: Storing original width:', originalWidth);
                }
            }

            // Set explicit current width first (enable transition FROM a specific value)
            var currentWidth = $button.outerWidth();
            $button.css('width', currentWidth + 'px');

            // Small delay to ensure width is set before class addition
            setTimeout(function () {
                $button.addClass('scrolled');
            }, 10);

            if (debugMode) {
                console.log('FB Call Now: Scroll animation activated');
            }
        } else if (scrollTop <= scrollThreshold && isScrolled) {
            // User scrolled back to top - prepare for smooth expansion
            isScrolled = false;

            // Set the target width before removing scrolled class
            var targetWidth = $button.data('original-width');
            if (targetWidth) {
                // Temporarily set explicit width for smooth transition
                $button.css('width', targetWidth + 'px');

                if (debugMode) {
                    console.log('FB Call Now: Setting target width for expansion:', targetWidth);
                }

                // Small delay to ensure width is set before class removal
                setTimeout(function () {
                    $button.removeClass('scrolled');

                    // After transition completes, reset to auto width
                    setTimeout(function () {
                        $button.css('width', '');
                    }, 600); // Slightly longer than transition duration
                }, 10);
            } else {
                $button.removeClass('scrolled');
            }

            if (debugMode) {
                console.log('FB Call Now: Scroll animation deactivated');
            }
        }
    }

    /**
     * Apply dynamic styles from PHP settings
     */
    function applyDynamicStyles($button) {
        var settings = fbcnSettings;


        // Apply colors (these are already applied via inline styles, but ensure consistency)
        $button.css({
            'background-color': settings.buttonColor,
            'color': settings.textColor
        });

        // Apply positioning classes
        $button.addClass('fbcn-' + settings.horizontalPosition);

        // Handle device visibility
        if (settings.deviceVisibility && settings.deviceVisibility.length > 0) {
            // Remove all device visibility classes first
            $button.removeClass('fbcn-show-desktop fbcn-show-tablet fbcn-show-mobile');

            // Add appropriate classes
            settings.deviceVisibility.forEach(function (device) {
                $button.addClass('fbcn-show-' + device);
            });
        }

    }

    /**
     * Check button visibility and log debug info
     */
    function checkButtonVisibility($button) {
        var isVisible = $button.is(':visible');
        var computedStyle = window.getComputedStyle($button[0]);
        var display = computedStyle.getPropertyValue('display');
        var opacity = computedStyle.getPropertyValue('opacity');
        var visibility = computedStyle.getPropertyValue('visibility');


        return isVisible;
    }

    /**
     * Add debug information to the button
     */
    function addDebugInfo($button) {
        var debugData = $button.data('fbcn-debug');


        // Add debug overlay
        var $debugOverlay = $('<div class="fbcn-debug-overlay"></div>');
        $debugOverlay.css({
            position: 'fixed',
            top: '10px',
            left: '10px',
            background: 'rgba(0,0,0,0.8)',
            color: '#fff',
            padding: '10px',
            'border-radius': '5px',
            'font-family': 'monospace',
            'font-size': '12px',
            'z-index': '10000',
            'max-width': '300px',
            'word-wrap': 'break-word'
        });

        var debugInfo = [
            'FB Call Now Debug',
            '==================',
            'Button visible: ' + checkButtonVisibility($button),
            'Window width: ' + $(window).width(),
            'Device type: ' + getCurrentDeviceType(),
            'Button classes: ' + $button.attr('class'),
            'Should display: ' + (debugData ? debugData.should_display : 'unknown'),
            'Scroll position: ' + $(window).scrollTop(),
            'Is scrolled: ' + isScrolled
        ];

        $debugOverlay.html(debugInfo.join('<br>'));
        $('body').append($debugOverlay);

        // Update debug overlay on resize and scroll
        $(window).on('resize scroll', function () {
            var newInfo = [
                'FB Call Now Debug',
                '==================',
                'Button visible: ' + checkButtonVisibility($button),
                'Window width: ' + $(window).width(),
                'Device type: ' + getCurrentDeviceType(),
                'Button classes: ' + $button.attr('class'),
                'Should display: ' + (debugData ? debugData.should_display : 'unknown'),
                'Scroll position: ' + $(window).scrollTop(),
                'Is scrolled: ' + isScrolled
            ];
            $debugOverlay.html(newInfo.join('<br>'));
        });
    }

    /**
     * Track button clicks for debugging/analytics
     */
    function trackButtonClick($button) {
        var phoneNumber = $button.attr('href').replace('tel:', '');

        // Track click event

        // You can add analytics tracking here
        // Example: Google Analytics event tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click', {
                'event_category': 'FB Call Now',
                'event_label': phoneNumber,
                'value': 1
            });
        }

        // Example: Facebook Pixel tracking
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Contact', {
                content_name: 'Call Button Click',
                value: phoneNumber
            });
        }
    }

    /**
     * Detect if device can make phone calls
     */
    function canMakePhoneCalls() {
        // Check if it's a mobile device that likely supports phone calls
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Check for mobile devices that typically support calling
        var isMobilePhone = /android.*mobile|iphone|ipod|blackberry|windows phone/i.test(userAgent);

        // Check for tablets (which typically don't support calling)
        var isTablet = /ipad|android(?!.*mobile)|tablet/i.test(userAgent);

        // Also check screen size as additional validation
        var isMobileScreen = window.innerWidth < 768;

        return isMobilePhone && !isTablet;
    }

    /**
     * Create and show phone number modal
     */
    function showPhoneModal(phoneNumber, buttonText) {
        // Remove any existing modal
        $('#fbcn-phone-modal').remove();

        // Format phone number for display
        var displayNumber = phoneNumber;

        // Use buttonText if provided, otherwise default to 'Call Us'
        var modalTitle = buttonText || 'Call Us';

        // Get button colors from settings
        var buttonColor = fbcnSettings.buttonColor || '#007cba';
        var textColor = fbcnSettings.textColor || '#ffffff';

        // Create modal HTML
        var modalHtml = [
            '<div id="fbcn-phone-modal" class="fbcn-modal-overlay">',
            '  <div class="fbcn-modal-content">',
            '    <div class="fbcn-modal-header">',
            '      <h3>' + modalTitle + '</h3>',
            '      <button class="fbcn-modal-close" aria-label="Close">&times;</button>',
            '    </div>',
            '    <div class="fbcn-modal-body">',
            '      <div class="fbcn-phone-display">',
            '        <i class="fas fa-phone fbcn-modal-icon" style="color: ' + buttonColor + ';"></i>',
            '        <div class="fbcn-phone-number">' + displayNumber + '</div>',
            '      </div>',
            '      <button class="fbcn-copy-button" style="background-color: ' + buttonColor + '; color: ' + textColor + ';">',
            '        <i class="fas fa-copy"></i> Copy Number',
            '      </button>',
            '      <div class="fbcn-copy-feedback" style="display: none;">Number copied!</div>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('');

        // Append modal to body
        $('body').append(modalHtml);

        // Get modal elements
        var $modal = $('#fbcn-phone-modal');
        var $closeBtn = $modal.find('.fbcn-modal-close');
        var $copyBtn = $modal.find('.fbcn-copy-button');
        var $feedback = $modal.find('.fbcn-copy-feedback');

        // Show modal with animation
        setTimeout(function () {
            $modal.addClass('fbcn-modal-show');
        }, 10);

        // Close modal handlers
        $closeBtn.on('click', function () {
            closePhoneModal();
        });

        // Close on overlay click
        $modal.on('click', function (e) {
            if (e.target === this) {
                closePhoneModal();
            }
        });

        // Close on ESC key
        $(document).on('keydown.fbcnModal', function (e) {
            if (e.keyCode === 27) {
                closePhoneModal();
            }
        });

        // Copy to clipboard functionality
        $copyBtn.on('click', function () {
            copyToClipboard(phoneNumber);

            // Show feedback
            $feedback.fadeIn(200);
            setTimeout(function () {
                $feedback.fadeOut(200);
            }, 2000);

            // Update button text temporarily
            var originalHtml = $copyBtn.html();
            $copyBtn.html('<i class="fas fa-check"></i> Copied!');
            setTimeout(function () {
                $copyBtn.html(originalHtml);
            }, 2000);
        });

        // Add hover effect for copy button with darker shade
        $copyBtn.on('mouseenter', function () {
            $(this).css('background-color', darkenColor(buttonColor, 20));
        }).on('mouseleave', function () {
            $(this).css('background-color', buttonColor);
        });
    }

    /**
     * Close phone modal
     */
    function closePhoneModal() {
        var $modal = $('#fbcn-phone-modal');

        $modal.removeClass('fbcn-modal-show');

        // Remove modal after animation
        setTimeout(function () {
            $modal.remove();
            $(document).off('keydown.fbcnModal');
        }, 300);
    }

    /**
     * Copy text to clipboard
     */
    function copyToClipboard(text) {
        // Modern clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
        } else {
            // Fallback for older browsers
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
        }
    }

    /**
     * Darken a hex color by a percentage
     */
    function darkenColor(color, percent) {
        // Remove # if present
        var hex = color.replace('#', '');

        // Convert to RGB
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);

        // Darken
        r = Math.max(0, Math.floor(r * (1 - percent / 100)));
        g = Math.max(0, Math.floor(g * (1 - percent / 100)));
        b = Math.max(0, Math.floor(b * (1 - percent / 100)));

        // Convert back to hex
        var newHex = '#' +
            ((r < 16 ? '0' : '') + r.toString(16)) +
            ((g < 16 ? '0' : '') + g.toString(16)) +
            ((b < 16 ? '0' : '') + b.toString(16));

        return newHex;
    }

    /**
     * Handle responsive visibility changes
     */
    function handleResponsiveVisibility($button) {
        // Monitor window resize to ensure proper visibility
        var resizeTimer;

        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                checkDeviceVisibility($button);
            }, 250);
        });

        // Initial check
        checkDeviceVisibility($button);
    }

    /**
     * Check if button should be visible on current device
     */
    function checkDeviceVisibility($button) {
        var windowWidth = $(window).width();
        var shouldShow = false;
        var deviceType = getCurrentDeviceType();

        // Check based on current screen size and classes
        if (windowWidth >= 992 && $button.hasClass('fbcn-show-desktop')) {
            shouldShow = true;
        } else if (windowWidth >= 768 && windowWidth <= 991 && $button.hasClass('fbcn-show-tablet')) {
            shouldShow = true;
        } else if (windowWidth < 768 && $button.hasClass('fbcn-show-mobile')) {
            shouldShow = true;
        }


        // Apply visibility classes for JavaScript-based showing/hiding
        if (shouldShow) {
            $button.removeClass('fbcn-js-hidden').addClass('fbcn-js-visible');
        } else {
            $button.removeClass('fbcn-js-visible').addClass('fbcn-js-hidden');
        }

        return shouldShow;
    }

    /**
     * Add loading state support for button
     */
    function addLoadingStateSupport($button) {
        $button.on('click', function () {
            var $this = $(this);

            // Add loading state briefly to provide user feedback
            $this.addClass('fbcn-loading');

            // Reset after short delay
            setTimeout(function () {
                $this.removeClass('fbcn-loading');
            }, 500);
        });
    }

    /**
     * Utility function to get current device type
     */
    function getCurrentDeviceType() {
        var width = $(window).width();

        if (width >= 992) {
            return 'desktop';
        } else if (width >= 768) {
            return 'tablet';
        } else {
            return 'mobile';
        }
    }

    /**
     * Public API for external access
     */
    window.FBCallNow = {
        getCurrentDeviceType: getCurrentDeviceType,
        refreshButton: function () {
            initCallButton();
        },
        hideButton: function () {
            $('.fbcn-call-button').addClass('fbcn-hidden');
        },
        showButton: function () {
            $('.fbcn-call-button').removeClass('fbcn-hidden');
        },
        checkVisibility: function () {
            var $button = $('.fbcn-call-button');
            if ($button.length > 0) {
                return checkButtonVisibility($button);
            }
            return false;
        },
        getDebugInfo: function () {
            var $button = $('.fbcn-call-button');
            if ($button.length > 0) {
                return {
                    exists: true,
                    visible: checkButtonVisibility($button),
                    classes: $button.attr('class'),
                    debugData: $button.data('fbcn-debug'),
                    deviceType: getCurrentDeviceType(),
                    windowWidth: $(window).width(),
                    scrollPosition: $(window).scrollTop(),
                    isScrolled: isScrolled
                };
            }
            return { exists: false };
        },
        // Additional methods for scroll animation control
        setScrollThreshold: function (pixels) {
            scrollThreshold = pixels;
            checkScrollPosition();
        },
        resetScrollAnimation: function () {
            isScrolled = false;
            $('.fbcn-call-button').removeClass('scrolled');
        }
    };

})(jQuery);