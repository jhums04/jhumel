<?php
/**
 * The "Start a project" brief. Included by FormShortcode::render() with type="brief", which supplies $form.
 * It shares the quote form's script, endpoint, spam check, and delivery; only the questions differ.
 *
 * The questions come in three steps. Without JavaScript every step shows at once (the form needs JavaScript to
 * send anyway); the script shows one step at a time with the step list, Back, and Next.
 *
 * @package QuoteForm
 *
 * @var array<string, mixed> $form
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variables are local to FormShortcode::render().

$field_id = static fn( string $field ): string => $form['id'] . '-' . $field;

$step_names = array(
	__( 'What you need', 'quote-form' ),
	__( 'The project', 'quote-form' ),
	__( 'Your details', 'quote-form' ),
);

$step_attributes = static function ( int $step_number ) use ( $step_names ): void {
	/* translators: 1: step number, 2: number of steps, 3: step name. */
	$label = sprintf( __( 'Step %1$d of %2$d: %3$s', 'quote-form' ), $step_number, count( $step_names ), $step_names[ $step_number - 1 ] );
	printf( 'class="quote-form__step" role="group" aria-label="%s" tabindex="-1" data-step', esc_attr( $label ) );
};

$next_button = static function (): void {
	?>
	<div class="quote-form__actions" data-role="step-controls" hidden>
		<button class="quote-form__back" type="button" data-role="back"><?php esc_html_e( 'Back', 'quote-form' ); ?></button>
		<button class="quote-form__next" type="button" data-role="next"><?php esc_html_e( 'Next', 'quote-form' ); ?></button>
	</div>
	<?php
};

