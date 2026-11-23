<?php
/**
 * Validation for the settings form.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Settings;

/**
 * Cleans submitted settings. An invalid field keeps its previous value and adds an admin notice.
 */
final class SettingsSanitizer {

	private const MINIMUM_SECRET_LENGTH = 32;

	/**
	 * Creates the sanitizer.
	 *
	 * @param Settings $settings Saved settings, used as the fallback for invalid fields.
	 */
	public function __construct( private Settings $settings ) {
	}

	/**
	 * Sanitize callback for register_setting().
	 *
	 * @param mixed $input Submitted form values.
	 * @return array<string, mixed>
	 */
	public function sanitize( mixed $input ): array {
		$current = $this->settings->all();

		if ( ! is_array( $input ) ) {
			return $current;
		}

		return array(
			'webhook_url'          => $this->sanitize_webhook_url( (string) ( $input['webhook_url'] ?? '' ), $current['webhook_url'] ),
			'webhook_secret'       => $this->sanitize_webhook_secret( (string) ( $input['webhook_secret'] ?? '' ), $current['webhook_secret'] ),
			'turnstile_site_key'   => sanitize_text_field( (string) ( $input['turnstile_site_key'] ?? '' ) ),
			'turnstile_secret_key' => $this->keep_secret_when_blank( sanitize_text_field( (string) ( $input['turnstile_secret_key'] ?? '' ) ), $current['turnstile_secret_key'] ),
			'services'             => $this->sanitize_services( $input['services'] ?? array(), $current['services'] ),
		);
	}

	/**
	 * Accepts an empty value or an http(s) URL.
	 *
	 * @param string $url         Submitted URL.
	 * @param string $current_url Saved URL.
	 */
	private function sanitize_webhook_url( string $url, string $current_url ): string {
		$url = trim( $url );

		if ( '' === $url ) {
			return '';
		}

		$clean_url = esc_url_raw( $url, array( 'http', 'https' ) );

		if ( '' === $clean_url || false === filter_var( $clean_url, FILTER_VALIDATE_URL ) ) {
			$this->add_error( 'webhook_url', __( 'Webhook URL must start with http:// or https://. The previous URL was kept.', 'quote-form' ) );
			return $current_url;
		}

		return $clean_url;
	}

	/**
	 * Requires a long secret of printable characters without spaces.
	 *
	 * @param string $secret         Submitted secret.
	 * @param string $current_secret Saved secret.
	 */
	private function sanitize_webhook_secret( string $secret, string $current_secret ): string {
		$secret = trim( $secret );

		if ( '' === $secret ) {
			return $current_secret;
		}

		if ( strlen( $secret ) < self::MINIMUM_SECRET_LENGTH || 1 !== preg_match( '/^[\x21-\x7E]+$/', $secret ) ) {
			$this->add_error(
				'webhook_secret',
				sprintf(
					/* translators: %d: minimum number of characters. */
					__( 'Webhook secret must be at least %d characters with no spaces. The previous secret was kept.', 'quote-form' ),
					self::MINIMUM_SECRET_LENGTH
				)
			);
			return $current_secret;
		}

		return $secret;
	}

	/**
	 * Secret fields are rendered empty, so an empty submission means "unchanged".
	 *
	 * @param string $secret         Submitted secret.
	 * @param string $current_secret Saved secret.
	 */
	private function keep_secret_when_blank( string $secret, string $current_secret ): string {
		return '' === $secret ? $current_secret : $secret;
	}

	/**
	 * Validates the whole services list. Any invalid row keeps the previous list, so prices never half-update.
	 *
	 * @param mixed                                               $rows             Submitted rows.
	 * @param list<array{key: string, label: string, price: int}> $current_services Saved services.
	 * @return list<array{key: string, label: string, price: int}>
	 */
	private function sanitize_services( mixed $rows, array $current_services ): array {
		if ( ! is_array( $rows ) ) {
			return $current_services;
		}

		$services = array();

		foreach ( $rows as $row ) {
			$key   = sanitize_key( (string) ( $row['key'] ?? '' ) );
			$label = sanitize_text_field( (string) ( $row['label'] ?? '' ) );
			$price = trim( (string) ( $row['price'] ?? '' ) );

			if ( '' === $key && '' === $label && '' === $price ) {
				continue;
			}

			if ( '' === $key || '' === $label ) {
				$this->add_error( 'services', __( 'Every service needs a key and a label. The previous services were kept.', 'quote-form' ) );
				return $current_services;
			}

			if ( ! ctype_digit( $price ) ) {
				$this->add_error( 'services', __( 'Prices must be whole numbers of US dollars, 0 or more. The previous services were kept.', 'quote-form' ) );
				return $current_services;
			}

			if ( in_array( $key, array_column( $services, 'key' ), true ) ) {
				/* translators: %s: the duplicated service key. */
				$this->add_error( 'services', sprintf( __( 'The service key "%s" is used twice. The previous services were kept.', 'quote-form' ), $key ) );
				return $current_services;
			}

			$services[] = array(
				'key'   => $key,
				'label' => $label,
				'price' => (int) $price,
			);
		}

		return $services;
	}

	/**
	 * Shows an error notice above the settings form.
	 *
	 * @param string $field   Field the error belongs to.
	 * @param string $message Message for the admin.
	 */
	private function add_error( string $field, string $message ): void {
		add_settings_error( Settings::OPTION_NAME, 'quote_form_' . $field, $message );
	}
}
