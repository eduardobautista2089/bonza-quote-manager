<?php
namespace BonzaQuote;

use BonzaQuote\Admin\QuoteAdmin;
use BonzaQuote\Admin\QuoteListTable;
use BonzaQuote\Frontend\QuoteForm;
use BonzaQuote\PostTypes\QuotePostType;

/**
 * Class Plugin
 * 
 * Core plugin class that bootstraps all components of the Bonza Quote plugin.
 * Initializes custom post types, frontend forms, and admin UI hooks.
 */
class Plugin {
    /**
     * Runs the plugin by registering post types, frontend hooks, and admin actions.
     */
    public function run() {
        ( new QuotePostType() )->register();
        ( new QuoteForm() )->init();
        ( new QuoteAdmin() )->init();
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }

    public function enqueue_admin_scripts ( $hook ) {
        $screen = get_current_screen();

        if ( ! $screen ) {
            return;
        }

        if (
            $screen->base === 'post' &&
            $screen->post_type === 'bonza_quote'
        ) {
            wp_enqueue_script(
                'bonza-quote-admin',
                BONZA_QUOTE_PLUGIN_URL . 'assets/js/admin/bonza-quote-admin.js',
                [ 'jquery' ],
                true
            );
        }
    }
}