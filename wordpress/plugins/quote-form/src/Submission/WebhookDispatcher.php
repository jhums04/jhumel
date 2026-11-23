<?php
/**
 * Delivery to n8n.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

use QuoteForm\Settings\Settings;

/**
 * Signs one submission, posts it to the n8n webhook, and records the outcome.
 */
final class WebhookDispatcher {

	private const TIMEOUT_SECONDS = 5;

	/**
	 * Creates the dispatcher.
	 *
	 * @param Settings             $settings   Saved settings, for the webhook URL and secret.
	 * @param SubmissionRepository $repository Where the outcome is recorded.
	 */
	public function __construct(
		private Settings $settings,
		private SubmissionRepository $repository
	) {
	}

	/**
	 * Attempts delivery once. n8n answers 2xx only after saving the lead, so anything else is a failure.
	 *
	 * @param Submission $submission The submission to send.
	 * @return bool Whether n8n accepted it.
	 */
	public function deliver( Submission $submission ): bool {
		$settings = $this->settings->all();

		if ( '' === $settings['webhook_url'] || '' === $settings['webhook_secret'] ) {
			$this->repository->record_failed_attempt( $submission->id, 'Webhook URL or secret is not set on Settings > Quote Form.' );
			return false;
		}

		// A fresh timestamp on every attempt, so retries pass n8n's five-minute freshness check.
		$timestamp = time();

		$response = wp_remote_post(
			$settings['webhook_url'],
			array(
				'timeout'     => self::TIMEOUT_SECONDS,
				'headers'     => array(
					'Content-Type'     => 'application/json',
					'X-Lead-Timestamp' => (string) $timestamp,
					'X-Lead-Signature' => WebhookSignature::create( $settings['webhook_secret'], $timestamp, $submission->payload ),
				),
				'body'        => $submission->payload,
				'data_format' => 'body',
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->repository->record_failed_attempt( $submission->id, $response->get_error_message() );
			return false;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code < 200 || $status_code > 299 ) {
			$this->repository->record_failed_attempt( $submission->id, 'HTTP ' . $status_code );
			return false;
		}

		$this->repository->mark_sent( $submission->id );
		return true;
	}
}
