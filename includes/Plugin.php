<?php
namespace BonzaQuote;

use BonzaQuote\Admin\QuoteAdmin;
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
    }
}