/**
 * FB Call Now Pro - Premium Admin JS
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // --- Day Selector Logic ---
        $('.fbcn-day-pill').on('click', function(e) {
            // Prevent double event since it's a label with checkbox
            if (e.target.type !== 'checkbox') {
                e.preventDefault();
                var $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            }
        });

        // Handle change event for visual update
        $('.fbcn-day-pill input[type="checkbox"]').on('change', function() {
            var $pill = $(this).closest('.fbcn-day-pill');
            if ($(this).is(':checked')) {
                $pill.addClass('active');
            } else {
                $pill.removeClass('active');
            }
            updateStatusCard();
        });

        // --- Device Selector Logic ---
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
            } else {
                $card.removeClass('active');
            }
        });

        // --- Time Window Validation & Status ---
        $('#start_time, #end_time, [name="fbcn_pro_settings[wrap_to_next_day]"]').on('change', function() {
            var start = $('#start_time').val();
            var end = $('#end_time').val();
            var wrap = $('[name="fbcn_pro_settings[wrap_to_next_day]"]').is(':checked');

            // Reset borders
            $('#start_time, #end_time').css('border-color', '');

            if (!wrap && start > end) {
                // Invalid: Start > End without wrap
                // Show error visual (red border)
                $('#start_time, #end_time').css('border-color', '#ef4444');
            }

            updateStatusCard();
        });

        // --- Helper: Get Current Day ---
        // We use the server-provided day as a baseline, but ideally we'd want server-side calculation.
        // For the visual preview, we'll rely on the server string passed in localization.
        
        function updateStatusCard() {
            // Get data
            var serverTime = fbcnProData.serverTime; // "HH:MM"
            var currentDay = fbcnProData.currentDay; // "monday"
            
            var start = $('#start_time').val();
            var end = $('#end_time').val();
            var wrap = $('[name="fbcn_pro_settings[wrap_to_next_day]"]').is(':checked');
            
            // Check Day
            // Note: The day selector inputs have values like 'monday'
            var isDayActive = $('.fbcn-day-pill input[value="' + currentDay + '"]').is(':checked');
            
            // Check Time
            var isTimeActive = false;
            
            // Simple string comparison works for HH:MM format
            if (wrap) {
                // e.g. 23:00 to 07:00
                if (serverTime >= start || serverTime < end) {
                    isTimeActive = true;
                }
            } else {
                // e.g. 09:00 to 17:00
                if (serverTime >= start && serverTime < end) {
                    isTimeActive = true;
                }
            }
            
            // Update UI
            var isVisible = isDayActive && isTimeActive;
            
            var $card = $('#fbcn-live-status-card');
            
            // Update Badge & Status Class
            var $statusText = $card.find('.status-text');
            
            if (isVisible) {
                $card.removeClass('status-offline').addClass('status-live');
                $statusText.text('ACTIVE');
            } else {
                $card.removeClass('status-live').addClass('status-offline');
                $statusText.text('INACTIVE');
            }
            
            // Update Check List
            var $dayCheck = $('#check-day');
            var $dayIcon = $dayCheck.find('.dashicons');
            
            if (isDayActive) {
                $dayCheck.removeClass('failing').addClass('passing');
                $dayIcon.removeClass('dashicons-no').addClass('dashicons-yes');
            } else {
                $dayCheck.removeClass('passing').addClass('failing');
                $dayIcon.removeClass('dashicons-yes').addClass('dashicons-no');
            }
            
            var $timeCheck = $('#check-time');
            var $timeIcon = $timeCheck.find('.dashicons');
            
            if (isTimeActive) {
                $timeCheck.removeClass('failing').addClass('passing');
                $timeIcon.removeClass('dashicons-no').addClass('dashicons-yes');
            } else {
                $timeCheck.removeClass('passing').addClass('failing');
                $timeIcon.removeClass('dashicons-yes').addClass('dashicons-no');
            }
        }
        
        // Initial run
        updateStatusCard();

    });

})(jQuery);
