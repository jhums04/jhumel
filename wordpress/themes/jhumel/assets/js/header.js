/**
 * The sticky header compacts once the page scrolls: less padding and a soft shadow, so it takes less room
 * while reading. Without this script the header simply stays at its full height.
 */
( function () {
	'use strict';

	const COMPACT_AFTER_PIXELS = 24;
	const header = document.querySelector( 'header.wp-block-template-part' );

	function updateHeader() {
		header.classList.toggle( 'is-compact', window.scrollY > COMPACT_AFTER_PIXELS );
	}

	window.addEventListener( 'scroll', updateHeader, { passive: true } );
	updateHeader();
} )();
