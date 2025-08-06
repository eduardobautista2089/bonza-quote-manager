<?php
namespace BonzaQuote\PostTypes;

/**
 * Class QuotePostType
 * 
 * Registers the custom post type used to store submitted quotes.
 * This post type is not publicly visible but is used internally for storing quote data.
 */
class QuotePostType {
    /**
     * Hooks the post type registration to the 'init' action.
     */
    public function register() {
        add_action( 'init', [ $this, 'register_post_type' ] );
    }

    /**
     * Registers the 'bonza_quote' custom post type.
     * Hidden from the frontend and admin UI, But supports title and editor for internal data storage.
     */
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