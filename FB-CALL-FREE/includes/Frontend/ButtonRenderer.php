<?php

namespace FBCallNow\Frontend;

use FBCallNow\Core\Logger;
use FBCallNow\Core\Defaults;

/**
 * Frontend button rendering
 * 
 * @package FBCallNow\Frontend
 * @since 3.0.0
 */
class ButtonRenderer {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_call_button'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only enqueue if button should be displayed
        if (!$this->should_display_button()) {
            return;
        }
        
        // Use consistent asset versioning
        $version = $this->get_asset_version();
        
        // Enqueue Font Awesome for icons
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );
        
        wp_enqueue_style(
            'fbcn-frontend-style',
            FBCN_PLUGIN_URL . 'assets/css/frontend.css',
            array('font-awesome'),
            $version
        );
        
        wp_enqueue_script(
            'fbcn-frontend-script',
            FBCN_PLUGIN_URL . 'assets/js/frontend.js',
            array(),
            $version,
            true
        );
        
        // Pass settings to JavaScript for dynamic styling
        $basic_settings = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        
        $script_data = array(
            'buttonColor' => $basic_settings['button_color'] ?? '#007cba',
            'textColor' => $basic_settings['text_color'] ?? '#ffffff',
            'horizontalPosition' => $basic_settings['horizontal_position'] ?? 'right',
            'verticalPosition' => $basic_settings['vertical_position'] ?? 10
        );
        
        if (defined('FBCN_PRO_ACTIVE') && FBCN_PRO_ACTIVE) {
            $pro_settings = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
            $script_data['deviceVisibility'] = $pro_settings['device_visibility'] ?? array('desktop', 'tablet', 'mobile');
        } else {
             $script_data['deviceVisibility'] = array('desktop', 'tablet', 'mobile');
        }
        
        wp_localize_script(
            'fbcn-frontend-script',
            'fbcnSettings',
            $script_data
        );
    }
    
    /**
     * Get asset version for cache busting
     * 
     * Uses plugin version for proper browser caching while ensuring cache
     * invalidation on plugin updates. In debug mode, adds timestamp for
     * development convenience.
     * 
     * @return string Version string for asset cache busting
     */
    private function get_asset_version() {
        // Use plugin version as base for proper browser caching
        $version = FBCN_VERSION;
        
        // In development mode, add timestamp to force refresh
        // Only when both WP_DEBUG and SCRIPT_DEBUG are enabled
        if (defined('WP_DEBUG') && WP_DEBUG && defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            $version .= '.' . time();
        }
        
        /**
         * Filter the asset version used for cache busting
         * 
         * @param string $version The asset version string
         */
        return apply_filters('fbcn_asset_version', $version);
    }
    
    /**
     * Render the call button
     */
    public function render_call_button() {
        // Check if button should be displayed
        if (!$this->should_display_button()) {
            return;
        }
        
        $basic_settings = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        
        // Get button properties
        $button_text = esc_html($basic_settings['button_text'] ?? __('Call Now', 'fb-call-now'));
        $phone_number = esc_attr($basic_settings['phone_number'] ?? '');
        $button_color = esc_attr($basic_settings['button_color'] ?? '#007cba');
        $text_color = esc_attr($basic_settings['text_color'] ?? '#ffffff');
        $horizontal_position = esc_attr($basic_settings['horizontal_position'] ?? 'right');
        $vertical_position = absint($basic_settings['vertical_position'] ?? 10);
        
        // Calculate vertical position percentage
        // Position 1 = 5%, Position 10 = 90% (5% padding from top and bottom)
        $vertical_percentage = 5 + (($vertical_position - 1) / 9 * 85);
        
        // Build CSS classes for device visibility
        $pro_settings = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        $device_visibility = $pro_settings['device_visibility'] ?? array('desktop', 'tablet', 'mobile');
        
        $css_classes = array('fbcn-call-button');
        foreach ($device_visibility as $device) {
            $css_classes[] = 'fbcn-show-' . $device;
        }
        
        // Inline styles for positioning and colors
        $inline_styles = array(
            'background-color: ' . $button_color,
            'color: ' . $text_color,
            $horizontal_position . ': 20px',
            'top: ' . $vertical_percentage . '%'
        );
        
        // Render the button with text and icon
        ?>
        <a href="tel:<?php echo $phone_number; ?>" 
           role="button" 
           aria-label="<?php echo $button_text; ?>"
           class="<?php echo esc_attr(implode(' ', $css_classes)); ?>"
           style="<?php echo esc_attr(implode('; ', $inline_styles)); ?>">
            <span class="fbcn-button-text"><?php echo $button_text; ?></span>
            <i class="fas fa-phone fbcn-button-icon" aria-hidden="true"></i>
        </a>
        <?php
        
        Logger::info('Call button rendered for phone: ' . $phone_number);
    }
    
    /**
     * Check if button should be displayed
     */
    private function should_display_button() {
        $basic_settings = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $pro_settings = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        
        // Check if button is enabled
        if (empty($basic_settings['enable'])) {
            return false;
        }
        
        // Validate phone number exists and is properly formatted
        $phone_number = $basic_settings['phone_number'] ?? '';
        if (empty($phone_number) || !preg_match('/^\+1-\d{3}-\d{3}-\d{4}$/', $phone_number)) {
            Logger::error('Invalid or missing phone number: ' . $phone_number);
            return false;
        }
        
        // If Pro is not active, always display (Basic version has no restrictions)
        if (!defined('FBCN_PRO_ACTIVE') || !FBCN_PRO_ACTIVE) {
            return true;
        }

        // Check day of week visibility
        if (!$this->is_visible_today($pro_settings)) {
            return false;
        }
        
        // Check time window visibility
        if (!$this->is_visible_now($pro_settings)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if button should be visible today
     */
    private function is_visible_today($pro_settings) {
        // Get the saved days_visible setting
        $days_visible = isset($pro_settings['days_visible']) ? $pro_settings['days_visible'] : array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        
        // Ensure it's an array (in case it got corrupted)
        if (!is_array($days_visible)) {
            $days_visible = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        }
        
        // Get current day of week in lowercase
        $current_day = strtolower(wp_date('l'));
        
        // Check if current day is in the allowed days array
        $is_visible = in_array($current_day, $days_visible);
        
        Logger::info("Day check: Today='{$current_day}', Allowed=[" . implode(',', $days_visible) . "], Visible=" . ($is_visible ? 'YES' : 'NO'));
        
        return $is_visible;
    }
    
    /**
     * Check if button should be visible at current time
     */
    private function is_visible_now($pro_settings) {
        $start_time = $pro_settings['start_time'] ?? '00:00';
        $end_time = $pro_settings['end_time'] ?? '23:00';
        $wrap_to_next_day = $pro_settings['wrap_to_next_day'] ?? false;
        
        // Get current time in site timezone
        $current_time = wp_date('H:i');
        
        // Convert times to minutes since midnight for accurate comparison
        $current_minutes = $this->time_to_minutes($current_time);
        $start_minutes = $this->time_to_minutes($start_time);
        $end_minutes = $this->time_to_minutes($end_time);
        
        // Log for debugging
        Logger::info("Time check: Current={$current_time} ({$current_minutes}min), Start={$start_time} ({$start_minutes}min), End={$end_time} ({$end_minutes}min)");
        
        // Handle time window logic
        if ($wrap_to_next_day && $end_minutes === 23 * 60) {
            $is_visible = ($current_minutes >= $start_minutes || $current_minutes === 0);
        } elseif ($start_minutes <= $end_minutes) {
            // Same day: visible from start time until (but not including) end time
            $is_visible = ($current_minutes >= $start_minutes && $current_minutes < $end_minutes);
        } else {
            // Cross-midnight window
            $is_visible = ($current_minutes >= $start_minutes || $current_minutes < $end_minutes);
        }
        
        return $is_visible;
    }
    
    /**
     * Convert time string (HH:MM) to minutes since midnight
     */
    private function time_to_minutes($time) {
        $parts = explode(':', $time);
        return intval($parts[0]) * 60 + intval($parts[1]);
    }
    
    /**
     * Get visibility rules for debugging
     */
    public function get_visibility_debug_info() {
        $basic_settings = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $pro_settings = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        
        $debug_info = array(
            'enabled' => $basic_settings['enable'] ?? false,
            'phone_valid' => !empty($basic_settings['phone_number']) && preg_match('/^\+1-\d{3}-\d{3}-\d{4}$/', $basic_settings['phone_number']),
            'visible_today' => $this->is_visible_today($pro_settings),
            'visible_now' => $this->is_visible_now($pro_settings),
            'current_time' => wp_date('Y-m-d H:i:s'),
            'current_day' => wp_date('l'),
            'should_display' => $this->should_display_button()
        );
        
        Logger::info('Visibility check: ' . json_encode($debug_info));
        
        return $debug_info;
    }
}