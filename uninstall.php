<?php
/**
 * Uninstall Bonza Quote Plugin
 * 
 * Deletes all plugin data: custom posts, metadata, and options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete all bonza_quote posts and their meta
$qoutes = get_posts( [
    'post_type'     => 'bonza_quote',
    'post_status'   => 'any',
    'numberposts'   => -1,
    'fields'        => 'ids',
] );

if ( $quotes ) {
    foreach ( $quotes as $quote_id ) {
        wp_delete_post( $quote_id, true );
    }
}

delete_option( 'bonza_quote_settings' );

delete_site_option( 'bonza+_quote_settings' );