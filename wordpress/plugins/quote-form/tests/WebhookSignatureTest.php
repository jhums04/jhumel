<?php
/**
 * Tests for the webhook signature.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Tests;

use PHPUnit\Framework\TestCase;
use QuoteForm\Submission\WebhookSignature;

/**
 * Expected values were computed independently with Python's hmac module,
 * so these tests prove PHP and other HMAC implementations (like n8n's) agree.
 */
final class WebhookSignatureTest extends TestCase {

	private const SECRET    = 'test-secret';
	private const TIMESTAMP = 1795400000;
	private const BODY      = '{"external_id":"6f1c","organization_slug":"demo"}';

	/**
	 * Matches the value Python computes for the same secret, timestamp, and body.
	 */
	public function test_matches_an_independent_hmac_implementation(): void {
		$this->assertSame(
			'sha256=0110b30d9ee7707b534149c3d1d052c600ace517c917aedef2be1125ef37368d',
			WebhookSignature::create( self::SECRET, self::TIMESTAMP, self::BODY )
		);
	}

	/**
	 * A replayed request with a new timestamp needs a new signature.
	 */
	public function test_changes_when_the_timestamp_changes(): void {
		$this->assertSame(
			'sha256=f954f6261c31725b0426618a7c8da860864bd21a06ca208929953ba91bbb648e',
			WebhookSignature::create( self::SECRET, self::TIMESTAMP + 1, self::BODY )
		);
	}

	/**
	 * Editing any part of the body, here the organization, breaks the signature.
	 */
	public function test_changes_when_the_body_changes(): void {
		$this->assertSame(
			'sha256=0c9eacaa01aeb6fa99924a0e21a92700d973af9aa5149ae33ac2ad45aeaacf7e',
			WebhookSignature::create( self::SECRET, self::TIMESTAMP, '{"external_id":"6f1c","organization_slug":"jhumel"}' )
		);
	}

	/**
	 * Without the right secret, nobody can produce a valid signature.
	 */
	public function test_changes_when_the_secret_changes(): void {
		$this->assertSame(
			'sha256=d599ef2f9e88afa7a173a2df855a5d86d0b88f1b77bf791852a6d8d3d2b1051d',
			WebhookSignature::create( 'other-secret', self::TIMESTAMP, self::BODY )
		);
	}
}