$error_slot = static function ( string $field ) use ( $field_id ): void {
	?>
	<p class="quote-form__error" id="<?php echo esc_attr( $field_id( $field . '-error' ) ); ?>" hidden><span class="quote-form__visually-hidden"><?php esc_html_e( 'Error:', 'quote-form' ); ?></span> <span data-role="error-message"></span></p>
	<?php
};
?>
<div class="quote-form-wrapper">
	<form class="quote-form quote-form--brief" id="<?php echo esc_attr( $form['id'] ); ?>" data-config="<?php echo esc_attr( wp_json_encode( $form['config'] ) ); ?>" novalidate>
		<input type="hidden" name="organization" value="<?php echo esc_attr( $form['organization'] ); ?>">
		<input type="hidden" name="nonce" value="<?php echo esc_attr( $form['nonce'] ); ?>">

		<ol class="quote-form__stepper" data-role="stepper" aria-hidden="true" hidden>
			<?php foreach ( $step_names as $step_name ) : ?>
				<li class="quote-form__stepper-item"><?php echo esc_html( $step_name ); ?></li>
			<?php endforeach; ?>
		</ol>

		<div <?php $step_attributes( 1 ); ?>>
			<fieldset class="quote-form__group" aria-describedby="<?php echo esc_attr( $field_id( 'services-error' ) ); ?>">
				<legend class="quote-form__label"><?php esc_html_e( 'What do you need help with?', 'quote-form' ); ?> <span class="quote-form__optional"><?php esc_html_e( '(choose any)', 'quote-form' ); ?></span></legend>
				<div class="quote-form__chips">
					<?php foreach ( $form['services'] as $service_key => $service_label ) : ?>
						<label class="quote-form__chip">
							<input class="quote-form__chip-input" type="checkbox" name="services" value="<?php echo esc_attr( $service_key ); ?>">
							<span><?php echo esc_html( $service_label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<?php $error_slot( 'services' ); ?>
			</fieldset>

			<?php $next_button(); ?>
		</div>

		<div <?php $step_attributes( 2 ); ?>>
			<fieldset class="quote-form__group">
				<legend class="quote-form__label"><?php esc_html_e( 'Timeline', 'quote-form' ); ?></legend>
				<div class="quote-form__chips">
					<?php foreach ( $form['timelines'] as $timeline_key => $timeline_label ) : ?>
						<label class="quote-form__chip">
							<input class="quote-form__chip-input" type="radio" name="timeline" value="<?php echo esc_attr( $timeline_key ); ?>" <?php checked( 'standard', $timeline_key ); ?>>
							<span><?php echo esc_html( $timeline_label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<div class="quote-form__field">
				<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'message' ) ); ?>"><?php esc_html_e( 'Tell me about the project', 'quote-form' ); ?></label>
				<textarea class="quote-form__input" id="<?php echo esc_attr( $field_id( 'message' ) ); ?>" name="message" rows="5" placeholder="<?php esc_attr_e( 'What is slowing your team down, and what would "done" look like?', 'quote-form' ); ?>" aria-describedby="<?php echo esc_attr( $field_id( 'message-error' ) ); ?>" data-required></textarea>
				<?php $error_slot( 'message' ); ?>
			</div>

			<?php $next_button(); ?>
		</div>

		<div <?php $step_attributes( 3 ); ?>>
			<div class="quote-form__row">
				<div class="quote-form__field">
					<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'full_name' ) ); ?>"><?php esc_html_e( 'Name', 'quote-form' ); ?></label>
					<input class="quote-form__input" type="text" id="<?php echo esc_attr( $field_id( 'full_name' ) ); ?>" name="full_name" autocomplete="name" aria-describedby="<?php echo esc_attr( $field_id( 'full_name-error' ) ); ?>">
					<?php $error_slot( 'full_name' ); ?>
				</div>

				<div class="quote-form__field">
					<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'email' ) ); ?>"><?php esc_html_e( 'Email', 'quote-form' ); ?></label>
					<input class="quote-form__input" type="email" id="<?php echo esc_attr( $field_id( 'email' ) ); ?>" name="email" autocomplete="email" aria-describedby="<?php echo esc_attr( $field_id( 'email-error' ) ); ?>">
					<?php $error_slot( 'email' ); ?>
				</div>
			</div>

			<div class="quote-form__field">
				<label class="quote-form__label" for="<?php echo esc_attr( $field_id( 'company' ) ); ?>">
					<?php esc_html_e( 'Company', 'quote-form' ); ?> <span class="quote-form__optional"><?php esc_html_e( '(optional)', 'quote-form' ); ?></span>
				</label>
				<input class="quote-form__input" type="text" id="<?php echo esc_attr( $field_id( 'company' ) ); ?>" name="company" autocomplete="organization">
			</div>

			<?php if ( '' !== $form['turnstile_site_key'] ) : ?>
				<div class="cf-turnstile quote-form__turnstile" data-sitekey="<?php echo esc_attr( $form['turnstile_site_key'] ); ?>"></div>
			<?php endif; ?>

			<p class="quote-form__status" data-role="status" role="alert" hidden></p>

			<div class="quote-form__actions">
				<button class="quote-form__back" type="button" data-role="back" hidden><?php esc_html_e( 'Back', 'quote-form' ); ?></button>
				<button class="quote-form__submit" type="submit"><?php esc_html_e( 'Send my brief', 'quote-form' ); ?></button>
			</div>
		</div>

		<noscript>
			<p class="quote-form__noscript"><?php esc_html_e( 'This form needs JavaScript for its spam check. Turn JavaScript on to send your brief.', 'quote-form' ); ?></p>
		</noscript>
	</form>

	<div class="quote-form__success" data-role="success" role="status" tabindex="-1" hidden>
		<p class="quote-form__success-title"><?php esc_html_e( 'Brief received', 'quote-form' ); ?></p>
		<p><?php echo esc_html( $form['success_message'] ); ?></p>
		<button class="quote-form__again" type="button" data-role="send-another"><?php esc_html_e( 'Send another brief', 'quote-form' ); ?></button>
	</div>
</div>
