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

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }

    /**
     * Enqueues admin scripts for the Bonza Quote post type edit screen.
     *
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_admin_scripts( $hook ) {
        $screen = get_current_screen();

        if ( ! $screen || $screen->base !== 'post' || $screen->post_type !== 'bonza_quote' ) {
            return;
        }

        wp_enqueue_script(
            'bonza-quote-admin',
            BONZA_QUOTE_PLUGIN_URL . 'assets/js/admin/bonza-quote-admin.js',
            [ 'jquery' ],
            BONZA_QUOTE_VERSION ?? false, // Use version constant if available
            true
        );
    }
}
