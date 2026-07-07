<?php
/**
 * Plugin Name: FB Call Now Pro
 * Description: Unlocks advanced visibility and styling features for FB Call Now. Requires FB Call Now (Free) to be installed and active.
 * Version: 1.0.0
 * Author: thepluginfactory.com
 * Author URI: https://thepluginfactory.com
 * Text Domain: fb-call-now-pro
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.8
 * Tested up to: 6.3
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('FBCN_PRO_VERSION', '1.0.0');
define('FBCN_PRO_PLUGIN_FILE', __FILE__);
define('FBCN_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FBCN_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FBCN_PRO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main FB Call Now Pro class
 */
class FB_Call_Now_Pro {

    /**
     * Single instance of the plugin
     */
    private static $instance = null;

    /**
     * Minimum required Free version
     */
    const MIN_FREE_VERSION = '4.1.77';

    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize the plugin
     */
    private function init() {
        // Check dependencies very early (before plugins_loaded to define constant)
        add_action('plugins_loaded', array($this, 'check_dependencies'), 1);

        // Activation hook
        register_activation_hook(FBCN_PRO_PLUGIN_FILE, array($this, 'activate'));
    }

    /**
     * Check if Free version is installed and active
     */
    public function check_dependencies() {
        // Check if Free version is active
        if (!defined('FBCN_VERSION')) {
            add_action('admin_notices', array($this, 'missing_free_notice'));
            return;
        }

        // Check minimum version
        if (version_compare(FBCN_VERSION, self::MIN_FREE_VERSION, '<')) {
            add_action('admin_notices', array($this, 'outdated_free_notice'));
            return;
        }

        // Dependencies met - activate Pro features
        $this->activate_pro_features();
    }

    /**
     * Activate Pro features by defining the constant
     */
    private function activate_pro_features() {
        // Define the constant that unlocks Pro features in the Free version
        if (!defined('FBCN_PRO_ACTIVE')) {
            define('FBCN_PRO_ACTIVE', true);
        }

        // Load license handler
        require_once FBCN_PRO_PLUGIN_DIR . 'includes/Licensing/License.php';
        new FBCallNowPro\Licensing\License();

        // Load Admin Settings (New UI)
        require_once FBCN_PRO_PLUGIN_DIR . 'includes/Admin/ProSettings.php';
        new FBCallNowPro\Admin\ProSettings();
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Nothing special needed - Pro settings defaults are already in Free version
    }

    /**
     * Admin notice when Free version is missing
     */
    public function missing_free_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php _e('FB Call Now Pro', 'fb-call-now-pro'); ?>:</strong>
                <?php _e('This plugin requires FB Call Now (Free) to be installed and activated.', 'fb-call-now-pro'); ?>
                <a href="<?php echo admin_url('plugin-install.php?s=fb+call+now&tab=search&type=term'); ?>">
                    <?php _e('Install FB Call Now', 'fb-call-now-pro'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Admin notice when Free version is outdated
     */
    public function outdated_free_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php _e('FB Call Now Pro', 'fb-call-now-pro'); ?>:</strong>
                <?php printf(
                    __('This plugin requires FB Call Now version %s or higher. Please update the free version.', 'fb-call-now-pro'),
                    self::MIN_FREE_VERSION
                ); ?>
            </p>
        </div>
        <?php
    }
}

// Initialize the plugin
FB_Call_Now_Pro::get_instance();
