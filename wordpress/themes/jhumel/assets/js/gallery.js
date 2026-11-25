/**
 * Screenshot galleries: open each screenshot link in a full-screen viewer with previous and next.
 * The page has one viewer; previous and next stay within the gallery the screenshot came from.
 *
 * Without this script the links simply open the full-size images. The viewer is a native modal dialog, so the
 * browser keeps focus inside it, closes it on Escape, and returns focus to the screenshot that opened it.
 */
( function () {
	'use strict';

	const SWIPE_DISTANCE = 50;

	const lightbox = document.querySelector( '[data-lightbox]' );

	const image = lightbox.querySelector( '[data-role="image"]' );
	const caption = lightbox.querySelector( '[data-role="caption"]' );
	const counter = lightbox.querySelector( '[data-role="counter"]' );
	let galleryLinks = [];
	let currentIndex = 0;
	let swipeStartX = null;

	function show( index ) {
		currentIndex = ( index + galleryLinks.length ) % galleryLinks.length;
		const link = galleryLinks[ currentIndex ];

		image.src = link.href;
		image.alt = link.querySelector( 'img' ).alt;
		caption.textContent = link.dataset.caption;
		counter.textContent = lightbox.dataset.counter
			.replace( '%1$s', String( currentIndex + 1 ) )
			.replace( '%2$s', String( galleryLinks.length ) );
	}

	function open( event ) {
		event.preventDefault();
		galleryLinks = Array.from(
			event.currentTarget.closest( '[data-gallery]' ).querySelectorAll( '.gallery__item' )
		);
		show( galleryLinks.indexOf( event.currentTarget ) );
		lightbox.showModal();
	}

	function handleKey( event ) {
		if ( event.key === 'ArrowRight' ) {
			show( currentIndex + 1 );
		} else if ( event.key === 'ArrowLeft' ) {
			show( currentIndex - 1 );
		}
	}

	// A click on the dimmed area around the image closes the viewer, like most image viewers.
	function closeOnBackdrop( event ) {
		if ( event.target === lightbox ) {
			lightbox.close();
		}
	}

	function startSwipe( event ) {
		swipeStartX = event.clientX;
	}

	function endSwipe( event ) {
		if ( swipeStartX === null ) {
			return;
		}
		const distance = event.clientX - swipeStartX;
		swipeStartX = null;

		if ( Math.abs( distance ) >= SWIPE_DISTANCE ) {
			show( currentIndex + ( distance < 0 ? 1 : -1 ) );
		}
	}

	document.querySelectorAll( '[data-gallery] .gallery__item' ).forEach( ( link ) => link.addEventListener( 'click', open ) );
	lightbox.querySelector( '[data-role="close"]' ).addEventListener( 'click', () => lightbox.close() );
	lightbox.querySelector( '[data-role="previous"]' ).addEventListener( 'click', () => show( currentIndex - 1 ) );
	lightbox.querySelector( '[data-role="next"]' ).addEventListener( 'click', () => show( currentIndex + 1 ) );
	lightbox.addEventListener( 'keydown', handleKey );
	lightbox.addEventListener( 'click', closeOnBackdrop );
	image.addEventListener( 'pointerdown', startSwipe );
	image.addEventListener( 'pointerup', endSwipe );
	image.addEventListener( 'pointercancel', () => {
		swipeStartX = null;
	} );
} )();
