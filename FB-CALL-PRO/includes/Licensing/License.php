<?php

namespace FBCallNowPro\Licensing;

/**
 * License Handler
 *
 * Handles license key validation
 *
 * @package FBCallNowPro\Licensing
 * @since 1.0.0
 */
class License {

    // Generated API Keys (Format must be XXXX-XXXX-XXXX-XXXX due to frontend JS validation)
    private $valid_keys = array(
        'FBCN-PROX-1234-5678',
        'ABCD-EFGH-IJKL-MNOP',
        '1234-5678-90AB-CDEF'
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'validate_license_submission'));
        add_action('admin_notices', array($this, 'display_debug_notice'));
    }

    /**
     * Intercept the Free plugin's license submission to validate and save it.
     */
    public function validate_license_submission() {
        if (isset($_POST['fbcn_api_key'])) {
            $new_key = strtoupper(trim(sanitize_text_field($_POST['fbcn_api_key'])));
            set_transient('fbcn_last_post_key', $new_key, 60);
            
            if (isset($_POST['fbcn_api_key_nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['fbcn_api_key_nonce_field'])), 'fbcn_api_key_nonce')) {
                set_transient('fbcn_last_nonce_status', 'valid', 60);
                
                if (!empty($new_key)) {
                    if (in_array($new_key, $this->valid_keys)) {
                        update_option('fbcn_pro_api_key', $new_key);
                        set_transient('fbcn_last_activation_status', 'success', 60);
                    } else {
                        delete_option('fbcn_pro_api_key');
                        set_transient('fbcn_last_activation_status', 'invalid_mismatch', 60);
                    }
                }
            } else {
                set_transient('fbcn_last_nonce_status', 'invalid_or_missing', 60);
            }
        }
    }

    /**
     * Display debug information for troubleshooting
     */
    public function display_debug_notice() {
        if (get_transient('fbcn_last_post_key') !== false) {
            $key = get_transient('fbcn_last_post_key');
            $nonce = get_transient('fbcn_last_nonce_status');
            $status = get_transient('fbcn_last_activation_status');
            
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>FB Call Now Pro Debug Info:</strong><br>';
            echo 'Submitted Key: <code>' . esc_html($key) . '</code><br>';
            echo 'Nonce Status: <code>' . esc_html($nonce) . '</code><br>';
            echo 'Activation Status: <code>' . esc_html($status) . '</code><br>';
            echo 'Expected format example: <code>' . esc_html($this->valid_keys[0]) . '</code></p>';
            echo '</div>';
            
            delete_transient('fbcn_last_post_key');
            delete_transient('fbcn_last_nonce_status');
            delete_transient('fbcn_last_activation_status');
        }
    }

    /**
     * Check if license is valid
     *
     * @return bool
     */
    public function is_valid() {
        $key = get_option('fbcn_pro_api_key');
        return in_array($key, $this->valid_keys);
    }

    /**
     * Get license status
     *
     * @return string License status
     */
    public function get_status() {
        return $this->is_valid() ? 'active' : 'inactive';
    }
}

