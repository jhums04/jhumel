<?php
/**
 * Removes the plugin's data when it is deleted from the Plugins screen.
 *
 * @package QuoteForm
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Dropping the plugin's own table on uninstall.
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'quote_form_submissions' ) );

delete_option( 'quote_form_settings' );

wp_clear_scheduled_hook( 'quote_form_retry_pending' );
