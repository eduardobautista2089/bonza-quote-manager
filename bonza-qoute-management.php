<?php
/*
Plugin Name: Bonza Quote Management Plugin
Description: A custom plugin to handle service quote submissions and approvals.
Version: 1.0.0
Author: Eduardo
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: bonza-quote
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BONZA_QUOTE_PLUGIN_VERSION', '1.0.0' );
define( 'BONZA_QUOTE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BONZA_QUOTE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BONZA_QUOTE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Load the autoloader.
require_once BONZA_QUOTE_PLUGIN_PATH . 'includes/Autoloader.php';
BonzaQuote\Autoloader::register();

// Run the plugin.
$plugin = new BonzaQuote\Plugin();
$plugin->run();