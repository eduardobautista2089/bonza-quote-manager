<?php
// Path to the WordPress test library.
$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

// Load Composer's autoloader first (includes PHPUnit Polyfills).
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Load WordPress test functions.
require_once $_tests_dir . '/includes/functions.php';

// Tell the WP test suite to load our plugin.
function _manually_load_plugin() {
    require dirname( __DIR__ ) . '/bonza-quote-management.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Load the WordPress test environment.
require $_tests_dir . '/includes/bootstrap.php';
