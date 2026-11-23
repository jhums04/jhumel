<?php
/**
 * Outcome of a Turnstile check.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Security;

/**
 * Unavailable is separate from Failed so the visitor is told to retry rather than that they look like a bot.
 */
enum TurnstileResult {
	case Passed;
	case Failed;
	case Unavailable;
}
