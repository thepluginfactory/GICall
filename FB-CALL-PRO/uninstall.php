<?php
/**
 * FB Call Now Pro Uninstall
 *
 * Handles cleanup when the plugin is uninstalled
 * Note: Pro settings are stored in the Free version's option (fbcn_pro_settings)
 * and are managed by the Free version's uninstall process.
 *
 * @package FBCallNowPro
 * @since 1.0.0
 */

// Exit if not called by WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Pro plugin doesn't store its own options - all settings are in Free version
// The Free version handles cleanup of fbcn_pro_settings based on delete_data_on_uninstall setting
