<?php
/**
 * PHPUnit bootstrap file for FB Call Now plugin tests
 * 
 * @package FBCallNow\Tests
 */

// Define test environment
if (!defined('FBCN_TESTING')) {
    define('FBCN_TESTING', true);
}

// Set up WordPress test environment
$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

// Verify WordPress test environment exists
if (!file_exists($_tests_dir . '/includes/functions.php')) {
    throw new Exception(
        "WordPress test suite not found at {$_tests_dir}. " .
        "Please set WP_TESTS_DIR environment variable or install wordpress-tests-lib."
    );
}

// Give access to tests_add_filter() function
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested
 */
function _manually_load_plugin() {
    $plugin_file = dirname(dirname(__FILE__)) . '/fb-call-now.php';
    if (!file_exists($plugin_file)) {
        throw new Exception("Plugin file not found: {$plugin_file}");
    }
    require $plugin_file;
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment
require $_tests_dir . '/includes/bootstrap.php';

// Load the plugin autoloader
$autoloader = dirname(dirname(__FILE__)) . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    throw new Exception(
        "Composer autoloader not found. Please run 'composer install' first."
    );
}
require_once $autoloader;

// Set up test constants that might be needed
if (!defined('FBCN_VERSION')) {
    define('FBCN_VERSION', '3.0.1');
}

if (!defined('FBCN_PLUGIN_DIR')) {
    define('FBCN_PLUGIN_DIR', dirname(dirname(__FILE__)) . '/');
}

if (!defined('FBCN_PLUGIN_URL')) {
    define('FBCN_PLUGIN_URL', 'http://example.com/wp-content/plugins/fb-call-now/');
}

if (!defined('FBCN_PLUGIN_BASENAME')) {
    define('FBCN_PLUGIN_BASENAME', 'fb-call-now/fb-call-now.php');
}