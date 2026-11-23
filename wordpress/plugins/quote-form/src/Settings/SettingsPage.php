<?php
/**
 * The Settings > Quote Form admin page.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Settings;

/**
 * Registers the settings with the Settings API and renders the page.
 */
final class SettingsPage {

	private const PAGE_SLUG      = 'quote-form';
	private const SETTINGS_GROUP = 'quote_form';

	/**
	 * Creates the page.
	 *
	 * @param Settings          $settings  Saved settings.
	 * @param SettingsSanitizer $sanitizer Validation for submitted values.
	 */
	public function __construct(
		private Settings $settings,
		private SettingsSanitizer $sanitizer
	) {
	}

	/**
	 * Adds the page under the Settings menu.
	 */
	public function add_menu_page(): void {
		add_options_page(
			__( 'Quote Form', 'quote-form' ),
			__( 'Quote Form', 'quote-form' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Registers the option, its sections, and its fields.
	 */
	public function register_settings(): void {
		register_setting(
			self::SETTINGS_GROUP,
			Settings::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this->sanitizer, 'sanitize' ),
				'default'           => Settings::defaults(),
			)
		);

		add_settings_section(
			'quote_form_webhook',
			__( 'Webhook', 'quote-form' ),
			array( $this, 'render_webhook_section' ),
			self::PAGE_SLUG
		);
		$this->add_field( 'webhook_url', __( 'Webhook URL', 'quote-form' ), 'render_url_field', 'quote_form_webhook' );
		$this->add_field( 'webhook_secret', __( 'Webhook secret', 'quote-form' ), 'render_secret_field', 'quote_form_webhook' );

		add_settings_section(
			'quote_form_turnstile',
			__( 'Cloudflare Turnstile', 'quote-form' ),
			array( $this, 'render_turnstile_section' ),
			self::PAGE_SLUG
		);
		$this->add_field( 'turnstile_site_key', __( 'Site key', 'quote-form' ), 'render_text_field', 'quote_form_turnstile' );
		$this->add_field( 'turnstile_secret_key', __( 'Secret key', 'quote-form' ), 'render_secret_field', 'quote_form_turnstile' );

		add_settings_section(
			'quote_form_services',
			__( 'Services', 'quote-form' ),
			array( $this, 'render_services_section' ),
			self::PAGE_SLUG
		);
		add_settings_field(
			'quote_form_services',
			__( 'Services and base prices', 'quote-form' ),
			array( $this, 'render_services_field' ),
			self::PAGE_SLUG,
			'quote_form_services'
		);
	}

	/**
	 * Renders the page. Saving goes through options.php, which checks the nonce and capability.
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
				<?php
				settings_fields( self::SETTINGS_GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Explains the webhook section.
	 */
	public function render_webhook_section(): void {
		echo '<p>' . esc_html__( 'Every submission is sent to this n8n webhook, signed with the secret. The secret must match LEAD_WEBHOOK_SECRET in n8n.', 'quote-form' ) . '</p>';
	}

	/**
	 * Explains the Turnstile section.
	 */
	public function render_turnstile_section(): void {
		echo '<p>' . esc_html__( 'Keys from the Cloudflare dashboard. Turnstile blocks bots without a puzzle for real visitors.', 'quote-form' ) . '</p>';
	}

	/**
	 * Explains the services section.
	 */
	public function render_services_section(): void {
		echo '<p>' . esc_html__( 'The services shown on the form, with their starting prices in US dollars. Clear a row to remove a service. Fill the empty row and save to add one.', 'quote-form' ) . '</p>';
	}

	/**
	 * Renders a URL input.
	 *
	 * @param array{key: string} $args Field arguments.
	 */
	public function render_url_field( array $args ): void {
		printf(
			'<input type="url" id="%1$s" name="%2$s" value="%3$s" class="regular-text">',
			esc_attr( $this->field_id( $args['key'] ) ),
			esc_attr( $this->field_name( $args['key'] ) ),
			esc_attr( $this->settings->all()[ $args['key'] ] )
		);
	}

	/**
	 * Renders a plain text input.
	 *
	 * @param array{key: string} $args Field arguments.
	 */
	public function render_text_field( array $args ): void {
		printf(
			'<input type="text" id="%1$s" name="%2$s" value="%3$s" class="regular-text">',
			esc_attr( $this->field_id( $args['key'] ) ),
			esc_attr( $this->field_name( $args['key'] ) ),
			esc_attr( $this->settings->all()[ $args['key'] ] )
		);
	}

	/**
	 * Renders a secret input that is always empty, so the saved value never reaches the page.
	 *
	 * @param array{key: string} $args Field arguments.
	 */
	public function render_secret_field( array $args ): void {
		$is_saved = '' !== $this->settings->all()[ $args['key'] ];

		printf(
			'<input type="password" id="%1$s" name="%2$s" value="" class="regular-text" autocomplete="new-password">',
			esc_attr( $this->field_id( $args['key'] ) ),
			esc_attr( $this->field_name( $args['key'] ) )
		);
		echo '<p class="description">';
		if ( $is_saved ) {
			esc_html_e( 'Saved. Leave blank to keep the current value.', 'quote-form' );
		} else {
			esc_html_e( 'Not set yet.', 'quote-form' );
		}
		echo '</p>';
	}

	/**
	 * Renders the services table: one row per saved service plus one empty row.
	 */
	public function render_services_field(): void {
		$rows   = $this->settings->all()['services'];
		$rows[] = array(
			'key'   => '',
			'label' => '',
			'price' => '',
		);
		?>
		<table class="widefat striped" style="max-width: 640px;">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Key', 'quote-form' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Label', 'quote-form' ); ?></th>
					<th scope="col"><?php esc_html_e( 'From (USD)', 'quote-form' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rows as $index => $row ) : ?>
					<tr>
						<td>
							<input type="text" name="<?php echo esc_attr( $this->service_field_name( $index, 'key' ) ); ?>" value="<?php echo esc_attr( $row['key'] ); ?>" aria-label="<?php esc_attr_e( 'Key', 'quote-form' ); ?>">
						</td>
						<td>
							<input type="text" name="<?php echo esc_attr( $this->service_field_name( $index, 'label' ) ); ?>" value="<?php echo esc_attr( $row['label'] ); ?>" class="regular-text" aria-label="<?php esc_attr_e( 'Label', 'quote-form' ); ?>">
						</td>
						<td>
							<input type="number" min="0" step="1" name="<?php echo esc_attr( $this->service_field_name( $index, 'price' ) ); ?>" value="<?php echo esc_attr( (string) $row['price'] ); ?>" class="small-text" aria-label="<?php esc_attr_e( 'From (USD)', 'quote-form' ); ?>">
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description"><?php esc_html_e( 'Keys use lowercase letters, numbers, and underscores, and are sent to n8n. Labels are shown to visitors.', 'quote-form' ); ?></p>
		<?php
	}

	/**
	 * Adds a single-input field whose label points at the input.
	 *
	 * @param string $key      Setting key.
	 * @param string $title    Field label.
	 * @param string $renderer Method that renders the input.
	 * @param string $section  Section id.
	 */
	private function add_field( string $key, string $title, string $renderer, string $section ): void {
		add_settings_field(
			'quote_form_' . $key,
			$title,
			array( $this, $renderer ),
			self::PAGE_SLUG,
			$section,
			array(
				'key'       => $key,
				'label_for' => $this->field_id( $key ),
			)
		);
	}

	/**
	 * HTML id of a field's input.
	 *
	 * @param string $key Setting key.
	 */
	private function field_id( string $key ): string {
		return 'quote_form_' . $key;
	}

	/**
	 * Input name that posts into the settings array.
	 *
	 * @param string $key Setting key.
	 */
	private function field_name( string $key ): string {
		return Settings::OPTION_NAME . '[' . $key . ']';
	}

	/**
	 * Input name for one cell of the services table.
	 *
	 * @param int    $index  Row number.
	 * @param string $column Column key.
	 */
	private function service_field_name( int $index, string $column ): string {
		return Settings::OPTION_NAME . '[services][' . $index . '][' . $column . ']';
	}
}
