<?php
/**
 * The quote form's HTML. Included by FormShortcode::render(), which supplies $form.
 *
 * @package QuoteForm
 *
 * @var array<string, mixed> $form
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variables are local to FormShortcode::render().

$field_id = static fn( string $field ): string => $form['id'] . '-' . $field;
?>
<div class="quote-form-wrapper">
	<form class="quote-form" id="<?php echo esc_attr( $form['id'] ); ?>" data-config="<?php echo esc_attr( wp_json_encode( $form['config'] ) ); ?>" novalidate>
		<input type="hidden" name="organization" value="<?php echo esc_attr( $form['organization'] ); ?>">
		<input type="hidden" name="nonce" value="<?php echo esc_attr( $form['nonce'] ); ?>">

		<fieldset class="quote-form__group" aria-describedby="<?php echo esc_attr( $field_id( 'services-error' ) ); ?>">
			<legend class="quote-form__label"><?php esc_html_e( 'What can I help with?', 'quote-form' ); ?></legend>
			<div class="quote-form__options">
				<?php foreach ( $form['services'] as $service ) : ?>
					<label class="quote-form__option">
						<input class="quote-form__checkbox" type="checkbox" name="services" value="<?php echo esc_attr( $service['key'] ); ?>">
						<span class="quote-form__option-label"><?php echo esc_html( $service['label'] ); ?></span>
						<span class="quote-form__option-price">
							<?php
							/* translators: %s: starting price of a service, such as 2,000. */
							echo esc_html( sprintf( __( 'from $%s', 'quote-form' ), number_format_i18n( $service['price'] ) ) );
							?>
						</span>
					</label>
				<?php endforeach; ?>
			</div>
			<p class="quote-form__error" id="<?php echo esc_attr( $field_id( 'services-error' ) ); ?>" hidden><span class="quote-form__visually-hidden"><?php esc_html_e( 'Error:', 'quote-form' ); ?></span> <span data-role="error-message"></span></p>
		</fieldset>

		<div class="quote-form__field">
			<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'timeline' ) ); ?>"><?php esc_html_e( 'Timeline', 'quote-form' ); ?></label>
			<select class="quote-form__input" id="<?php echo esc_attr( $field_id( 'timeline' ) ); ?>" name="timeline">
				<?php foreach ( $form['timelines'] as $timeline_key => $timeline_label ) : ?>
					<option value="<?php echo esc_attr( $timeline_key ); ?>"><?php echo esc_html( $timeline_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<p class="quote-form__estimate" data-role="estimate" aria-live="polite"><?php esc_html_e( 'Select a service to see a starting price.', 'quote-form' ); ?></p>

		<div class="quote-form__row">
			<div class="quote-form__field">
				<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'full_name' ) ); ?>"><?php esc_html_e( 'Name', 'quote-form' ); ?></label>
				<input class="quote-form__input" type="text" id="<?php echo esc_attr( $field_id( 'full_name' ) ); ?>" name="full_name" autocomplete="name" aria-describedby="<?php echo esc_attr( $field_id( 'full_name-error' ) ); ?>">
				<p class="quote-form__error" id="<?php echo esc_attr( $field_id( 'full_name-error' ) ); ?>" hidden><span class="quote-form__visually-hidden"><?php esc_html_e( 'Error:', 'quote-form' ); ?></span> <span data-role="error-message"></span></p>
			</div>

			<div class="quote-form__field">
				<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'email' ) ); ?>"><?php esc_html_e( 'Email', 'quote-form' ); ?></label>
				<input class="quote-form__input" type="email" id="<?php echo esc_attr( $field_id( 'email' ) ); ?>" name="email" autocomplete="email" aria-describedby="<?php echo esc_attr( $field_id( 'email-error' ) ); ?>">
				<p class="quote-form__error" id="<?php echo esc_attr( $field_id( 'email-error' ) ); ?>" hidden><span class="quote-form__visually-hidden"><?php esc_html_e( 'Error:', 'quote-form' ); ?></span> <span data-role="error-message"></span></p>
			</div>
		</div>

		<div class="quote-form__row">
			<div class="quote-form__field">
				<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'phone' ) ); ?>">
					<?php esc_html_e( 'Phone', 'quote-form' ); ?> <span class="quote-form__optional"><?php esc_html_e( '(optional)', 'quote-form' ); ?></span>
				</label>
				<input class="quote-form__input" type="tel" id="<?php echo esc_attr( $field_id( 'phone' ) ); ?>" name="phone" autocomplete="tel">
			</div>

			<div class="quote-form__field">
				<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'company' ) ); ?>">
					<?php esc_html_e( 'Company', 'quote-form' ); ?> <span class="quote-form__optional"><?php esc_html_e( '(optional)', 'quote-form' ); ?></span>
				</label>
				<input class="quote-form__input" type="text" id="<?php echo esc_attr( $field_id( 'company' ) ); ?>" name="company" autocomplete="organization">
			</div>
		</div>

		<div class="quote-form__field">
			<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'message' ) ); ?>">
				<?php esc_html_e( 'What do you need?', 'quote-form' ); ?>
				<?php if ( ! $form['is_message_required'] ) : ?>
					<span class="quote-form__optional"><?php esc_html_e( '(optional)', 'quote-form' ); ?></span>
				<?php endif; ?>
			</label>
			<textarea class="quote-form__input" id="<?php echo esc_attr( $field_id( 'message' ) ); ?>" name="message" rows="4" aria-describedby="<?php echo esc_attr( $field_id( 'message-error' ) ); ?>" <?php echo $form['is_message_required'] ? 'data-required' : ''; ?>></textarea>
			<p class="quote-form__error" id="<?php echo esc_attr( $field_id( 'message-error' ) ); ?>" hidden><span class="quote-form__visually-hidden"><?php esc_html_e( 'Error:', 'quote-form' ); ?></span> <span data-role="error-message"></span></p>
		</div>

		<?php if ( '' !== $form['turnstile_site_key'] ) : ?>
			<div class="cf-turnstile quote-form__turnstile" data-sitekey="<?php echo esc_attr( $form['turnstile_site_key'] ); ?>"></div>
		<?php endif; ?>

		<p class="quote-form__status" data-role="status" role="alert" hidden></p>

		<button class="quote-form__submit" type="submit"><?php esc_html_e( 'Send request', 'quote-form' ); ?></button>

		<noscript>
			<p class="quote-form__noscript"><?php esc_html_e( 'This form needs JavaScript for its spam check. Turn JavaScript on to send a request.', 'quote-form' ); ?></p>
		</noscript>
	</form>

	<div class="quote-form__success" data-role="success" role="status" tabindex="-1" hidden>
		<p class="quote-form__success-title"><?php esc_html_e( 'Request sent', 'quote-form' ); ?></p>
		<p><?php echo esc_html( $form['success_message'] ); ?></p>
	</div>
</div>
