<?php
/**
 * Retries for undelivered submissions.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

/**
 * Every 5 minutes, resends pending submissions. After the 5th failed attempt a submission
 * is marked failed and the site admin gets one email listing every lead that failed in that run.
 */
final class RetryScheduler {

	public const HOOK = 'quote_form_retry_pending';

	private const SCHEDULE         = 'quote_form_every_five_minutes';
	private const INTERVAL_SECONDS = 300;
	private const MAX_ATTEMPTS     = 5;
	private const BATCH_SIZE       = 20;

	/**
	 * Creates the scheduler.
	 *
	 * @param SubmissionRepository $repository Storage.
	 * @param WebhookDispatcher    $dispatcher Delivery.
	 */
	public function __construct(
		private SubmissionRepository $repository,
		private WebhookDispatcher $dispatcher
	) {
	}

	/**
	 * Starts the recurring job. Called on activation.
	 */
	public static function schedule(): void {
		if ( false === wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time(), self::SCHEDULE, self::HOOK );
		}
	}

	/**
	 * Stops the recurring job. Called on deactivation.
	 */
	public static function unschedule(): void {
		wp_clear_scheduled_hook( self::HOOK );
	}

	/**
	 * Adds the 5-minute interval, which WordPress does not have built in.
	 *
	 * @param array<string, array{interval: int, display: string}> $schedules Registered intervals.
	 * @return array<string, array{interval: int, display: string}>
	 */
	public function add_schedule( array $schedules ): array {
		$schedules[ self::SCHEDULE ] = array(
			'interval' => self::INTERVAL_SECONDS,
			'display'  => __( 'Every 5 minutes', 'quote-form' ),
		);

		return $schedules;
	}

	/**
	 * The cron job: one more attempt for each due submission.
	 */
	public function retry_pending(): void {
		$failed_submissions = array();

		foreach ( $this->repository->find_due_for_retry( self::MAX_ATTEMPTS, self::BATCH_SIZE ) as $submission ) {
			if ( $this->dispatcher->deliver( $submission ) ) {
				continue;
			}

			if ( $submission->attempts + 1 >= self::MAX_ATTEMPTS ) {
				$this->repository->mark_failed( $submission->id );
				$failed_submissions[] = $this->repository->find( $submission->id );
			}
		}

		if ( array() !== $failed_submissions ) {
			$this->notify_admin( $failed_submissions );
		}
	}

	/**
	 * Emails the site admin the details needed to follow up by hand.
	 *
	 * @param Submission[] $failed_submissions Submissions that just used their last attempt.
	 */
	private function notify_admin( array $failed_submissions ): void {
		$subject = sprintf(
			/* translators: 1: site name, 2: number of leads. */
			_n( '[%1$s] %2$d quote request could not be delivered to n8n', '[%1$s] %2$d quote requests could not be delivered to n8n', count( $failed_submissions ), 'quote-form' ),
			get_bloginfo( 'name' ),
			count( $failed_submissions )
		);

		$lines = array(
			sprintf(
				/* translators: %d: number of delivery attempts. */
				__( 'These leads failed %d delivery attempts. They are saved in WordPress, so nothing is lost, but they are not in the dashboard. Follow up with them directly.', 'quote-form' ),
				self::MAX_ATTEMPTS
			),
			'',
		);

		foreach ( $failed_submissions as $submission ) {
			$payload = json_decode( $submission->payload, true );

			$lines[] = sprintf( '%s <%s>', $payload['full_name'], $payload['email'] );
			$lines[] = sprintf( 'Organization: %s', $submission->organization_slug );
			$lines[] = sprintf( 'Submitted: %s UTC', $submission->created_at );
			$lines[] = sprintf( 'Last error: %s', $submission->last_error );
			$lines[] = sprintf( 'Submission id: %s', $submission->external_id );
			$lines[] = '';
		}

		wp_mail( get_option( 'admin_email' ), $subject, implode( "\n", $lines ) );
	}
}
