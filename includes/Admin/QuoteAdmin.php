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
        add_filter( 'set-screen-option', function( $status, $option, $value ) {
            return ( $option === 'quotes_per_page' ) ? $value : $status;
        }, 10, 3 );
    }

    /**
     * Adds a new admin menu item for managing Bonza quotes.
     */
    public function add_menu() {
    $hook = add_menu_page(
        'Bonza Quotes',
        'Bonza Quotes',
        'manage_options',
        'bonza-quotes',
        [ $this, 'render_quotes_page' ],
        'dashicons-feedback',
        26
    );

        add_action( "load-$hook", [ $this, 'screen_option' ] );

        add_action( "load-$hook", function() {
            $list_table = new QuoteListTable();
            $list_table->process_bulk_action();
        } );
    }

    public function screen_option() {
        add_screen_option(
            'per_page',
            [
                'label'   => 'Quotes per page',
                'default' => 10,
                'option'  => 'quotes_per_page',
            ]
        );
    }

    /**
     * Renders the admin page content for quote management.
     */
    public function render_quotes_page() {
       $list_table = new QuoteListTable();
       $list_table->prepare_items();
       ?>

        <div class="wrap">
            <h1 class="wp-heading-inline">Bonza Quotes</h1>
                
            <form method="post" action="">

                <?php $list_table->views(); ?>
                <input type="hidden" name="page" value="bonza-quotes" />
                <?php wp_nonce_field( 'bulk-quotes' ); ?>
                <?php 
                    $list_table->search_box( 'Search Quotes', 'search_quotes' );
                    $list_table->display(); 
                ?>
            </form>
        </div>

       <?php
    }
}