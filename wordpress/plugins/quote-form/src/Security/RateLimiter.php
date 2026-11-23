<?php
/**
 * Per-visitor submission limit.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Security;

/**
 * Allows 5 accepted submissions per IP address per hour, counted in a transient.
 */
final class RateLimiter {

	private const MAX_SUBMISSIONS = 5;
	private const WINDOW_SECONDS  = 3600;

	/**
	 * Whether this IP address has used up its submissions for the current hour.
	 *
	 * @param string $ip_address Visitor IP address.
	 */
	public function is_limited( string $ip_address ): bool {
		return $this->current_window( $ip_address )['count'] >= self::MAX_SUBMISSIONS;
	}

	/**
	 * Counts one accepted submission. Rejected requests are not counted, so fixing a typo costs nothing.
	 *
	 * @param string $ip_address Visitor IP address.
	 */
	public function record( string $ip_address ): void {
		$window = $this->current_window( $ip_address );
		++$window['count'];

		$seconds_left = $window['started_at'] + self::WINDOW_SECONDS - time();
		set_transient( $this->transient_name( $ip_address ), $window, max( 1, $seconds_left ) );
	}

	/**
	 * The stored window, or a fresh one when none exists or it has ended.
	 *
	 * @param string $ip_address Visitor IP address.
	 * @return array{started_at: int, count: int}
	 */
	private function current_window( string $ip_address ): array {
		$window = get_transient( $this->transient_name( $ip_address ) );

		if ( ! is_array( $window ) || $window['started_at'] + self::WINDOW_SECONDS <= time() ) {
			return array(
				'started_at' => time(),
				'count'      => 0,
			);
		}

		return $window;
	}

	/**
	 * Transient name built from a hash, so IP addresses are never stored in plain text.
	 *
	 * @param string $ip_address Visitor IP address.
	 */
	private function transient_name( string $ip_address ): string {
		return 'quote_form_rate_' . substr( wp_hash( $ip_address ), 0, 32 );
	}
}
