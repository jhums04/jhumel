<?php
/**
 * Storage for submissions.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

use RuntimeException;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The plugin's own table, read and written only here; caching would hide status changes.

/**
 * Reads and writes the {prefix}quote_form_submissions table. Every query is prepared.
 */
final class SubmissionRepository {

	/**
	 * Saves a new submission as pending. The payload is stored exactly as it will be sent to n8n.
	 *
	 * @param array{external_id: string, organization_slug: string} $payload The webhook body.
	 * @return int The new row's id.
	 *
	 * @throws RuntimeException When the database refuses the row, so the visitor is not told it was saved.
	 */
	public function create_pending( array $payload ): int {
		global $wpdb;

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

		return (int) $wpdb->insert_id;
	}

	/**
	 * Loads one submission.
	 *
	 * @param int $id Row id.
	 *
	 * @throws RuntimeException When no row has that id.
	 */
	public function find( int $id ): Submission {
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %i WHERE id = %d', $this->table_name(), $id ) );

		if ( null === $row ) {
			throw new RuntimeException( 'No submission with id ' . absint( $id ) );
		}

		return Submission::from_row( $row );
	}

	/**
	 * Pending submissions due another attempt, oldest first.
	 *
	 * Rows younger than a minute are skipped so the retry job never races the immediate attempt.
	 *
	 * @param int $max_attempts Rows with this many attempts are no longer retried.
	 * @param int $limit        Most rows to return.
	 * @return list<Submission>
	 */
	public function find_due_for_retry( int $max_attempts, int $limit ): array {
		global $wpdb;

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM %i WHERE status = 'pending' AND attempts < %d AND created_at <= %s ORDER BY created_at ASC LIMIT %d",
				$this->table_name(),
				$max_attempts,
				gmdate( 'Y-m-d H:i:s', time() - MINUTE_IN_SECONDS ),
				$limit
			)
		);

		return array_map( array( Submission::class, 'from_row' ), $rows );
	}

	/**
	 * Records a successful delivery.
	 *
	 * @param int $id Row id.
	 */
	public function mark_sent( int $id ): void {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE %i SET status = 'sent', attempts = attempts + 1, last_error = NULL, sent_at = %s WHERE id = %d",
				$this->table_name(),
				gmdate( 'Y-m-d H:i:s' ),
				$id
			)
		);
	}

	/**
	 * Records a failed delivery attempt. The row stays pending for the next retry.
	 *
	 * @param int    $id    Row id.
	 * @param string $error Why the attempt failed.
	 */
	public function record_failed_attempt( int $id, string $error ): void {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				'UPDATE %i SET attempts = attempts + 1, last_error = %s WHERE id = %d',
				$this->table_name(),
				$error,
				$id
			)
		);
	}

	/**
	 * Stops retrying a submission.
	 *
	 * @param int $id Row id.
	 */
	public function mark_failed( int $id ): void {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "UPDATE %i SET status = 'failed' WHERE id = %d", $this->table_name(), $id ) );
	}

	/**
	 * Full table name with the site's prefix.
	 */
	private function table_name(): string {
		global $wpdb;

		return $wpdb->prefix . 'quote_form_submissions';
	}
}
