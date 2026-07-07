<?php
/**
 * Uninstall script for FB Call Now plugin
 * 
 * This file is executed when the plugin is uninstalled via WordPress admin.
 * It handles cleanup of all plugin data if the user has opted for data deletion.
 * 
 * @package FBCallNow
 * @since 3.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Clean up plugin data
 */
function fbcn_cleanup_plugin_data() {
    // Get basic settings to check if user wants data deleted
    $basic_settings = get_option('fbcn_basic_settings', array());
    
    // Only proceed if user has opted for data deletion
    if (empty($basic_settings['delete_data_on_uninstall'])) {
        return; // User wants to keep settings
    }
    
    // Include logger for final log entry
    $plugin_dir = plugin_dir_path(__FILE__);
    if (file_exists($plugin_dir . 'vendor/autoload.php')) {
        require_once $plugin_dir . 'vendor/autoload.php';
        
        // Log the uninstall action
        if (class_exists('FBCallNow\Core\Logger')) {
            FBCallNow\Core\Logger::info('Plugin uninstalled - cleaning up all data');
        }
    }
    
    // List of all plugin options to remove
    $options_to_remove = array(
        'fbcn_basic_settings',
        'fbcn_pro_settings',
        // Add any additional options that might be created in future versions
    );
    
    // Remove each option
    foreach ($options_to_remove as $option_name) {
        delete_option($option_name);
    }
    
    // Remove any transients that might have been set
    delete_transient('fbcn_visibility_cache');
    delete_transient('fbcn_settings_cache');
    
    // Clean up any user meta that might have been set
    delete_metadata('user', 0, 'fbcn_user_preference', '', true);
    
    // Remove log file if it exists
    $upload_dir = wp_upload_dir();
    $log_file = trailingslashit($upload_dir['basedir']) . 'fb-call-now.log';
    
    if (file_exists($log_file)) {
        unlink($log_file);
    }
    
    // Clear any cached data
    wp_cache_delete('fbcn_basic_settings', 'options');
    wp_cache_delete('fbcn_pro_settings', 'options');
    
    // Force database cleanup
    global $wpdb;
    
    // Remove any custom database entries (if any were created)
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            'fbcn_%'
        )
    );
    
    // Clean up any scheduled events
    wp_clear_scheduled_hook('fbcn_daily_cleanup');
    wp_clear_scheduled_hook('fbcn_weekly_maintenance');
}

/**
 * Multisite cleanup
 */
function fbcn_cleanup_multisite_data() {
    if (!is_multisite()) {
        return;
    }
    
    global $wpdb;
    
    // Get all blog IDs
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        fbcn_cleanup_plugin_data();
        restore_current_blog();
    }
    
    // Clean up any network-wide options
    delete_site_option('fbcn_network_settings');
}

// Execute cleanup
if (is_multisite()) {
    fbcn_cleanup_multisite_data();
} else {
    fbcn_cleanup_plugin_data();
}

// Final cleanup - remove any remaining traces
wp_cache_flush();