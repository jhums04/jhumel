<?php
/**
 * The [quote_form] shortcode.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Form;

use QuoteForm\Settings\Settings;

/**
 * Renders the quote form and loads its assets only on pages that use it.
 */
final class FormShortcode {

	public const TAG = 'quote_form';

	private const ASSET_HANDLE     = 'quote-form';
	private const TURNSTILE_HANDLE = 'quote-form-turnstile';
	private const TURNSTILE_SCRIPT = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

	/**
	 * Creates the shortcode.
	 *
	 * @param Settings $settings    Saved settings: services, prices, Turnstile site key.
	 * @param string   $plugin_file Path to quote-form.php, used to build asset URLs.
	 */
	public function __construct(
		private Settings $settings,
		private string $plugin_file
	) {
	}

	/**
	 * Registers the stylesheet and scripts so render() can enqueue them on demand.
	 */
	public function register_assets(): void {
		wp_register_style(
			self::ASSET_HANDLE,
			plugins_url( 'assets/css/quote-form.css', $this->plugin_file ),
			array(),
			$this->asset_version( 'assets/css/quote-form.css' )
		);

		wp_register_script(
			self::ASSET_HANDLE,
			plugins_url( 'assets/js/quote-form.js', $this->plugin_file ),
			array(),
			$this->asset_version( 'assets/js/quote-form.js' ),
			array( 'strategy' => 'defer' )
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Cloudflare versions this script itself.
		wp_register_script( self::TURNSTILE_HANDLE, self::TURNSTILE_SCRIPT, array(), null, array( 'strategy' => 'defer' ) );
	}

	/**
	 * Renders [quote_form organization="demo" require_message="yes" success_message="..."].
	 *
	 * @param array<string, string>|string $attributes Shortcode attributes; WordPress passes '' when there are none.
	 */
	public function render( array|string $attributes ): string {
		$attributes = shortcode_atts(
			array(
				'organization'    => '',
				'require_message' => 'no',
				'success_message' => __( 'Thanks, I have your request. I read every one myself and will reply by email.', 'quote-form' ),
			),
			$attributes,
			self::TAG
		);

		$organization = sanitize_key( $attributes['organization'] );

		if ( '' === $organization ) {
			return current_user_can( 'manage_options' )
				? '<p>' . esc_html__( 'Quote form: add an organization to the shortcode, for example [quote_form organization="demo"].', 'quote-form' ) . '</p>'
				: '';
		}

		$settings = $this->settings->all();

		wp_enqueue_style( self::ASSET_HANDLE );
		wp_enqueue_script( self::ASSET_HANDLE );
		if ( '' !== $settings['turnstile_site_key'] ) {
			wp_enqueue_script( self::TURNSTILE_HANDLE );
		}

		$form = array(
			'id'                  => 'quote-form-' . $organization,
			'organization'        => $organization,
			'nonce'               => wp_create_nonce( 'quote_form_submit_' . $organization ),
			'services'            => $settings['services'],
			'timelines'           => $this->timeline_labels(),
			'is_message_required' => 'yes' === $attributes['require_message'],
			'success_message'     => $attributes['success_message'],
			'turnstile_site_key'  => $settings['turnstile_site_key'],
			'config'              => array(
				'endpoint'            => rest_url( 'quote-form/v1/submissions' ),
				'prices'              => array_column( $settings['services'], 'price', 'key' ),
				'timelinePercentages' => PriceEstimator::TIMELINE_PERCENTAGES,
				'messages'            => $this->script_messages(),
			),
		);

		ob_start();
		require dirname( $this->plugin_file ) . '/templates/form.php';
		return (string) ob_get_clean();
	}

	/**
	 * Visitor-facing names for each timeline in PriceEstimator.
	 *
	 * @return array<string, string>
	 */
	private function timeline_labels(): array {
		$rush_extra_percent = PriceEstimator::TIMELINE_PERCENTAGES['rush'] - PriceEstimator::TIMELINE_PERCENTAGES['standard'];

		return array(
			'standard' => __( 'Standard', 'quote-form' ),
			/* translators: %d: extra cost of a rush timeline, in percent. */
			'rush'     => sprintf( __( 'Rush (%d%% extra)', 'quote-form' ), $rush_extra_percent ),
		);
	}

	/**
	 * Text the script shows, kept here so it can be translated like the rest of the form.
	 *
	 * @return array<string, string>
	 */
	private function script_messages(): array {
		return array(
			/* translators: %s: formatted price, such as $2,400. */
			'estimate'         => __( 'Starting at %s', 'quote-form' ),
			'estimateEmpty'    => __( 'Select a service to see a starting price.', 'quote-form' ),
			'servicesRequired' => __( 'Choose at least one service.', 'quote-form' ),
			'nameRequired'     => __( 'Enter your name.', 'quote-form' ),
			'emailInvalid'     => __( 'Enter an email address like name@example.com.', 'quote-form' ),
			'messageRequired'  => __( 'Tell me a little about what you need.', 'quote-form' ),
			'turnstilePending' => __( 'The spam check is still running. Wait a moment, then send again.', 'quote-form' ),
			'sending'          => __( 'Sending...', 'quote-form' ),
			'sendFailed'       => __( 'Your request did not go through. Your details are still here, so try again in a moment.', 'quote-form' ),
		);
	}

	/**
	 * Cache-busting version from the file's last change.
	 *
	 * @param string $relative_path Path inside the plugin folder.
	 */
	private function asset_version( string $relative_path ): string {
		return (string) filemtime( dirname( $this->plugin_file ) . '/' . $relative_path );
	}
}
