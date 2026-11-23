<?php
/**
 * Price estimate for a quote request.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Form;

use InvalidArgumentException;

/**
 * Estimate = sum of the selected services' base prices x the timeline multiplier.
 *
 * Pure PHP with no WordPress calls, so the server-side price can be unit-tested.
 */
final class PriceEstimator {

	/**
	 * Timeline multipliers as percentages, so the math stays in whole numbers.
	 */
	public const TIMELINE_PERCENTAGES = array(
		'standard' => 100,
		'rush'     => 125,
	);

	/**
	 * Creates the estimator.
	 *
	 * @param array<string, int> $base_prices Base price in whole US dollars, keyed by service key.
	 */
	public function __construct( private array $base_prices ) {
	}

	/**
	 * The estimated price in US dollars, with at most two decimals.
	 *
	 * Error messages leave out the submitted value, so visitor input is never echoed back.
	 *
	 * @param string[] $service_keys Selected services. A repeated key counts once.
	 * @param string   $timeline     A key of TIMELINE_PERCENTAGES.
	 *
	 * @throws InvalidArgumentException When a service or the timeline is unknown.
	 */
	public function estimate( array $service_keys, string $timeline ): float {
		if ( ! array_key_exists( $timeline, self::TIMELINE_PERCENTAGES ) ) {
			throw new InvalidArgumentException( 'Unknown timeline.' );
		}

		$total_dollars = 0;

		foreach ( array_unique( $service_keys ) as $service_key ) {
			if ( ! array_key_exists( $service_key, $this->base_prices ) ) {
				throw new InvalidArgumentException( 'Unknown service.' );
			}

			$total_dollars += $this->base_prices[ $service_key ];
		}

		$total_cents = $total_dollars * self::TIMELINE_PERCENTAGES[ $timeline ];

		return $total_cents / 100;
	}
}
