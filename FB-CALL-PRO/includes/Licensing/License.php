<?php

namespace FBCallNowPro\Licensing;

/**
 * License Handler (Placeholder)
 *
 * Placeholder class for future license key validation functionality
 *
 * @package FBCallNowPro\Licensing
 * @since 1.0.0
 */
class License {

    /**
     * Constructor
     */
    public function __construct() {
        // Placeholder for future license validation hooks
    }

    /**
     * Check if license is valid
     *
     * @return bool Always returns true for now (all features enabled)
     */
    public function is_valid() {
        return true;
    }

    /**
     * Get license status
     *
     * @return string License status
     */
    public function get_status() {
        return 'active';
    }

    /**
     * Activate license
     *
     * @param string $license_key License key to activate
     * @return bool Whether activation was successful
     */
    public function activate($license_key) {
        // Placeholder - always return true
        return true;
    }

    /**
     * Deactivate license
     *
     * @return bool Whether deactivation was successful
     */
    public function deactivate() {
        // Placeholder - always return true
        return true;
    }
}
