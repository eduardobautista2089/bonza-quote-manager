<?php
namespace BonzaQuote\Admin;

/**
 * Class QuoteAdmin
 * 
 * Handles the admin functionality for the Bonza Quote Management plugin.
 * Registers an admin menu and renders the admin page for viewing and managing quotes.
 */

class QuoteAdmin {
    /**
     * Initializes the admin hooks.
     * Registers the admin menu setup action
     */
    public function init() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
    }

    /**
     * Adds a new admin menu item for managing Bonza quotes.
     */
    public function add_menu() {
        add_menu_page(
            'Bonza Quotes',
            'Bonza Quotes',
            'manage_options',
            'bonza-quotes',
            [ $this, 'render_quotes_page' ],
            'dashicons-feedback',
            26
        );
    }

    /**
     * Renders the admin page content for quote management.
     */
    public function render_quotes_page() {
        echo '<h1>Bonza Quotes</h1>';
        echo '<p>Coming soon: Table of submitted quotes with appoval/rejection.</p>';
    }
}