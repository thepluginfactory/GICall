<?php

namespace FBCallNow\Admin;

use FBCallNow\Core\Logger;

/**
 * Debug Log admin page
 * 
 * @package FBCallNow\Admin
 * @since 3.0.0
 */
class DebugLog {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_debug_log_menu'), 20);
        add_action('admin_post_fbcn_clear_log', array($this, 'handle_clear_log'));
    }
    
    /**
     * Add debug log submenu
     */
    public function add_debug_log_menu() {
        // Only add menu if debug logging is enabled in Pro Settings
        $pro_settings = get_option('fbcn_pro_settings', array());
        if (empty($pro_settings['debug_logging'])) {
            return; // Debug logging is disabled
        }
        
        add_submenu_page(
            'fb-call-now',
            __('Debug Log', 'fb-call-now'),
            __('Debug Log', 'fb-call-now'),
            'manage_options',
            'fbcn_debug_log',
            array($this, 'debug_log_page')
        );
    }
    
    /**
     * Debug log page
     */
    public function debug_log_page() {
        // Handle form submissions
        if (isset($_GET['log_cleared']) && $_GET['log_cleared'] === '1') {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Debug log cleared successfully.', 'fb-call-now') . '</p></div>';
        }
        
        $log_content = Logger::read_log(200);
        $log_file_size = Logger::get_log_file_size();
        $log_file_path = Logger::get_log_file_path();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="fbcn-debug-info">
                <p><strong><?php _e('Log File Location:', 'fb-call-now'); ?></strong> <?php echo esc_html($log_file_path); ?></p>
                <p><strong><?php _e('File Size:', 'fb-call-now'); ?></strong> <?php echo esc_html($log_file_size); ?></p>
                <p><strong><?php _e('Showing:', 'fb-call-now'); ?></strong> <?php _e('Last 200 lines', 'fb-call-now'); ?></p>
            </div>
            
            <div class="fbcn-debug-actions">
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                    <?php wp_nonce_field('fbcn_clear_log', 'fbcn_clear_log_nonce'); ?>
                    <input type="hidden" name="action" value="fbcn_clear_log" />
                    <button type="submit" class="button button-secondary" onclick="return confirm('<?php _e('Are you sure you want to clear the debug log?', 'fb-call-now'); ?>');">
                        <?php _e('Clear Log', 'fb-call-now'); ?>
                    </button>
                </form>
                
                <button type="button" class="button button-secondary" onclick="location.reload();">
                    <?php _e('Refresh', 'fb-call-now'); ?>
                </button>
                
                <button type="button" class="button button-secondary" onclick="fbcnCopyLog();" data-copied-text="<?php _e('Copied!', 'fb-call-now'); ?>">
                    <?php _e('Copy Log', 'fb-call-now'); ?>
                </button>
            </div>
            
            <div class="fbcn-log-container">
                <h3><?php _e('Debug Log Contents', 'fb-call-now'); ?></h3>
                <pre id="fbcn-log-content" class="fbcn-log-content"><?php echo esc_html($log_content); ?></pre>
            </div>
            
            <div class="fbcn-debug-help">
                <h3><?php _e('How to Use This Log', 'fb-call-now'); ?></h3>
                <ul>
                    <li><?php _e('This log shows the last 200 lines of plugin activity.', 'fb-call-now'); ?></li>
                    <li><?php _e('INFO entries show normal plugin operations (activation, settings saves, etc.).', 'fb-call-now'); ?></li>
                    <li><?php _e('ERROR entries indicate problems that need attention.', 'fb-call-now'); ?></li>
                    <li><?php _e('Copy the log contents and share them when reporting issues for faster diagnosis.', 'fb-call-now'); ?></li>
                    <li><?php _e('The log file is automatically created in your WordPress uploads directory.', 'fb-call-now'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handle clear log action
     */
    public function handle_clear_log() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['fbcn_clear_log_nonce'], 'fbcn_clear_log')) {
            wp_die(__('Security check failed.', 'fb-call-now'));
        }
        
        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'fb-call-now'));
        }
        
        // Clear the log
        Logger::clear_log();
        
        // Redirect back with success message
        wp_redirect(admin_url('admin.php?page=fbcn_debug_log&log_cleared=1'));
        exit;
    }
}