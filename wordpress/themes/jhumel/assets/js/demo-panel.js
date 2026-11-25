/**
 * Lead Engine's demo panel: "Try it live" opens it under the case study, and the step list on its left follows the
 * visitor. Before a send, step 1 (send a test lead) is current; after a send, it is ticked off and steps 2 and 3
 * (watch Telegram, open the dashboard) are highlighted. The button needs this script, so it starts hidden.
 */
( function () {
	'use strict';

	const panel = document.querySelector( '[data-demo-panel]' );
	const toggles = document.querySelectorAll( '[data-open-demo]' );
	const steps = Array.from( panel.querySelectorAll( '[data-demo-steps] > li' ) );
	const prefersReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function setOpen( isOpen ) {
		panel.hidden = ! isOpen;
		toggles.forEach( ( toggle ) => toggle.setAttribute( 'aria-expanded', String( isOpen ) ) );
	}

	function openPanel() {
		setOpen( true );
		panel.scrollIntoView( { behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' } );
		panel.focus( { preventScroll: true } );
	}

	function closePanel() {
		setOpen( false );
		toggles[ 0 ].focus();
	}

	function showSent( isSent ) {
		steps.forEach( ( step, index ) => {
			const isFirstStep = index === 0;
			step.classList.toggle( 'is-done', isSent && isFirstStep );
			step.classList.toggle( 'is-current', isSent !== isFirstStep );
		} );
	}

	toggles.forEach( ( toggle ) => {
		toggle.addEventListener( 'click', () => ( panel.hidden ? openPanel() : closePanel() ) );
		toggle.closest( '.demo-opener' ).hidden = false;
	} );
	panel.querySelector( '[data-close-demo]' ).addEventListener( 'click', closePanel );
	panel.addEventListener( 'quote-form:sent', () => showSent( true ) );
	panel.querySelector( '[data-role="send-another"]' ).addEventListener( 'click', () => showSent( false ) );
} )();
