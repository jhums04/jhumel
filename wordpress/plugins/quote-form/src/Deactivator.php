<?php
/**
 * Runs once when the plugin is deactivated.
 *
 * @package QuoteForm
 */

namespace QuoteForm;

use QuoteForm\Submission\RetryScheduler;

/**
 * Stops scheduled work. Data stays, because the plugin may be reactivated.
 */
final class Deactivator {

	/**
	 * Removes the retry job.
	 */
	public static function deactivate(): void {
		RetryScheduler::unschedule();
	}
}
