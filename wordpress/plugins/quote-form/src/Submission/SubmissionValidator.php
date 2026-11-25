<?php
/**
 * Field validation for submissions.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Submission;

use QuoteForm\Form\PriceEstimator;
use QuoteForm\Form\ProjectBrief;
use QuoteForm\Settings\Settings;

/**
 * Cleans the submitted fields and reports one message per invalid field.
 * Messages match the form's own browser-side checks.
 *
 * Two forms post here: the quote form (priced services from the settings page) and the project brief
 * (the ProjectBrief choices plus a required description).
 */
final class SubmissionValidator {

	private const MAX_NAME_LENGTH    = 200;
	private const MAX_PHONE_LENGTH   = 50;
	private const MAX_COMPANY_LENGTH = 200;
	private const MAX_MESSAGE_LENGTH = 5000;

	/**
	 * Creates the validator.
	 *
	 * @param Settings $settings Saved settings, for the list of offered services.
	 */
	public function __construct( private Settings $settings ) {
	}

	/**
	 * Validates decoded JSON from the form.
	 *
	 * @param array<string, mixed> $input Request body.
	 * @return array{values: array{is_brief: bool, services: list<string>, timeline: string, full_name: string, email: string, phone: ?string, company: ?string, message: ?string}, errors: array<string, string>}
	 */
	public function validate( array $input ): array {
		$errors   = array();
		$is_brief = ProjectBrief::FORM_TYPE === ( $input['form'] ?? null );

		$offered_services = $is_brief
			? array_keys( ProjectBrief::services() )
			: array_column( $this->settings->all()['services'], 'key' );
		$services         = $this->service_keys( $input['services'] ?? null );
		if ( array() === $services ) {
			$errors['services'] = $is_brief
				? __( 'Choose at least one thing you need help with.', 'quote-form' )
				: __( 'Choose at least one service.', 'quote-form' );
		} elseif ( array_diff( $services, $offered_services ) !== array() ) {
			$errors['services'] = __( 'Choose services from the list.', 'quote-form' );
		}

		$offered_timelines = $is_brief ? ProjectBrief::timelines() : PriceEstimator::TIMELINE_PERCENTAGES;
		$timeline          = $this->text( $input, 'timeline' );
		if ( ! array_key_exists( $timeline, $offered_timelines ) ) {
			$errors['timeline'] = __( 'Choose a timeline.', 'quote-form' );
		}

		$full_name = $this->text( $input, 'full_name' );
		if ( '' === $full_name ) {
			$errors['full_name'] = __( 'Enter your name.', 'quote-form' );
		} elseif ( mb_strlen( $full_name ) > self::MAX_NAME_LENGTH ) {
			$errors['full_name'] = $this->too_long_message( self::MAX_NAME_LENGTH );
		}

		$email = sanitize_email( $this->text( $input, 'email' ) );
		if ( ! is_email( $email ) ) {
			$errors['email'] = __( 'Enter an email address like name@example.com.', 'quote-form' );
		}

		$phone = $this->text( $input, 'phone' );
		if ( mb_strlen( $phone ) > self::MAX_PHONE_LENGTH ) {
			$errors['phone'] = $this->too_long_message( self::MAX_PHONE_LENGTH );
		}

		$company = $this->text( $input, 'company' );
		if ( mb_strlen( $company ) > self::MAX_COMPANY_LENGTH ) {
			$errors['company'] = $this->too_long_message( self::MAX_COMPANY_LENGTH );
		}

		$message = sanitize_textarea_field( $this->raw_string( $input, 'message' ) );
		if ( $is_brief && '' === trim( $message ) ) {
			$errors['message'] = __( 'Tell me a little about the project.', 'quote-form' );
		} elseif ( mb_strlen( $message ) > self::MAX_MESSAGE_LENGTH ) {
			$errors['message'] = $this->too_long_message( self::MAX_MESSAGE_LENGTH );
		}

		return array(
			'values' => array(
				'is_brief'  => $is_brief,
				'services'  => $services,
				'timeline'  => $timeline,
				'full_name' => $full_name,
				'email'     => $email,
				'phone'     => '' === $phone ? null : $phone,
				'company'   => '' === $company ? null : $company,
				'message'   => '' === $message ? null : $message,
			),
			'errors' => $errors,
		);
	}

	/**
	 * Unique service keys from the submitted list; anything that is not a list of strings counts as empty.
	 *
	 * @param mixed $submitted Submitted services.
	 * @return list<string>
	 */
	private function service_keys( mixed $submitted ): array {
		if ( ! is_array( $submitted ) ) {
			return array();
		}

		$keys = array_map(
			static fn( mixed $key ): string => is_string( $key ) ? sanitize_key( $key ) : '',
			$submitted
		);

		return array_values( array_unique( array_filter( $keys ) ) );
	}

	/**
	 * A single-line text field, sanitized.
	 *
	 * @param array<string, mixed> $input Request body.
	 * @param string               $key   Field name.
	 */
	private function text( array $input, string $key ): string {
		return sanitize_text_field( $this->raw_string( $input, $key ) );
	}

	/**
	 * A field's value when it is a string, otherwise an empty string.
	 *
	 * @param array<string, mixed> $input Request body.
	 * @param string               $key   Field name.
	 */
	private function raw_string( array $input, string $key ): string {
		return is_string( $input[ $key ] ?? null ) ? $input[ $key ] : '';
	}

	/**
	 * Message for a field over its length limit.
	 *
	 * @param int $max_length Allowed characters.
	 */
	private function too_long_message( int $max_length ): string {
		/* translators: %d: maximum number of characters. */
		return sprintf( __( 'Use %d characters or fewer.', 'quote-form' ), $max_length );
	}
}
