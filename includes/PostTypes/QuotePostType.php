<?php
namespace BonzaQuote\PostTypes;

class QuotePostType {
    public function register() {
        add_action( 'init', [ $this, 'register_post_type' ] );
    }

    public function register_post_type() {
        register_post_type( 'bonza_quote', [
            'labels' => [
                'name' => 'Qoutes',
                'singular_name' => 'Quote',
            ],
            'public' => false,
            'show_ui' => false,
            'supports' => [ 'title', 'editor' ],
        ]);
    }
}