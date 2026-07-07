<?php
/**
 * Plugin Name: FB Call Now
 * Description: Renders a floating circular "Call Now" button on the front end, which dials a user-configured telephone number when clicked. Includes both Basic and Pro visibility controls.
 * Version: 4.1.77
 * Author: thepluginfactory.com
 * Author URI: https://thepluginfactory.com
 * Text Domain: fb-call-now
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.8
 * Tested up to: 6.3
 * Requires PHP:
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('FBCN_VERSION', '4.1.77');
define('FBCN_PLUGIN_FILE', __FILE__);
define('FBCN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FBCN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FBCN_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
require_once FBCN_PLUGIN_DIR . 'vendor/autoload.php';

use FBCallNow\Core\Logger;
use FBCallNow\Core\Defaults;
use FBCallNow\Admin\Settings;
use FBCallNow\Frontend\ButtonRenderer;
use FBCallNow\Admin\UserGuide;
use FBCallNow\Admin\DebugLog;

/**
 * Main plugin class
 */
class FB_Call_Now
{

    /**
     * Single instance of the plugin
     */
    private static $instance = null;

    /**
     * Get plugin instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the plugin
     */
    private function init()
    {
        // Hook into WordPress
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init_components'));

        // Activation and deactivation hooks
        register_activation_hook(FBCN_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(FBCN_PLUGIN_FILE, array($this, 'deactivate'));

        // Plugin action links
        add_filter('plugin_action_links_' . FBCN_PLUGIN_BASENAME, array($this, 'plugin_action_links'));
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain()
    {
        load_plugin_textdomain(
            'fb-call-now',
            false,
            dirname(FBCN_PLUGIN_BASENAME) . '/languages/'
        );
    }

    /**
     * Initialize plugin components
     */
    public function init_components()
    {
        // Initialize admin components
        if (is_admin()) {
            new Settings();
            new UserGuide();
            new DebugLog();
        }

        // Initialize frontend components
        if (!is_admin()) {
            new ButtonRenderer();
        }
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
        Logger::info('Plugin activated');

        // Set default options if they don't exist
        $this->set_default_options();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate()
    {
        Logger::info('Plugin deactivated');

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Set default plugin options
     */
    private function set_default_options()
    {
        // Get defaults from centralized class
        $basic_defaults = Defaults::get_basic_settings();
        $pro_defaults = Defaults::get_pro_settings();

        // Set defaults only if options don't exist
        if (!get_option('fbcn_basic_settings')) {
            add_option('fbcn_basic_settings', $basic_defaults);
        }

        if (!get_option('fbcn_pro_settings')) {
            add_option('fbcn_pro_settings', $pro_defaults);
        }

        Logger::info('Default options set: ' . json_encode(array(
            'basic' => $basic_defaults,
            'pro' => $pro_defaults
        )));
    }

    /**
     * Add plugin action links
     */
    public function plugin_action_links($links)
    {
        $settings_links = array(
            '<a href="' . admin_url('admin.php?page=fbcn_basic_settings') . '">' . __('Basic Settings', 'fb-call-now') . '</a>'
        );

        if (defined('FBCN_PRO_ACTIVE') && FBCN_PRO_ACTIVE) {
            $settings_links[] = '<a href="' . admin_url('admin.php?page=fbcn_pro_settings') . '">' . __('Pro Settings', 'fb-call-now') . '</a>';
        }

        return array_merge($settings_links, $links);
    }
}

// Initialize the plugin
FB_Call_Now::get_instance();