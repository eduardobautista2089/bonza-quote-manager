<?php
// Path to the WordPress core test library
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

// Start up the WP testing environment
require $_tests_dir . '/includes/bootstrap.php';

require_once dirname(__DIR__) . '/bonza-quote-management.php';

BonzaQuote\Autoloader::register();