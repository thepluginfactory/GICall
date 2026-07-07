<?php

namespace FBCallNowPro\Admin;

/**
 * Pro Admin Settings (Overrides Free version's Pro page)
 * 
 * @package FBCallNowPro\Admin
 * @since 1.0.0
 */
class ProSettings {

    /**
     * Constructor
     */
    public function __construct() {
        // High priority to ensure we run after Free version (which usually runs at default 10)
        add_action('admin_menu', array($this, 'replace_admin_menu'), 999);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Replace the Pro settings page registered by the Free version
     */
    public function replace_admin_menu() {
        // Free plugin now handles the Pro Settings page directly.
        // Nothing to replace - let the free plugin's page render.
    }

    

    /**
     * Enqueue assets
     */
    public function enqueue_assets($hook) {
        // Only load on our plugin page
        if ($hook !== 'fb-call-now_page_fbcn_pro_settings') {
            return;
        }

        wp_enqueue_style(
            'fbcn-pro-admin-style',
            FBCN_PRO_PLUGIN_URL . 'assets/css/admin-pro.css',
            array(),
            FBCN_PRO_VERSION
        );

        wp_enqueue_script(
            'fbcn-pro-admin-script',
            FBCN_PRO_PLUGIN_URL . 'assets/js/admin-pro.js',
            array('jquery'),
            FBCN_PRO_VERSION,
            true
        );
        
        // Pass server time to JS for status calculation
        wp_localize_script('fbcn-pro-admin-script', 'fbcnProData', array(
            'serverTime' => current_time('H:i'),
            'currentDay' => strtolower(date('l'))
        ));
    }

    /**
     * Render the settings page
     */
    public function render_page() {
        // Output suppressed: the free plugin (FB Call Now) now renders the Pro Settings page.
        // This method intentionally left blank.
    }
}
