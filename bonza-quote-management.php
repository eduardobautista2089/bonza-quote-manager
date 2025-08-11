<?php
/*
Plugin Name: Bonza Quote Management
Description: Handles service quote submissions, notifications, and approvals.
Version: 1.0.0
Author: Eduardo
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: bonza-quote
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin constants
 */
define( 'BONZA_QUOTE_PLUGIN_VERSION', '1.0.0' );
define( 'BONZA_QUOTE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BONZA_QUOTE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BONZA_QUOTE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Load the autoloader.
require_once BONZA_QUOTE_PLUGIN_PATH . 'includes/Autoloader.php';
BonzaQuote\Autoloader::register();

// Bootstrap and run the plugin.
( new BonzaQuote\Plugin() )->run();

/**
 * Plugin activation
 */
function bonza_quote_activate() {
    $cpt = new BonzaQuote\PostTypes\QuotePostType();
    $cpt->register_post_type();

    // Flush rewrite rules to register CPT permalinks
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'bonza_quote_activate' );

/**
 * Plugin deactiavation
 */
function bonza_quote_deactivate() {
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'bonza_quote_deactivate' );