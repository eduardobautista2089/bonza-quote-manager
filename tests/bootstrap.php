<?php
$_tests_dir = getenv('WP_TESTS_DIR') ? getenv('WP_TESTS_DIR') : '/tmp/wordpress-tests-lib';

// Load wordpress test functions
require_once $_tests_dir . '/includes/functions.php';

// Tell WP test suite to load our plugin
function _manually_load_plugin() {
    require dirname(__DIR__) . '/bonza-quote-management.php';
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Load the WP tests environment
require $_tests_dir . '/includes/bootstrap.php';