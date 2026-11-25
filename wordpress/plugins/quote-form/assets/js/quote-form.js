/**
 * Quote form and project brief: live price estimate (quote form only), steps (brief only), validation, and sending.
 *
 * The estimate here is display only. The server recalculates it with PriceEstimator.
 */
( function () {
	'use strict';

	const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	// Turnstile (the spam check) is loaded once, the first time any form nears the screen or is focused.
	// Until then the page does not download it, which keeps the page light for visitors who never reach a form.
	let turnstileLoading = null;

	function loadTurnstile( scriptUrl ) {
		if ( turnstileLoading === null ) {
			turnstileLoading = new Promise( ( resolve, reject ) => {
				const script = document.createElement( 'script' );
				script.src = scriptUrl + '?render=explicit';
				script.async = true;
				script.onload = () => resolve( window.turnstile );
				script.onerror = reject;
				document.head.appendChild( script );
			} );
		}
		return turnstileLoading;
	}

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
		const isBrief = config.formType === 'brief';
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

		// A field the form does not have (the brief has no phone) reads as empty. Radio groups read their checked value.
		function fieldValue( name ) {
			const field = form.elements[ name ];
			return field ? field.value.trim() : '';
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
			if ( estimateOutput === null ) {
				return;
			}

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

		// The brief asks its questions in steps, one at a time. The quote form has none, so these do nothing there.
		const steps = Array.from( form.querySelectorAll( '[data-step]' ) );
		const stepperItems = Array.from( form.querySelectorAll( '.quote-form__stepper-item' ) );
		let currentStep = 0;

		function showStep( index ) {
			currentStep = index;
			steps.forEach( ( step, stepIndex ) => {
				step.hidden = stepIndex !== index;
			} );
			stepperItems.forEach( ( item, itemIndex ) => {
				item.classList.toggle( 'is-current', itemIndex === index );
				item.classList.toggle( 'is-done', itemIndex < index );
			} );
		}

		// Moving focus to the new step tells screen readers which step they are on, from the step's label.
		function moveToStep( index ) {
			showStep( index );
			steps[ index ].focus();
		}

		function stepOfField( field ) {
			return steps.findIndex( ( step ) => step.querySelector( '[name="' + field + '"]' ) !== null );
		}

		function focusFirstError() {
			const firstError = form.querySelector( '.quote-form__error:not([hidden])' );
			if ( firstError === null ) {
				return;
			}
			if ( steps.length > 0 ) {
				showStep( steps.indexOf( firstError.closest( '[data-step]' ) ) );
			}
			const field = firstError.closest( '.quote-form__field, .quote-form__group' );
			field.querySelector( 'input, select, textarea' ).focus();
		}

		function validate() {
			const errors = {};

			if ( selectedServiceKeys().length === 0 ) {
				errors.services = isBrief ? config.messages.needsRequired : config.messages.servicesRequired;
			}
			if ( fieldValue( 'full_name' ) === '' ) {
				errors.full_name = config.messages.nameRequired;
			}
			if ( ! EMAIL_PATTERN.test( fieldValue( 'email' ) ) ) {
				errors.email = config.messages.emailInvalid;
			}
			if ( form.elements.message.hasAttribute( 'data-required' ) && fieldValue( 'message' ) === '' ) {
				errors.message = isBrief ? config.messages.briefRequired : config.messages.messageRequired;
			}

			return errors;
		}

		// Next checks only the questions on the current step; Send checks them all again.
		function goToNextStep() {
			clearErrors();

			const stepErrors = Object.entries( validate() ).filter(
				( [ field ] ) => stepOfField( field ) === currentStep
			);
			if ( stepErrors.length > 0 ) {
				stepErrors.forEach( ( [ field, message ] ) => showFieldError( field, message ) );
				focusFirstError();
				return;
			}

			moveToStep( currentStep + 1 );
		}

		function goToPreviousStep() {
			clearErrors();
			moveToStep( currentStep - 1 );
		}

		const turnstileWidget = form.querySelector( '.cf-turnstile' );
		let isTurnstileRendered = false;

		function renderTurnstile() {
			if ( turnstileWidget === null || isTurnstileRendered ) {
				return;
			}
			isTurnstileRendered = true;
			loadTurnstile( config.turnstileScript )
				.then( ( turnstile ) => turnstile.render( turnstileWidget, { sitekey: turnstileWidget.dataset.sitekey } ) )
				.catch( () => {
					isTurnstileRendered = false;
				} );
		}

		if ( turnstileWidget !== null ) {
			const observer = new IntersectionObserver(
				( entries ) => {
					if ( entries.some( ( entry ) => entry.isIntersecting ) ) {
						observer.disconnect();
						renderTurnstile();
					}
				},
				{ rootMargin: '600px 0px' }
			);
			// The widget itself is watched, not the form: in the brief it sits on the last step, and it should load
			// only once that step is on screen.
			observer.observe( turnstileWidget );
			( turnstileWidget.closest( '[data-step]' ) ?? form ).addEventListener( 'focusin', renderTurnstile, {
				once: true,
			} );
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

		// Themes can listen for quote-form:sent on the page to show their own next steps after a send.
		function showSuccess() {
			form.hidden = true;
			successPanel.hidden = false;
			successPanel.focus();
			wrapper.dispatchEvent( new CustomEvent( 'quote-form:sent', { bubbles: true } ) );
		}

		// Back to a blank form. The spam-check token was spent on the last send, so the widget gets a fresh one.
		function showBlankForm() {
			form.reset();
			clearErrors();
			updateEstimate();
			successPanel.hidden = true;
			form.hidden = false;
			resetTurnstile();
			if ( steps.length > 0 ) {
				showStep( 0 );
			}
			form.querySelector( 'input:not([type="hidden"]), select, textarea' ).focus();
		}

		function setSending( isSending ) {
			submitButton.disabled = isSending;
			submitButton.textContent = isSending ? config.messages.sending : submitLabel;
		}

		async function sendRequest() {
			const response = await fetch( config.endpoint, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': config.restNonce,
				},
				body: JSON.stringify( {
					form: config.formType,
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

			const body = await response.json().catch( () => ( {} ) );

			// 422 carries one message per field, shown the same way as browser-side errors.
			// Other refusals (expired form, too many requests, spam check down) carry one message.
			if ( response.status === 422 && body.errors ) {
				Object.entries( body.errors ).forEach( ( [ field, message ] ) => showFieldError( field, message ) );
				focusFirstError();
			} else {
				showStatus( typeof body.message === 'string' ? body.message : config.messages.sendFailed );
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

			if ( turnstileWidget !== null && turnstileToken() === '' ) {
				renderTurnstile();
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
		wrapper.querySelector( '[data-role="send-another"]' ).addEventListener( 'click', showBlankForm );
		updateEstimate();

		if ( steps.length > 0 ) {
			form.querySelectorAll( '[data-role="stepper"], [data-role="step-controls"], [data-role="back"]' ).forEach(
				( element ) => {
					element.hidden = false;
				}
			);
			// The first step has nothing to go back to.
			steps[ 0 ].querySelector( '[data-role="back"]' ).hidden = true;
			form.querySelectorAll( '[data-role="next"]' ).forEach( ( button ) =>
				button.addEventListener( 'click', goToNextStep )
			);
			form.querySelectorAll( '[data-role="back"]' ).forEach( ( button ) =>
				button.addEventListener( 'click', goToPreviousStep )
			);
			showStep( 0 );
		}
	}

	document.querySelectorAll( '.quote-form' ).forEach( initializeQuoteForm );
} )();
