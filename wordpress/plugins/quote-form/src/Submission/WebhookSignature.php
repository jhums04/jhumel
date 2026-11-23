<?php
/**
 * Signature for the lead webhook.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

/**
 * HMAC-SHA256 over "timestamp.body", as n8n's "Verify signature" node expects.
 *
 * Pure PHP with no WordPress calls, so it can be unit-tested against other implementations.
 */
final class WebhookSignature {

	/**
	 * The X-Lead-Signature header value.
	 *
	 * @param string $secret    The shared webhook secret.
	 * @param int    $timestamp Unix time sent in X-Lead-Timestamp; signing it stops old requests being replayed.
	 * @param string $body      The exact request body that will be sent.
	 */
	public static function create( string $secret, int $timestamp, string $body ): string {
		return 'sha256=' . hash_hmac( 'sha256', $timestamp . '.' . $body, $secret );
	}
}
