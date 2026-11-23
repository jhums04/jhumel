<?php
/**
 * The plugin's saved settings.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Settings;

/**
 * Reads the quote_form_settings option and supplies its defaults.
 */
final class Settings {

	public const OPTION_NAME = 'quote_form_settings';

	/**
	 * Values used before anything is saved. The services are placeholders to edit on the settings page.
	 *
	 * @return array{webhook_url: string, webhook_secret: string, turnstile_site_key: string, turnstile_secret_key: string, services: list<array{key: string, label: string, price: int}>}
	 */
	public static function defaults(): array {
		return array(
			'webhook_url'          => '',
			'webhook_secret'       => '',
			'turnstile_site_key'   => '',
			'turnstile_secret_key' => '',
			'services'             => array(
				array(
					'key'   => 'automation',
					'label' => 'n8n automation workflow',
					'price' => 400,
				),
				array(
					'key'   => 'lead_system',
					'label' => 'Full lead-to-CRM system',
					'price' => 2000,
				),
				array(
					'key'   => 'supabase_app',
					'label' => 'React + Supabase web app',
					'price' => 1500,
				),
				array(
					'key'   => 'wordpress_plugin',
					'label' => 'Custom WordPress plugin',
					'price' => 600,
				),
				array(
					'key'   => 'rescue',
					'label' => 'App rescue or security audit',
					'price' => 300,
				),
			),
		);
	}

	/**
	 * All saved settings, with defaults for anything not saved yet.
	 *
	 * @return array{webhook_url: string, webhook_secret: string, turnstile_site_key: string, turnstile_secret_key: string, services: list<array{key: string, label: string, price: int}>}
	 */
	public function all(): array {
		return wp_parse_args( get_option( self::OPTION_NAME, array() ), self::defaults() );
	}
}
