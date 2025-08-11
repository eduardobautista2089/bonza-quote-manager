<?php
namespace BonzaQuote;

use BonzaQuote\Admin\QuoteAdmin;
use BonzaQuote\Frontend\QuoteForm;
use BonzaQuote\PostTypes\QuotePostType;

/**
 * Class Plugin
 *
 * Core plugin bootstrapper.
 * Registers post types, initializes frontend and admin components,
 * and enqueues admin-specific scripts.
 */
class Plugin {

    /**
     * Runs the plugin by registering post types, frontend hooks, and admin actions.
     *
     * @return void
     */
    public function run() {
        ( new QuotePostType() )->register();
        ( new QuoteForm() )->init();
        ( new QuoteAdmin() )->init();

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Enqueue public-facing styles and scripts.
     * 
     * @return void
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'bonza-quote-public',
            BONZA_QUOTE_PLUGIN_URL . 'assets/css/public/bonza-quote-public.css',
            [],
            BONZA_QUOTE_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'bonza-quote-public',
            BONZA_QUOTE_PLUGIN_URL . 'assets/js/public/bonza-quote-public.js',
            [ 'jquery' ],
            BONZA_QUOTE_PLUGIN_VERSION,
            true
        );

        // Pass flash message if needed
        if ( isset( $_GET['bq_submitted'] ) && $_GET['bq_submitted'] === '1' ) {
            wp_add_inline_script(
                'bonza-quote-public',
                'document.addEventListener("DOMContentLoaded", function() { BonzaQuoteFlash("Thank you! your quote has been submitted."); });'
            );
        }
    }

    /**
     * Enqueues admin scripts/styles for the Bonza Quote admin screens.
     *
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_admin_assets( $hook ) {
        $screen = get_current_screen();

        if ( ! $screen || $screen->base !== 'post' || $screen->post_type !== 'bonza_quote' ) {
            return;
        }

        wp_enqueue_style(
            'bonza-quote-admin',
            BONZA_QUOTE_PLUGIN_URL . 'assets/css/admin/bonza-quote-admin.css',
            [],
            BONZA_QUOTE_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'bonza-quote-admin',
            BONZA_QUOTE_PLUGIN_URL . 'assets/js/admin/bonza-quote-admin.js',
            [ 'jquery' ],
            BONZA_QUOTE_VERSION,
            true
        );
    }
}
