<?php
class SampleTest extends WP_UnitTestCase {
    public function test_constants_are_defined() {
        $this->assertTrue( defined( 'BONZA_QUOTE_PLUGIN_PATH' ) );
    }

    public function test_plugin_class_exists() {
        $this->assertTrue( class_exists( \BonzaQuote\Plugin::class ) );
    }
}