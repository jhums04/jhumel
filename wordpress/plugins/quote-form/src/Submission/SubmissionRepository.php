<?php
/**
 * Storage for submissions.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

use RuntimeException;

/**
 * Reads and writes the {prefix}quote_form_submissions table.
 */
final class SubmissionRepository {

	/**
	 * Saves a new submission as pending. The payload is stored exactly as it will be sent to n8n.
	 *
	 * @param array{external_id: string, organization_slug: string} $payload The webhook body.
	 *
	 * @throws RuntimeException When the database refuses the row, so the visitor is not told it was saved.
	 */
	public function create_pending( array $payload ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- The plugin's own table; insert() prepares the values.
		$inserted = $wpdb->insert(
			$this->table_name(),
			array(
				'external_id'       => $payload['external_id'],
				'organization_slug' => $payload['organization_slug'],
				'payload'           => wp_json_encode( $payload ),
				'status'            => 'pending',
				'attempts'          => 0,
				'created_at'        => gmdate( 'Y-m-d H:i:s' ),
			),
			array( '%s', '%s', '%s', '%s', '%d', '%s' )
		);

		if ( false === $inserted ) {
			throw new RuntimeException( 'Could not save the submission: ' . esc_html( $wpdb->last_error ) );
		}
	}

	/**
	 * Full table name with the site's prefix.
	 */
	private function table_name(): string {
		global $wpdb;

		return $wpdb->prefix . 'quote_form_submissions';
	}
}
