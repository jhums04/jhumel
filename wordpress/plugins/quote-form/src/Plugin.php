<?php
/**
 * Wires the plugin into WordPress.
 *
 * @package QuoteForm
 */

namespace QuoteForm;

use QuoteForm\Settings\Settings;
use QuoteForm\Settings\SettingsPage;
use QuoteForm\Settings\SettingsSanitizer;

/**
 * Builds the plugin's objects and registers every hook in one place.
 */
final class Plugin {

	/**
	 * Registers all hooks.
	 */
	public function register(): void {
		$settings      = new Settings();
		$settings_page = new SettingsPage( $settings, new SettingsSanitizer( $settings ) );

		add_action( 'admin_menu', array( $settings_page, 'add_menu_page' ) );
		add_action( 'admin_init', array( $settings_page, 'register_settings' ) );
	}
}
