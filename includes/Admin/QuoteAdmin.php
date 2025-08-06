<?php
namespace BonzaQuote\Admin;

class QuoteAdmin {
    public function init() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
    }

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

    public function render_quotes_page() {
        echo '<h1>Bonza Quotes</h1>';
        echo '<p>Coming soon: Table of submitted quotes with appoval/rejection.</p>';
    }
}