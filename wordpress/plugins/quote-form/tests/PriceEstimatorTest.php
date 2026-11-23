<?php
/**
 * Tests for the price estimate.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use QuoteForm\Form\PriceEstimator;

/**
 * Checks PriceEstimator::estimate() against the pricing rules in ARCHITECTURE.md.
 */
final class PriceEstimatorTest extends TestCase {

	/**
	 * Builds an estimator with a fixed price list, independent of the saved settings.
	 */
	private function make_estimator(): PriceEstimator {
		return new PriceEstimator(
			array(
				'automation'  => 400,
				'lead_system' => 2000,
				'rescue'      => 300,
				'odd_price'   => 301,
				'free_call'   => 0,
			)
		);
	}

	/**
	 * The example from the webhook contract: lead_system + automation = 2400.
	 */
	public function test_sums_base_prices_on_a_standard_timeline(): void {
		$this->assertSame( 2400.0, $this->make_estimator()->estimate( array( 'lead_system', 'automation' ), 'standard' ) );
	}

	/**
	 * Rush adds 25 percent.
	 */
	public function test_applies_25_percent_for_rush(): void {
		$this->assertSame( 2500.0, $this->make_estimator()->estimate( array( 'lead_system' ), 'rush' ) );
	}

	/**
	 * 301 x 1.25 = 376.25, kept exactly rather than rounded.
	 */
	public function test_keeps_cents_when_rush_leaves_a_fraction(): void {
		$this->assertSame( 376.25, $this->make_estimator()->estimate( array( 'odd_price' ), 'rush' ) );
	}

	/**
	 * One service costs its base price.
	 */
	public function test_prices_a_single_service(): void {
		$this->assertSame( 300.0, $this->make_estimator()->estimate( array( 'rescue' ), 'standard' ) );
	}

	/**
	 * A tampered request cannot double the price by repeating a service.
	 */
	public function test_counts_a_repeated_service_once(): void {
		$this->assertSame( 2000.0, $this->make_estimator()->estimate( array( 'lead_system', 'lead_system' ), 'standard' ) );
	}

	/**
	 * Nothing selected costs nothing. Requiring a service is the form's job.
	 */
	public function test_returns_zero_when_nothing_is_selected(): void {
		$this->assertSame( 0.0, $this->make_estimator()->estimate( array(), 'standard' ) );
	}

	/**
	 * A service priced at 0 adds nothing.
	 */
	public function test_allows_a_free_service(): void {
		$this->assertSame( 400.0, $this->make_estimator()->estimate( array( 'automation', 'free_call' ), 'standard' ) );
	}

	/**
	 * A service key that is not in the price list is a bug, not a price of 0.
	 */
	public function test_rejects_an_unknown_service(): void {
		$this->expectException( InvalidArgumentException::class );

		$this->make_estimator()->estimate( array( 'teleportation' ), 'standard' );
	}

	/**
	 * Only standard and rush exist.
	 */
	public function test_rejects_an_unknown_timeline(): void {
		$this->expectException( InvalidArgumentException::class );

		$this->make_estimator()->estimate( array( 'rescue' ), 'yesterday' );
	}
}
