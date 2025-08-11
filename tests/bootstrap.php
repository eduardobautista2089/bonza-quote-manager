<?php
// Path to the WordPress core test library
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
    $_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

// Suppress only the known "twentytwentyfive/format" block binding notice in WP 6.5+
tests_add_filter( 'doing_it_wrong_trigger_error', function( $trigger, $function, $message ) {
    if ( strpos( $message, 'twentytwentyfive/format' ) !== false ) {
        return false; // Ignore this specific notice
    }
    return $trigger; // Keep other notices intact
}, 10, 3 );

// Start up the WP testing environment
require $_tests_dir . '/includes/bootstrap.php';

// Load your plugin
require_once dirname( __DIR__ ) . '/bonza-quote-management.php';

BonzaQuote\Autoloader::register();
