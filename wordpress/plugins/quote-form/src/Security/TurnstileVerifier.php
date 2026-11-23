<?php
/**
 * Cloudflare Turnstile verification.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Security;

use QuoteForm\Settings\Settings;

/**
 * Asks Cloudflare whether a Turnstile token is genuine. Fails closed: no secret, no token, or no answer means no pass.
 */
final class TurnstileVerifier {

	private const VERIFY_URL      = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
	private const TIMEOUT_SECONDS = 5;

	/**
	 * Creates the verifier.
	 *
	 * @param Settings $settings Saved settings, for the Turnstile secret key.
	 */
	public function __construct( private Settings $settings ) {
	}

	/**
	 * Verifies a token. Each token can be verified only once.
	 *
	 * @param string $token      The cf-turnstile-response value from the form.
	 * @param string $ip_address Visitor IP address, which Cloudflare uses as an extra signal.
	 */
	public function verify( string $token, string $ip_address ): TurnstileResult {
		$secret_key = $this->settings->all()['turnstile_secret_key'];

		if ( '' === $secret_key || '' === $token ) {
			return TurnstileResult::Failed;
		}

		$response = wp_remote_post(
			self::VERIFY_URL,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'body'    => array(
					'secret'   => $secret_key,
					'response' => $token,
					'remoteip' => $ip_address,
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return TurnstileResult::Unavailable;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		return is_array( $body ) && true === ( $body['success'] ?? false )
			? TurnstileResult::Passed
			: TurnstileResult::Failed;
	}
}
