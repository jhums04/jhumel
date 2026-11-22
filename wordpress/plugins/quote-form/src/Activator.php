<?php
/**
 * Runs once when the plugin is activated.
 *
 * @package QuoteForm
 */

namespace QuoteForm;

/**
 * Creates the submissions table.
 */
final class Activator {

	/**
	 * Creates or updates the submissions table.
	 *
	 * Times are stored in UTC. dbDelta needs the exact formatting below,
	 * including two spaces after PRIMARY KEY.
	 */
	public static function activate(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . 'quote_form_submissions';
		$charset_collate = $wpdb->get_charset_collate();

		dbDelta(
			"CREATE TABLE {$table_name} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				external_id char(36) NOT NULL,
				organization_slug varchar(64) NOT NULL,
				payload longtext NOT NULL,
				status varchar(16) NOT NULL DEFAULT 'pending',
				attempts tinyint(3) unsigned NOT NULL DEFAULT 0,
				last_error text NULL,
				created_at datetime NOT NULL,
				sent_at datetime NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY external_id (external_id),
				KEY status_created_at (status, created_at)
			) {$charset_collate};"
		);
	}
}
