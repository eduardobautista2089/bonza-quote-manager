<?php
use BonzaQuote\Plugin;

class BasicTest extends WP_UnitTestCase {

    public function setUp(): void {
        parent::setUp();

        // Suppress ONLY the "twentytwentyfive/format" block binding warning
        add_filter( 'doing_it_wrong_trigger_error', function( $trigger, $function, $message ) {
            if ( false !== strpos( $message, 'twentytwentyfive/format' ) ) {
                return false; // Do not trigger error
            }
            return $trigger; // Keep default for everything else
        }, 10, 3 );
    }

    public function test_plugin_can_run_and_register_post_type() {
        $plugin = new Plugin();
        $plugin->run();

        do_action( 'init' );

        $this->assertTrue(
            post_type_exists( 'bonza_quote' ),
            'Custom post type bonza_quote is registered'
        );
    }
}
