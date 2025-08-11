<?php
use PHPUnit\Framework\TestCase;
use BonzaQuote\Plugin;

class BasicTest extends WP_UnitTestCase {
    public function test_plugin_can_run_and_register_post_type() {
        $plugin = new Plugin();
        $plugin->run();

        do_action( 'init' );

        $this->assertTrue( post_type_exists( 'bonza_quote' ), 'Cstom post type bonza_quote is registered' );
    }
}
