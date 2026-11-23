<?php
/**
 * Wires the plugin into WordPress.
 *
 * @package QuoteForm
 */

namespace QuoteForm;

use QuoteForm\Form\FormShortcode;
use QuoteForm\Security\RateLimiter;
use QuoteForm\Security\TurnstileVerifier;
use QuoteForm\Settings\Settings;
use QuoteForm\Settings\SettingsPage;
use QuoteForm\Settings\SettingsSanitizer;
use QuoteForm\Submission\SubmissionController;
use QuoteForm\Submission\SubmissionRepository;
use QuoteForm\Submission\SubmissionValidator;

/**
 * Builds the plugin's objects and registers every hook in one place.
 */
final class Plugin {

	/**
	 * Creates the plugin.
	 *
	 * @param string $plugin_file Path to quote-form.php.
	 */
	public function __construct( private string $plugin_file ) {
	}

	/**
	 * Registers all hooks.
	 */
	public function register(): void {
		$settings              = new Settings();
		$settings_page         = new SettingsPage( $settings, new SettingsSanitizer( $settings ) );
		$form_shortcode        = new FormShortcode( $settings, $this->plugin_file );
		$submission_controller = new SubmissionController(
			$settings,
			new SubmissionValidator( $settings ),
			new TurnstileVerifier( $settings ),
			new RateLimiter(),
			new SubmissionRepository()
		);

		add_action( 'admin_menu', array( $settings_page, 'add_menu_page' ) );
		add_action( 'admin_init', array( $settings_page, 'register_settings' ) );

		// Registered on init, not wp_enqueue_scripts: block themes render shortcodes before wp_head.
		add_action( 'init', array( $form_shortcode, 'register_assets' ) );
		add_shortcode( FormShortcode::TAG, array( $form_shortcode, 'render' ) );

		add_action( 'rest_api_init', array( $submission_controller, 'register_routes' ) );
	}
}
