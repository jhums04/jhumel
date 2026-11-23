/**
 * Quote form: live price estimate, validation, and sending.
 *
 * The estimate here is display only. The server recalculates it with PriceEstimator.
 */
( function () {
	'use strict';

	const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	function formatDollars( cents ) {
		const hasCents = cents % 100 !== 0;

		return new Intl.NumberFormat( 'en-US', {
			style: 'currency',
			currency: 'USD',
			minimumFractionDigits: hasCents ? 2 : 0,
			maximumFractionDigits: 2,
		} ).format( cents / 100 );
	}

	function initializeQuoteForm( form ) {
		const config = JSON.parse( form.dataset.config );
		const wrapper = form.closest( '.quote-form-wrapper' );
		const estimateOutput = form.querySelector( '[data-role="estimate"]' );
		const statusOutput = form.querySelector( '[data-role="status"]' );
		const successPanel = wrapper.querySelector( '[data-role="success"]' );
		const submitButton = form.querySelector( '.quote-form__submit' );
		const submitLabel = submitButton.textContent;

		function selectedServiceKeys() {
			return Array.from( form.querySelectorAll( 'input[name="services"]:checked' ) ).map(
				( checkbox ) => checkbox.value
			);
		}

		function fieldValue( name ) {
			return form.elements[ name ].value.trim();
		}

		// Same formula as PriceEstimator: whole dollars times a percentage gives cents.
		function estimateCents( serviceKeys, timeline ) {
			const totalDollars = serviceKeys.reduce(
				( sum, serviceKey ) => sum + config.prices[ serviceKey ],
				0
			);

			return totalDollars * config.timelinePercentages[ timeline ];
		}

		function updateEstimate() {
			const serviceKeys = selectedServiceKeys();

			if ( serviceKeys.length === 0 ) {
				estimateOutput.textContent = config.messages.estimateEmpty;
				return;
			}

			const cents = estimateCents( serviceKeys, fieldValue( 'timeline' ) );
			estimateOutput.textContent = config.messages.estimate.replace( '%s', formatDollars( cents ) );
		}

		function errorElement( field ) {
			return document.getElementById( form.id + '-' + field + '-error' );
		}

		function showFieldError( field, message ) {
			const error = errorElement( field );

			// Fields without their own error slot, like the Turnstile token, use the general status line.
			if ( error === null ) {
				showStatus( message );
				return;
			}

			error.querySelector( '[data-role="error-message"]' ).textContent = message;
			error.hidden = false;

			const input = form.elements[ field ];
			if ( input instanceof HTMLElement ) {
				input.setAttribute( 'aria-invalid', 'true' );
			}
		}

		function clearFieldError( field ) {
			const error = errorElement( field );
			if ( error === null || error.hidden ) {
				return;
			}

			error.querySelector( '[data-role="error-message"]' ).textContent = '';
			error.hidden = true;

			const input = form.elements[ field ];
			if ( input instanceof HTMLElement ) {
				input.removeAttribute( 'aria-invalid' );
			}
		}

		function clearErrors() {
			[ 'services', 'full_name', 'email', 'message' ].forEach( clearFieldError );
			statusOutput.hidden = true;
			statusOutput.textContent = '';
		}

		// An error disappears as soon as the visitor edits its field, so fixed fields stop looking broken.
		function handleFieldEdit( event ) {
			clearFieldError( event.target.name );
		}

		function showStatus( message ) {
			statusOutput.textContent = message;
			statusOutput.hidden = false;
		}

		function focusFirstError() {
			const firstError = form.querySelector( '.quote-form__error:not([hidden])' );
			if ( firstError === null ) {
				return;
			}
			const field = firstError.closest( '.quote-form__field, .quote-form__group' );
			field.querySelector( 'input, select, textarea' ).focus();
		}

		function validate() {
			const errors = {};

			if ( selectedServiceKeys().length === 0 ) {
				errors.services = config.messages.servicesRequired;
			}
			if ( fieldValue( 'full_name' ) === '' ) {
				errors.full_name = config.messages.nameRequired;
			}
			if ( ! EMAIL_PATTERN.test( fieldValue( 'email' ) ) ) {
				errors.email = config.messages.emailInvalid;
			}
			if ( form.elements.message.hasAttribute( 'data-required' ) && fieldValue( 'message' ) === '' ) {
				errors.message = config.messages.messageRequired;
			}

			return errors;
		}

		function turnstileToken() {
			const tokenInput = form.querySelector( 'input[name="cf-turnstile-response"]' );
			return tokenInput ? tokenInput.value : '';
		}

		function resetTurnstile() {
			const widget = form.querySelector( '.cf-turnstile' );
			if ( widget && window.turnstile ) {
				window.turnstile.reset( widget );
			}
		}

		function showSuccess() {
			form.hidden = true;
			successPanel.hidden = false;
			successPanel.focus();
		}

		function setSending( isSending ) {
			submitButton.disabled = isSending;
			submitButton.textContent = isSending ? config.messages.sending : submitLabel;
		}

		async function sendRequest() {
			const response = await fetch( config.endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( {
					organization: fieldValue( 'organization' ),
					nonce: fieldValue( 'nonce' ),
					services: selectedServiceKeys(),
					timeline: fieldValue( 'timeline' ),
					full_name: fieldValue( 'full_name' ),
					email: fieldValue( 'email' ),
					phone: fieldValue( 'phone' ),
					company: fieldValue( 'company' ),
					message: fieldValue( 'message' ),
					turnstile_token: turnstileToken(),
				} ),
			} );

			if ( response.ok ) {
				showSuccess();
				return;
			}

			// 422 carries one message per field, shown the same way as browser-side errors.
			if ( response.status === 422 ) {
				const body = await response.json();
				Object.entries( body.errors ).forEach( ( [ field, message ] ) => showFieldError( field, message ) );
				focusFirstError();
			} else {
				showStatus( config.messages.sendFailed );
			}
			resetTurnstile();
		}

		async function handleSubmit( event ) {
			event.preventDefault();
			clearErrors();

			const errors = validate();
			if ( Object.keys( errors ).length > 0 ) {
				Object.entries( errors ).forEach( ( [ field, message ] ) => showFieldError( field, message ) );
				focusFirstError();
				return;
			}

			if ( form.querySelector( '.cf-turnstile' ) && turnstileToken() === '' ) {
				showStatus( config.messages.turnstilePending );
				return;
			}

			setSending( true );
			try {
				await sendRequest();
			} catch {
				showStatus( config.messages.sendFailed );
				resetTurnstile();
			}
			setSending( false );
		}

		form.addEventListener( 'change', updateEstimate );
		form.addEventListener( 'input', handleFieldEdit );
		form.addEventListener( 'change', handleFieldEdit );
		form.addEventListener( 'submit', handleSubmit );
		updateEstimate();
	}

	document.querySelectorAll( '.quote-form' ).forEach( initializeQuoteForm );
} )();
