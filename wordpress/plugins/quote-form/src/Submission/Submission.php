<?php
/**
 * One stored submission.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

/**
 * A row of {prefix}quote_form_submissions, read-only once loaded.
 */
final class Submission {

	/**
	 * Creates the submission.
	 *
	 * @param int     $id                Row id.
	 * @param string  $external_id       UUID sent to n8n.
	 * @param string  $organization_slug Organization the form belongs to.
	 * @param string  $payload           The exact JSON body sent to n8n.
	 * @param int     $attempts          Delivery attempts made so far.
	 * @param ?string $last_error        Why the last attempt failed, if it did.
	 * @param string  $created_at        UTC time the row was saved.
	 */
	public function __construct(
		public readonly int $id,
		public readonly string $external_id,
		public readonly string $organization_slug,
		public readonly string $payload,
		public readonly int $attempts,
		public readonly ?string $last_error,
		public readonly string $created_at
	) {
	}

	/**
	 * Builds a submission from a $wpdb result row, where every column arrives as a string.
	 *
	 * @param object $row Database row.
	 */
	public static function from_row( object $row ): self {
		return new self(
			(int) $row->id,
			$row->external_id,
			$row->organization_slug,
			$row->payload,
			(int) $row->attempts,
			$row->last_error,
			$row->created_at
		);
	}
}
