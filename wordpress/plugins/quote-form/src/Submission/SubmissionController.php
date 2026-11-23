<?php
/**
 * REST endpoint that receives quote form submissions.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

use QuoteForm\Form\PriceEstimator;
use QuoteForm\Security\RateLimiter;
use QuoteForm\Security\TurnstileResult;
use QuoteForm\Security\TurnstileVerifier;
use QuoteForm\Settings\Settings;
use WP_REST_Request;
use WP_REST_Response;

/**
 * POST /wp-json/quote-form/v1/submissions
 *
 * Checks run cheapest first: rate limit, nonce, fields, then Turnstile, because a Turnstile token can be used only once.
 */
final class SubmissionController {

	private const ROUTE_NAMESPACE = 'quote-form/v1';
	private const ROUTE           = '/submissions';

	/**
	 * Creates the controller.
	 *
	 * @param Settings             $settings     Saved settings, for service prices.
	 * @param SubmissionValidator  $validator    Field validation.
	 * @param TurnstileVerifier    $turnstile    Bot check.
	 * @param RateLimiter          $rate_limiter Per-visitor limit.
	 * @param SubmissionRepository $repository   Storage.
	 * @param WebhookDispatcher    $dispatcher   Delivery to n8n.
	 */
	public function __construct(
		private Settings $settings,
		private SubmissionValidator $validator,
		private TurnstileVerifier $turnstile,
		private RateLimiter $rate_limiter,
		private SubmissionRepository $repository,
		private WebhookDispatcher $dispatcher
	) {
	}

	/**
	 * Registers the route.
	 */
	public function register_routes(): void {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle' ),
				// A public form: the nonce, Turnstile, and rate limit protect it instead of a login.
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Validates, prices (quote form only), and saves one submission.
	 *
	 * @param WP_REST_Request $request The request.
	 */
	public function handle( WP_REST_Request $request ): WP_REST_Response {
		$ip_address = $this->visitor_ip_address();

		if ( $this->rate_limiter->is_limited( $ip_address ) ) {
			return $this->message_response( 429, __( 'You have sent several requests in the last hour. Try again later, or reach me by email.', 'quote-form' ) );
		}

		$input        = (array) $request->get_json_params();
		$organization = sanitize_key( is_string( $input['organization'] ?? null ) ? $input['organization'] : '' );
		$nonce        = is_string( $input['nonce'] ?? null ) ? $input['nonce'] : '';

		// The nonce was created for this organization, so editing the organization in the browser breaks it.
		if ( '' === $organization || ! wp_verify_nonce( $nonce, 'quote_form_submit_' . $organization ) ) {
			return $this->message_response( 403, __( 'This form has expired. Reload the page and send it again.', 'quote-form' ) );
		}

		$validation = $this->validator->validate( $input );
		if ( array() !== $validation['errors'] ) {
			return new WP_REST_Response( array( 'errors' => $validation['errors'] ), 422 );
		}

		$token  = is_string( $input['turnstile_token'] ?? null ) ? $input['turnstile_token'] : '';
		$result = $this->turnstile->verify( $token, $ip_address );
		if ( TurnstileResult::Unavailable === $result ) {
			return $this->message_response( 503, __( 'The spam check is not responding. Wait a moment and send again.', 'quote-form' ) );
		}
		if ( TurnstileResult::Failed === $result ) {
			return new WP_REST_Response(
				array( 'errors' => array( 'turnstile_token' => __( 'The spam check did not pass. Complete it again, then send.', 'quote-form' ) ) ),
				422
			);
		}

		$values    = $validation['values'];
		$estimator = new PriceEstimator( array_column( $this->settings->all()['services'], 'price', 'key' ) );

		$submission_id = $this->repository->create_pending(
			array(
				'external_id'       => wp_generate_uuid4(),
				'organization_slug' => $organization,
				'full_name'         => $values['full_name'],
				'email'             => $values['email'],
				'phone'             => $values['phone'],
				'company'           => $values['company'],
				'services'          => $values['services'],
				'timeline'          => $values['timeline'],
				'message'           => $values['message'],
				// The brief has no price list; its budget range does that job.
				'estimated_price'   => $values['is_brief'] ? null : $estimator->estimate( $values['services'], $values['timeline'] ),
				'budget'            => $values['budget'],
				'submitted_at'      => gmdate( 'c' ),
			)
		);
		$this->rate_limiter->record( $ip_address );

		// First delivery attempt right away. Success or not, the lead is saved, so the visitor sees success;
		// a failed attempt is retried by RetryScheduler.
		$this->dispatcher->deliver( $this->repository->find( $submission_id ) );

		return new WP_REST_Response( array( 'status' => 'received' ), 201 );
	}

	/**
	 * The connecting IP address. Behind a reverse proxy, wp-config.php must set REMOTE_ADDR from the trusted header.
	 */
	private function visitor_ip_address(): string {
		$remote_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		return false === filter_var( $remote_address, FILTER_VALIDATE_IP ) ? '' : $remote_address;
	}

	/**
	 * A response with one message for the form's status line.
	 *
	 * @param int    $status  HTTP status code.
	 * @param string $message Message for the visitor.
	 */
	private function message_response( int $status, string $message ): WP_REST_Response {
		return new WP_REST_Response( array( 'message' => $message ), $status );
	}
}
