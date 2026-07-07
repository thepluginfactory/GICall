<?php

namespace FBCallNow\Admin;

use FBCallNow\Core\Logger;
use FBCallNow\Core\Defaults;

/**
 * Admin settings management
 * 
 * @package FBCallNow\Admin
 * @since 3.0.0
 */
class Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Add admin menu and submenus
     */
    public function add_admin_menu() {
        // Add top-level menu
        add_menu_page(
            __('FB Call Now', 'fb-call-now'),
            __('FB Call Now', 'fb-call-now'),
            'manage_options',
            'fb-call-now',
            '', // No callback - will redirect to first submenu
            'dashicons-phone',
            30
        );
        
        // Add Basic Settings submenu
        add_submenu_page(
            'fb-call-now',
            __('Basic Settings', 'fb-call-now'),
            __('Basic Settings', 'fb-call-now'),
            'manage_options',
            'fbcn_basic_settings',
            array($this, 'basic_settings_page')
        );
        
        // Add Pro Settings submenu
        if (defined('FBCN_PRO_ACTIVE') && FBCN_PRO_ACTIVE) {
            add_submenu_page(
                'fb-call-now',
                __('Pro Settings', 'fb-call-now'),
                __('Pro Settings', 'fb-call-now'),
                'manage_options',
                'fbcn_pro_settings',
                array($this, 'pro_settings_page')
            );
        }
        
        // Add User Guide submenu
        add_submenu_page(
            'fb-call-now',
            __('User Guide', 'fb-call-now'),
            __('User Guide', 'fb-call-now'),
            'manage_options',
            'fbcn_user_guide',
            array($this, 'user_guide_page')
        );
        
        // Remove the duplicate top-level menu link
        remove_submenu_page('fb-call-now', 'fb-call-now');
        
        Logger::info('Admin menu initialized');
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        // Register basic settings
        register_setting(
            'fbcn_basic_settings_group',
            'fbcn_basic_settings',
            array(
                'sanitize_callback' => array($this, 'sanitize_basic_settings'),
                'default' => Defaults::get_basic_settings()
            )
        );
        
        // Register pro settings
        if (defined('FBCN_PRO_ACTIVE') && FBCN_PRO_ACTIVE) {
            register_setting(
                'fbcn_pro_settings_group',
                'fbcn_pro_settings',
                array(
                    'sanitize_callback' => array($this, 'sanitize_pro_settings'),
                    'default' => Defaults::get_pro_settings()
                )
            );
        }
        
        // Add settings sections and fields
        $this->add_basic_settings_fields();
        if (defined('FBCN_PRO_ACTIVE') && FBCN_PRO_ACTIVE) {
            $this->add_pro_settings_fields();
        }
    }
    
    /**
     * Add basic settings fields
     */
    private function add_basic_settings_fields() {
        // Basic Settings Section
        add_settings_section(
            'fbcn_basic_section',
            '', // Title hidden (handled by card header)
            array($this, 'basic_section_callback'),
            'fbcn_basic_settings'
        );
        
        // Enable Button field
        add_settings_field(
            'enable',
            __('Enable Button', 'fb-call-now'),
            array($this, 'enable_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Button Text field
        add_settings_field(
            'button_text',
            __('Button Text', 'fb-call-now'),
            array($this, 'button_text_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Phone Number field
        add_settings_field(
            'phone_number',
            __('Telephone Number', 'fb-call-now'),
            array($this, 'phone_number_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Button Color field
        add_settings_field(
            'button_color',
            __('Button Color', 'fb-call-now'),
            array($this, 'button_color_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Text Color field
        add_settings_field(
            'text_color',
            __('Text Color', 'fb-call-now'),
            array($this, 'text_color_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Horizontal Position field
        add_settings_field(
            'horizontal_position',
            __('Horizontal Position', 'fb-call-now'),
            array($this, 'horizontal_position_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Vertical Position field
        add_settings_field(
            'vertical_position',
            __('Vertical Position', 'fb-call-now'),
            array($this, 'vertical_position_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
        
        // Delete Data field
        add_settings_field(
            'delete_data_on_uninstall',
            __('Delete Data on Uninstall', 'fb-call-now'),
            array($this, 'delete_data_field_callback'),
            'fbcn_basic_settings',
            'fbcn_basic_section'
        );
    }
    
    /**
     * Add pro settings fields
     */
    private function add_pro_settings_fields() {
        // Pro Settings Section
        add_settings_section(
            'fbcn_pro_section',
            __('Pro Configuration', 'fb-call-now'),
            array($this, 'pro_section_callback'),
            'fbcn_pro_settings'
        );
        
        // Days Visible field
        add_settings_field(
            'days_visible',
            __('Day-of-Week Visibility', 'fb-call-now'),
            array($this, 'days_visible_field_callback'),
            'fbcn_pro_settings',
            'fbcn_pro_section'
        );
        
        // Time Window fields
        add_settings_field(
            'time_window',
            __('Time Window', 'fb-call-now'),
            array($this, 'time_window_field_callback'),
            'fbcn_pro_settings',
            'fbcn_pro_section'
        );
        
        // Device Visibility field
        add_settings_field(
            'device_visibility',
            __('Device Visibility', 'fb-call-now'),
            array($this, 'device_visibility_field_callback'),
            'fbcn_pro_settings',
            'fbcn_pro_section'
        );
        
        // Debug Logging field
        add_settings_field(
            'debug_logging',
            __('Debug Logging', 'fb-call-now'),
            array($this, 'debug_logging_field_callback'),
            'fbcn_pro_settings',
            'fbcn_pro_section'
        );
    }
    
    
    /**
     * Sanitize basic settings
     */
    public function sanitize_basic_settings($input) {
        $sanitized = array();
        
        // Enable checkbox
        $sanitized['enable'] = isset($input['enable']) ? true : false;
        
        // Button text
        $sanitized['button_text'] = sanitize_text_field($input['button_text'] ?? Defaults::get_basic_settings()['button_text']);
        
        // Phone number validation
        $phone = sanitize_text_field($input['phone_number'] ?? '');
        $phone = preg_replace('/[^\d+\-]/', '', $phone); // Remove spaces and invalid chars
        if (preg_match('/^\+1-\d{3}-\d{3}-\d{4}$/', $phone)) {
            $sanitized['phone_number'] = $phone;
        } else {
            $sanitized['phone_number'] = '';
            add_settings_error('fbcn_basic_settings', 'invalid_phone', __('Invalid phone number format. Please use +1-XXX-XXX-XXXX format.', 'fb-call-now'));
        }
        
        // Colors
        $sanitized['button_color'] = sanitize_hex_color($input['button_color'] ?? '#007cba') ?: '#007cba';
        $sanitized['text_color'] = sanitize_hex_color($input['text_color'] ?? '#ffffff') ?: '#ffffff';
        
        // Positions
        $sanitized['horizontal_position'] = in_array($input['horizontal_position'] ?? 'right', array('left', 'right')) ? $input['horizontal_position'] : 'right';
        $sanitized['vertical_position'] = max(1, min(10, absint($input['vertical_position'] ?? 10)));
        
        // Delete data checkbox
        $sanitized['delete_data_on_uninstall'] = isset($input['delete_data_on_uninstall']) ? true : false;
        
        Logger::info('Basic Settings saved: ' . json_encode($sanitized));
        
        return $sanitized;
    }
    
    /**
     * Sanitize pro settings
     */
    public function sanitize_pro_settings($input) {
        $sanitized = array();
        
        // Validate each setting group using focused methods
        $sanitized = array_merge($sanitized, $this->sanitize_days_visible($input));
        $sanitized = array_merge($sanitized, $this->sanitize_time_window($input));
        $sanitized = array_merge($sanitized, $this->sanitize_device_visibility($input));
        $sanitized = array_merge($sanitized, $this->sanitize_debug_logging($input));
        
        Logger::info('Pro Settings saved: ' . json_encode($sanitized));
        
        return $sanitized;
    }
    
    /**
     * Sanitize days visible setting
     * 
     * @param array $input Raw input data
     * @return array Sanitized days visible data
     */
    private function sanitize_days_visible($input) {
        $valid_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        $sanitized = array('days_visible' => array());
        
        if (isset($input['days_visible']) && is_array($input['days_visible'])) {
            foreach ($input['days_visible'] as $day) {
                if (in_array($day, $valid_days)) {
                    $sanitized['days_visible'][] = $day;
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize time window settings
     * 
     * @param array $input Raw input data
     * @return array Sanitized time window data
     */
    private function sanitize_time_window($input) {
        $sanitized = array();
        
        // Sanitize individual time values
        $start_time = sanitize_text_field($input['start_time'] ?? '00:00');
        $end_time = sanitize_text_field($input['end_time'] ?? '23:00');
        
        // Validate time format
        if (preg_match('/^([0-1][0-9]|2[0-3]):00$/', $start_time)) {
            $sanitized['start_time'] = $start_time;
        } else {
            $sanitized['start_time'] = '00:00';
        }
        
        if (preg_match('/^([0-1][0-9]|2[0-3]):00$/', $end_time)) {
            $sanitized['end_time'] = $end_time;
        } else {
            $sanitized['end_time'] = '23:00';
        }
        
        // Handle wrap to next day setting
        $wrap_to_next_day = isset($input['wrap_to_next_day']) ? true : false;
        $sanitized['wrap_to_next_day'] = $wrap_to_next_day;
        
        // Validate time window logic
        if (!$wrap_to_next_day) {
            $start_hour = intval(substr($sanitized['start_time'], 0, 2));
            $end_hour = intval(substr($sanitized['end_time'], 0, 2));
            
            if ($start_hour > $end_hour) {
                add_settings_error('fbcn_pro_settings', 'invalid_time_window', __('Start time cannot be later than end time unless "Wrap to Next Day" is enabled.', 'fb-call-now'));
                $sanitized['start_time'] = '00:00';
                $sanitized['end_time'] = '23:00';
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize device visibility settings
     * 
     * @param array $input Raw input data
     * @return array Sanitized device visibility data
     */
    private function sanitize_device_visibility($input) {
        $valid_devices = array('desktop', 'tablet', 'mobile');
        $sanitized = array('device_visibility' => array());
        
        if (isset($input['device_visibility']) && is_array($input['device_visibility'])) {
            foreach ($input['device_visibility'] as $device) {
                if (in_array($device, $valid_devices)) {
                    $sanitized['device_visibility'][] = $device;
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize debug logging setting
     * 
     * @param array $input Raw input data
     * @return array Sanitized debug logging data
     */
    private function sanitize_debug_logging($input) {
        return array(
            'debug_logging' => isset($input['debug_logging']) ? true : false
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'fbcn_') === false && $hook !== 'toplevel_page_fb-call-now') {
            return;
        }
        
        // Use improved cache busting strategy
        $version = $this->get_asset_version();
        
        // Enqueue WordPress color picker styles
        wp_enqueue_style('wp-color-picker');
        
        wp_enqueue_style(
            'fbcn-admin-style',
            FBCN_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            $version
        );
        
        wp_enqueue_script(
            'fbcn-admin-script',
            FBCN_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            $version,
            true
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
        // Force timestamp for immediate troubleshooting
        $version .= '.' . time();
        if (false && defined('WP_DEBUG') && WP_DEBUG && defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
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
     * Basic settings page
     */
    /**
     * Basic settings page
     */
    /**
     * Basic settings page
     */
    public function basic_settings_page() {
        // Get current settings for preview
        $basic_defaults = Defaults::get_basic_settings();
        $basic_settings = get_option('fbcn_basic_settings', $basic_defaults);
        $button_text = esc_html($basic_settings['button_text'] ?? $basic_defaults['button_text']);
        $button_color = esc_attr($basic_settings['button_color'] ?? $basic_defaults['button_color']);
        $text_color = esc_attr($basic_settings['text_color'] ?? $basic_defaults['text_color']);
        ?>
        <div class="wrap fbcn-settings-page">
            <!-- SaaS Header -->
            <div class="fbcn-saas-header">
                <div class="fbcn-brand">
                    <div class="fbcn-logo-icon">
                        <span class="dashicons dashicons-phone"></span>
                    </div>
                    <div class="fbcn-brand-text">
                        <h1>FB Call Now</h1>
                        <span class="fbcn-byline">by ThePluginFactory</span>
                    </div>
                    <span class="fbcn-badge-free">Free</span>
                </div>
                <div class="fbcn-meta">
                    <span class="fbcn-version">v4.1.77</span>
                </div>
            </div>
            
            <div class="fbcn-admin-wrapper">
                <!-- Left Column: Settings Form -->
                <div class="fbcn-settings-column">
                    <form method="post" action="options.php" class="fbcn-main-form">
                        <?php
                        settings_fields('fbcn_basic_settings_group');
                        ?>
                        
                        <div class="fbcn-card">
                            <div class="fbcn-card-header">
                                <h2><?php _e('Button Configuration', 'fb-call-now'); ?></h2>
                                <p><?php _e('Customize the look and feel of your call button.', 'fb-call-now'); ?></p>
                            </div>
                            <div class="fbcn-card-body">
                                <?php do_settings_sections('fbcn_basic_settings'); ?>
                            </div>
                            <div class="fbcn-card-footer">
                                <?php submit_button(__('Save Changes', 'fb-call-now'), 'primary large'); ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Live Preview (Canvas) -->
                <div class="fbcn-preview-column">
                    <div class="fbcn-preview-card">
                        <div class="fbcn-preview-header">
                            <div class="fbcn-preview-title">
                                <h3><?php _e('Live Preview', 'fb-call-now'); ?></h3>
                                <p><?php _e('Real-time visualization', 'fb-call-now'); ?></p>
                            </div>
                            <div class="fbcn-device-toggles">
                                <button type="button" class="fbcn-device-btn active" data-device="mobile" title="<?php _e('Mobile View', 'fb-call-now'); ?>">
                                    <span class="dashicons dashicons-smartphone"></span>
                                    <span>Mobile</span>
                                </button>
                                <button type="button" class="fbcn-device-btn" data-device="desktop" title="<?php _e('Desktop View', 'fb-call-now'); ?>">
                                    <span class="dashicons dashicons-desktop"></span>
                                    <span>Desktop</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="fbcn-preview-stage-wrapper">
                            <!-- Browser Chrome (Only visible in desktop mode) -->
                            <div class="fbcn-browser-chrome">
                                <div class="fbcn-browser-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="fbcn-browser-bar"></div>
                            </div>

                            <div id="fbcn-preview-stage" class="fbcn-preview-stage device-mobile">
                                <div class="fbcn-device-frame">
                                    <div class="fbcn-device-screen">
                                        <!-- Mock Website UI -->
                                        <div class="fbcn-mock-site">
                                            <div class="fbcn-mock-header">
                                                <div class="fbcn-mock-logo"></div>
                                                <div class="fbcn-mock-nav"></div>
                                            </div>
                                            <div class="fbcn-mock-hero">
                                                <div class="fbcn-mock-hero-text"></div>
                                                <div class="fbcn-mock-hero-btn"></div>
                                            </div>
                                            <div class="fbcn-mock-content">
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line short"></div>
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line short"></div>
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line short"></div>
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line"></div>
                                                <div class="fbcn-mock-line short"></div>
                                                <div class="fbcn-mock-line"></div>
                                            </div>
                                            
                                            <!-- Live Preview Button -->
                                            <div id="fbcn-live-button" class="fbcn-call-button">
                                                <span class="fbcn-button-text">Call Now</span>
                                                <span class="dashicons dashicons-phone fbcn-button-icon"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
        /**
     * Pro settings page with API key gate and premium UI
     */
    public function pro_settings_page() {
        $saved_key = get_option('fbcn_pro_api_key', '');
        $is_activated = !empty($saved_key);
        if (isset($_POST['fbcn_save_api_key']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbcn_api_key_nonce_field'] ?? '' ) ), 'fbcn_api_key_nonce' )) {
            $new_key = sanitize_text_field($_POST['fbcn_api_key'] ?? '');
            if (!empty($new_key)) {
                update_option('fbcn_pro_api_key', $new_key);
                $saved_key = $new_key;
                $is_activated = true;
            }
        }
        if (isset($_POST['fbcn_deactivate_key']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbcn_api_key_nonce_field'] ?? '' ) ), 'fbcn_api_key_nonce' )) {
            delete_option('fbcn_pro_api_key');
            $saved_key = '';
            $is_activated = false;
        }
        ?>
        <div class="wrap fbcn-settings-page fbcn-pro-page">
            <div class="fbcn-saas-header">
                <div class="fbcn-brand">
                    <div class="fbcn-logo-icon"><span class="dashicons dashicons-phone"></span></div>
                    <div class="fbcn-brand-text"><h1>FB Call Now</h1><span class="fbcn-byline">by ThePluginFactory</span></div>
                    <span class="fbcn-badge-pro">Pro</span>
                </div>
                <div class="fbcn-meta"><span class="fbcn-version">v<?php echo FBCN_VERSION; ?></span></div>
            </div>

            <?php if ( ! $is_activated ) : ?>
            <div class="fbcn-api-gate">
                <div class="fbcn-api-gate-card">
                    <div class="fbcn-api-gate-hero">
                        <div class="fbcn-gate-icon-wrap"><span class="dashicons dashicons-lock fbcn-gate-icon"></span></div>
                        <h2><?php _e( 'Activate Pro Features', 'fb-call-now' ); ?></h2>
                        <p><?php _e( 'Enter your license key to unlock advanced visibility controls including day-of-week scheduling, time windows, and device targeting.', 'fb-call-now' ); ?></p>
                    </div>
                    <form method="post" action="" class="fbcn-api-key-form">
                        <?php wp_nonce_field( 'fbcn_api_key_nonce', 'fbcn_api_key_nonce_field' ); ?>
                        <div class="fbcn-api-input-group">
                            <label for="fbcn_api_key" class="fbcn-api-label">
                                <span class="dashicons dashicons-admin-network"></span>
                                <?php _e( 'License Key', 'fb-call-now' ); ?>
                            </label>
                            <div class="fbcn-api-input-wrap">
                                <input type="text" id="fbcn_api_key" name="fbcn_api_key" class="fbcn-api-key-input" placeholder="XXXX-XXXX-XXXX-XXXX" autocomplete="off" />
                                <button type="submit" name="fbcn_save_api_key" class="fbcn-activate-btn">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php _e( 'Activate', 'fb-call-now' ); ?>
                                </button>
                            </div>
                            <p class="fbcn-api-hint"><?php _e( 'Your license key was emailed after purchase.', 'fb-call-now' ); ?> <a href="https://thepluginfactory.com/support" target="_blank"><?php _e( 'Contact Support', 'fb-call-now' ); ?></a></p>
                        </div>
                    </form>
                    <div class="fbcn-pro-features-preview">
                        <h3><?php _e( 'What\'s included in Pro', 'fb-call-now' ); ?></h3>
                        <div class="fbcn-feature-grid">
                            <div class="fbcn-feature-item"><span class="dashicons dashicons-calendar-alt fbcn-feature-icon"></span><div><strong><?php _e( 'Day Scheduling', 'fb-call-now' ); ?></strong><span><?php _e( 'Show on specific days only', 'fb-call-now' ); ?></span></div></div>
                            <div class="fbcn-feature-item"><span class="dashicons dashicons-clock fbcn-feature-icon"></span><div><strong><?php _e( 'Time Windows', 'fb-call-now' ); ?></strong><span><?php _e( 'Set active hours for your button', 'fb-call-now' ); ?></span></div></div>
                            <div class="fbcn-feature-item"><span class="dashicons dashicons-smartphone fbcn-feature-icon"></span><div><strong><?php _e( 'Device Targeting', 'fb-call-now' ); ?></strong><span><?php _e( 'Desktop, tablet, or mobile only', 'fb-call-now' ); ?></span></div></div>
                            <div class="fbcn-feature-item"><span class="dashicons dashicons-chart-area fbcn-feature-icon"></span><div><strong><?php _e( 'Debug Logging', 'fb-call-now' ); ?></strong><span><?php _e( 'Advanced troubleshooting tools', 'fb-call-now' ); ?></span></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php else : ?>
            <div class="fbcn-pro-activated-bar">
                <div class="fbcn-activated-info">
                    <span class="dashicons dashicons-yes-alt fbcn-activated-icon"></span>
                    <span class="fbcn-activated-label"><?php _e( 'Pro License Active', 'fb-call-now' ); ?></span>
                    <span class="fbcn-activated-key"><?php echo esc_html( substr( $saved_key, 0, 4 ) . '-****-****-' . substr( $saved_key, -4 ) ); ?></span>
                </div>
                <form method="post" action="" style="display:inline;">
                    <?php wp_nonce_field( 'fbcn_api_key_nonce', 'fbcn_api_key_nonce_field' ); ?>
                    <button type="submit" name="fbcn_deactivate_key" class="fbcn-deactivate-btn"><span class="dashicons dashicons-no"></span><?php _e( 'Deactivate', 'fb-call-now' ); ?></button>
                </form>
            </div>
            <div class="fbcn-admin-wrapper fbcn-pro-wrapper">
                <div class="fbcn-settings-column">
                    <form method="post" action="options.php" class="fbcn-main-form">
                        <?php settings_fields( 'fbcn_pro_settings_group' ); ?>
                        <?php $options = get_option( 'fbcn_pro_settings', Defaults::get_pro_settings() ); ?>
                        <?php $selected_days = $options['days_visible'] ?? []; ?>
                        <?php $start_time = $options['start_time'] ?? '00:00'; ?>
                        <?php $end_time = $options['end_time'] ?? '23:00'; ?>
                        <?php $wrap_to_next_day = isset( $options['wrap_to_next_day'] ) && $options['wrap_to_next_day']; ?>
                        <?php $selected_devices = $options['device_visibility'] ?? []; ?>
                        <?php $debug_logging = isset( $options['debug_logging'] ) && $options['debug_logging']; ?>

                        <div class="fbcn-card">
                            <div class="fbcn-card-header"><div class="fbcn-card-header-inner"><div class="fbcn-card-icon fbcn-card-icon--blue"><span class="dashicons dashicons-calendar-alt"></span></div><div><h2><?php _e( 'Scheduling', 'fb-call-now' ); ?></h2><p><?php _e( 'Control when your call button is visible.', 'fb-call-now' ); ?></p></div></div></div>
                            <div class="fbcn-card-body">
                                <div class="fbcn-pro-field-group">
                                    <div class="fbcn-pro-field-label"><span class="dashicons dashicons-calendar"></span><?php _e( 'Day-of-Week Visibility', 'fb-call-now' ); ?></div>
                                    <div class="fbcn-day-grid">
                                        <?php $days = [ 'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday' ]; ?>
                                        <?php foreach ( $days as $key => $label ) : ?>
                                        <label class="fbcn-day-pill <?php echo in_array( $key, $selected_days ) ? 'active' : ''; ?>">
                                            <input type="checkbox" name="fbcn_pro_settings[days_visible][]" value="<?php echo $key; ?>" <?php echo in_array( $key, $selected_days ) ? 'checked' : ''; ?> class="fbcn-day-checkbox" />
                                            <?php echo esc_html( $label ); ?>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="description"><?php _e( 'Select which days the button should be visible.', 'fb-call-now' ); ?></p>
                                </div>
                                <div class="fbcn-pro-field-group">
                                    <div class="fbcn-pro-field-label"><span class="dashicons dashicons-clock"></span><?php _e( 'Active Time Window', 'fb-call-now' ); ?></div>
                                    <div class="fbcn-time-row">
                                        <div class="fbcn-time-field"><label><?php _e( 'Start', 'fb-call-now' ); ?></label><select name="fbcn_pro_settings[start_time]" id="start_time" class="fbcn-time-select"><?php for ( $hour = 0; $hour < 24; $hour++ ) : $t = sprintf( '%02d:00', $hour ); ?><option value="<?php echo $t; ?>" <?php selected( $start_time, $t ); ?>><?php echo $t; ?></option><?php endfor; ?></select></div>
                                        <div class="fbcn-time-divider"><span class="dashicons dashicons-arrow-right-alt"></span></div>
                                        <div class="fbcn-time-field"><label><?php _e( 'End', 'fb-call-now' ); ?></label><select name="fbcn_pro_settings[end_time]" id="end_time" class="fbcn-time-select"><?php for ( $hour = 0; $hour < 24; $hour++ ) : $t = sprintf( '%02d:00', $hour ); ?><option value="<?php echo $t; ?>" <?php selected( $end_time, $t ); ?>><?php echo $t; ?></option><?php endfor; ?></select></div>
                                    </div>
                                    <label class="fbcn-wrap-toggle"><input type="checkbox" name="fbcn_pro_settings[wrap_to_next_day]" value="1" <?php checked( $wrap_to_next_day ); ?> /><span class="fbcn-toggle-label"><?php _e( 'Wrap to Next Day', 'fb-call-now' ); ?></span></label>
                                    <p class="description"><?php _e( 'Enable if end time should extend past midnight.', 'fb-call-now' ); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="fbcn-card">
                            <div class="fbcn-card-header"><div class="fbcn-card-header-inner"><div class="fbcn-card-icon fbcn-card-icon--purple"><span class="dashicons dashicons-smartphone"></span></div><div><h2><?php _e( 'Device Targeting', 'fb-call-now' ); ?></h2><p><?php _e( 'Choose which devices display your call button.', 'fb-call-now' ); ?></p></div></div></div>
                            <div class="fbcn-card-body">
                                <?php $devices = [ 'desktop' => [ 'label' => 'Desktop', 'sub' => '>= 992px', 'icon' => 'dashicons-desktop' ], 'tablet' => [ 'label' => 'Tablet', 'sub' => '768-991px', 'icon' => 'dashicons-tablet' ], 'mobile' => [ 'label' => 'Mobile', 'sub' => '< 768px', 'icon' => 'dashicons-smartphone' ] ]; ?>
                                <div class="fbcn-device-cards">
                                    <?php foreach ( $devices as $key => $info ) : $is_checked = in_array( $key, $selected_devices ); ?>
                                    <label class="fbcn-device-card <?php echo $is_checked ? 'active' : ''; ?>">
                                        <input type="checkbox" name="fbcn_pro_settings[device_visibility][]" value="<?php echo $key; ?>" <?php echo $is_checked ? 'checked' : ''; ?> class="fbcn-device-checkbox" />
                                        <span class="dashicons <?php echo esc_attr( $info['icon'] ); ?> fbcn-device-card-icon"></span>
                                        <span class="fbcn-device-card-label"><?php echo esc_html( $info['label'] ); ?></span>
                                        <span class="fbcn-device-card-sub"><?php echo esc_html( $info['sub'] ); ?></span>
                                        <span class="fbcn-device-check dashicons dashicons-yes-alt"></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description"><?php _e( 'Select which device types should display the button.', 'fb-call-now' ); ?></p>
                            </div>
                        </div>

                        <div class="fbcn-card">
                            <div class="fbcn-card-header"><div class="fbcn-card-header-inner"><div class="fbcn-card-icon fbcn-card-icon--orange"><span class="dashicons dashicons-admin-tools"></span></div><div><h2><?php _e( 'Advanced', 'fb-call-now' ); ?></h2><p><?php _e( 'Developer tools and troubleshooting.', 'fb-call-now' ); ?></p></div></div></div>
                            <div class="fbcn-card-body">
                                <label class="fbcn-toggle-row">
                                    <div class="fbcn-toggle-row-text"><span class="fbcn-toggle-row-title"><?php _e( 'Debug Logging', 'fb-call-now' ); ?></span><span class="fbcn-toggle-row-desc"><?php _e( 'Log plugin activity and show the Debug Log menu.', 'fb-call-now' ); ?></span></div>
                                    <div class="fbcn-switch-wrap"><input type="checkbox" id="debug_logging" name="fbcn_pro_settings[debug_logging]" value="1" <?php checked( $debug_logging ); ?> class="fbcn-switch-input" /><span class="fbcn-switch"></span></div>
                                </label>
                            </div>
                        </div>

                        <div class="fbcn-save-bar">
                            <?php submit_button( __( 'Save Changes', 'fb-call-now' ), 'primary large fbcn-save-btn', 'submit', false ); ?>
                        </div>
                    </form>
                </div>

                <div class="fbcn-pro-info-column">
                    <div class="fbcn-card fbcn-pro-status-card">
                        <div class="fbcn-card-header"><h2><?php _e( 'Pro Status', 'fb-call-now' ); ?></h2></div>
                        <div class="fbcn-card-body">
                            <div class="fbcn-status-row"><span class="fbcn-status-dot fbcn-status-active"></span><span><?php _e( 'License Active', 'fb-call-now' ); ?></span></div>
                            <div class="fbcn-status-row"><span class="dashicons dashicons-admin-network fbcn-status-icon"></span><span class="fbcn-status-key-display"><?php echo esc_html( substr( $saved_key, 0, 4 ) . '-****-****-' . substr( $saved_key, -4 ) ); ?></span></div>
                        </div>
                    </div>
                    <div class="fbcn-card fbcn-pro-tips-card">
                        <div class="fbcn-card-header"><h2><?php _e( 'Quick Tips', 'fb-call-now' ); ?></h2></div>
                        <div class="fbcn-card-body">
                            <ul class="fbcn-tips-list">
                                <li><span class="dashicons dashicons-calendar-alt"></span><span><?php _e( 'Uncheck days to hide the button on weekends.', 'fb-call-now' ); ?></span></li>
                                <li><span class="dashicons dashicons-clock"></span><span><?php _e( 'Use time windows to show button during business hours.', 'fb-call-now' ); ?></span></li>
                                <li><span class="dashicons dashicons-smartphone"></span><span><?php _e( 'Mobile-only is great for click-to-call campaigns.', 'fb-call-now' ); ?></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

/**
     * User guide page (placeholder - will be handled by UserGuide class)
     */
    public function user_guide_page() {
        // Get the UserGuide instance and call its page method
        $user_guide = new \FBCallNow\Admin\UserGuide();
        $user_guide->user_guide_page();
    }
    
    /**
     * Basic section callback
     */
    public function basic_section_callback() {
        echo '<p>' . __('Configure the basic settings for your floating call button.', 'fb-call-now') . '</p>';
    }
    
    /**
     * Pro section callback
     */
    public function pro_section_callback() {
        echo '<p>' . __('Advanced visibility controls for your call button.', 'fb-call-now') . '</p>';
    }
    
    /**
     * Enable field callback
     */
    public function enable_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $checked = isset($options['enable']) && $options['enable'] ? 'checked' : '';
        ?>
        <input type="checkbox" id="enable" name="fbcn_basic_settings[enable]" value="1" <?php echo $checked; ?> />
        <label for="enable"><?php _e('Display the call button on your website', 'fb-call-now'); ?></label>
        <?php
    }
    
    /**
     * Button text field callback
     */
    public function button_text_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $value = esc_attr($options['button_text'] ?? Defaults::get_basic_settings()['button_text']);
        ?>
        <input type="text" id="button_text" name="fbcn_basic_settings[button_text]" value="<?php echo $value; ?>" class="regular-text" />
        <p class="description"><?php _e('Text displayed on the floating button. Font size: 20px on desktop/tablet, 17px on mobile.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Phone number field callback
     */
    public function phone_number_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $value = esc_attr($options['phone_number'] ?? '');
        $error_class = '';
        
        // Check for validation errors
        $errors = get_settings_errors('fbcn_basic_settings');
        foreach ($errors as $error) {
            if ($error['code'] === 'invalid_phone') {
                $error_class = 'fbcn-field-error';
                break;
            }
        }
        ?>
        <input type="text" id="phone_number" name="fbcn_basic_settings[phone_number]" value="<?php echo $value; ?>" class="regular-text <?php echo $error_class; ?>" placeholder="+1-XXX-XXX-XXXX" />
        <?php if ($error_class): ?>
            <span class="fbcn-error-icon" title="<?php _e('Invalid phone number format', 'fb-call-now'); ?>">❗</span>
        <?php endif; ?>
        <p class="description"><?php _e('Phone number in +1-XXX-XXX-XXXX format. This will be dialed when the button is clicked.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Button color field callback
     */
    public function button_color_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $value = esc_attr($options['button_color'] ?? '#007cba');
        ?>
        <input type="text" id="button_color" name="fbcn_basic_settings[button_color]" value="<?php echo $value; ?>" class="fbcn-color-picker" />
        <p class="description"><?php _e('Background color of the call button.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Text color field callback
     */
    public function text_color_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $value = esc_attr($options['text_color'] ?? '#ffffff');
        ?>
        <input type="text" id="text_color" name="fbcn_basic_settings[text_color]" value="<?php echo $value; ?>" class="fbcn-color-picker" />
        <p class="description"><?php _e('Text color of the button label.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Horizontal position field callback
     */
    public function horizontal_position_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $value = $options['horizontal_position'] ?? 'right';
        ?>
        <select name="fbcn_basic_settings[horizontal_position]">
            <option value="left" <?php selected($value, 'left'); ?>><?php _e('Left', 'fb-call-now'); ?></option>
            <option value="right" <?php selected($value, 'right'); ?>><?php _e('Right', 'fb-call-now'); ?></option>
        </select>
        <p class="description"><?php _e('Which side of the screen to position the button (10px padding from edge).', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Vertical position field callback
     */
    public function vertical_position_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $value = absint($options['vertical_position'] ?? 10);
        ?>
        <select name="fbcn_basic_settings[vertical_position]" id="vertical_position">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <option value="<?php echo $i; ?>" <?php selected($value, $i); ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>
        <p class="description"><?php _e('Vertical position from top of screen. 1 = top, 10 = bottom.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Delete data field callback
     */
    public function delete_data_field_callback() {
        $options = get_option('fbcn_basic_settings', Defaults::get_basic_settings());
        $checked = isset($options['delete_data_on_uninstall']) && $options['delete_data_on_uninstall'] ? 'checked' : '';
        ?>
        <input type="checkbox" id="delete_data_on_uninstall" name="fbcn_basic_settings[delete_data_on_uninstall]" value="1" <?php echo $checked; ?> />
        <label for="delete_data_on_uninstall"><?php _e('Delete all plugin data when uninstalled', 'fb-call-now'); ?></label>
        <p class="description"><?php _e('Check this box to delete all settings when the plugin is uninstalled.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Days visible field callback
     */
    public function days_visible_field_callback() {
        $options = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        $selected_days = $options['days_visible'] ?? array();
        
        $days = array(
            'monday' => __('Monday', 'fb-call-now'),
            'tuesday' => __('Tuesday', 'fb-call-now'),
            'wednesday' => __('Wednesday', 'fb-call-now'),
            'thursday' => __('Thursday', 'fb-call-now'),
            'friday' => __('Friday', 'fb-call-now'),
            'saturday' => __('Saturday', 'fb-call-now'),
            'sunday' => __('Sunday', 'fb-call-now')
        );
        
        foreach ($days as $key => $label) {
            $checked = in_array($key, $selected_days) ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="fbcn_pro_settings[days_visible][]" value="<?php echo $key; ?>" <?php echo $checked; ?> />
                <?php echo $label; ?>
            </label><br />
            <?php
        }
        ?>
        <p class="description"><?php _e('Select which days of the week the button should be visible.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Time window field callback
     */
    public function time_window_field_callback() {
        $options = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        $start_time = $options['start_time'] ?? '00:00';
        $end_time = $options['end_time'] ?? '23:00';
        $wrap_to_next_day = isset($options['wrap_to_next_day']) && $options['wrap_to_next_day'];
        
        // Generate time options
        $time_options = array();
        for ($hour = 0; $hour < 24; $hour++) {
            $time_key = sprintf('%02d:00', $hour);
            $time_options[$time_key] = $time_key;
        }
        ?>
        <table class="form-table">
            <tr>
                <td>
                    <label for="start_time"><?php _e('Start Time:', 'fb-call-now'); ?></label>
                    <select name="fbcn_pro_settings[start_time]" id="start_time">
                        <?php foreach ($time_options as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php selected($start_time, $key); ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="end_time"><?php _e('End Time:', 'fb-call-now'); ?></label>
                    <select name="fbcn_pro_settings[end_time]" id="end_time">
                        <?php foreach ($time_options as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php selected($end_time, $key); ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        
        <label>
            <input type="checkbox" name="fbcn_pro_settings[wrap_to_next_day]" value="1" <?php checked($wrap_to_next_day); ?> />
            <?php _e('Wrap to Next Day', 'fb-call-now'); ?>
        </label>
        <p class="description"><?php _e('Time window when the button should be visible. Enable "Wrap to Next Day" if your end time should extend past midnight.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Device visibility field callback
     */
    public function device_visibility_field_callback() {
        $options = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        $selected_devices = $options['device_visibility'] ?? array();
        
        $devices = array(
            'desktop' => __('Desktop (≥992px)', 'fb-call-now'),
            'tablet' => __('Tablet (768px-991px)', 'fb-call-now'),
            'mobile' => __('Mobile (<768px)', 'fb-call-now')
        );
        
        foreach ($devices as $key => $label) {
            $checked = in_array($key, $selected_devices) ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="fbcn_pro_settings[device_visibility][]" value="<?php echo $key; ?>" <?php echo $checked; ?> />
                <?php echo $label; ?>
            </label><br />
            <?php
        }
        ?>
        <p class="description"><?php _e('Select which device types should display the button based on screen width.', 'fb-call-now'); ?></p>
        <?php
    }
    
    /**
     * Debug logging field callback
     */
    public function debug_logging_field_callback() {
        $options = get_option('fbcn_pro_settings', Defaults::get_pro_settings());
        $checked = isset($options['debug_logging']) && $options['debug_logging'] ? 'checked' : '';
        ?>
        <input type="checkbox" id="debug_logging" name="fbcn_pro_settings[debug_logging]" value="1" <?php echo $checked; ?> />
        <label for="debug_logging"><?php _e('Enable debug logging and show Debug Log menu', 'fb-call-now'); ?></label>
        <p class="description"><?php _e('When enabled, plugin activity will be logged and a Debug Log page will appear in the menu for troubleshooting.', 'fb-call-now'); ?></p>
        <?php
    }
}