<?php
/**
 * Theme setup: pattern category, cleanup, and meta tags.
 *
 * @package Jhumel
 */

add_action( 'init', 'jhumel_register_pattern_category' );
add_action( 'wp_enqueue_scripts', 'jhumel_enqueue_stylesheet' );
add_action( 'wp_enqueue_scripts', 'jhumel_enqueue_homepage_scripts' );
add_action( 'wp_enqueue_scripts', 'jhumel_enqueue_header_script' );
add_action( 'init', 'jhumel_remove_emoji_support' );
add_action( 'init', 'jhumel_remove_comment_support' );
add_action( 'wp_head', 'jhumel_output_meta_tags', 1 );

add_filter( 'comments_open', '__return_false' );
add_filter( 'pings_open', '__return_false' );
add_filter( 'comments_array', '__return_empty_array' );
add_filter( 'xmlrpc_methods', 'jhumel_remove_pingback_methods' );
add_filter( 'wp_headers', 'jhumel_remove_pingback_header' );

/**
 * Groups the homepage patterns in the editor's pattern inserter.
 */
function jhumel_register_pattern_category(): void {
	register_block_pattern_category( 'jhumel', array( 'label' => __( 'Jhumel', 'jhumel' ) ) );
}

/**
 * Loads style.css, which block themes do not load on their own.
 */
function jhumel_enqueue_stylesheet(): void {
	wp_enqueue_style( 'jhumel', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}

/**
 * Loads the homepage's two scripts: the screenshot viewer and Lead Engine's demo panel.
 * No other page has either, so no other page loads them.
 */
function jhumel_enqueue_homepage_scripts(): void {
	if ( ! is_front_page() ) {
		return;
	}

	$version = wp_get_theme()->get( 'Version' );
	$loading = array(
		'strategy'  => 'defer',
		'in_footer' => true,
	);

	wp_enqueue_script( 'jhumel-gallery', get_theme_file_uri( 'assets/js/gallery.js' ), array(), $version, $loading );
	wp_enqueue_script( 'jhumel-demo-panel', get_theme_file_uri( 'assets/js/demo-panel.js' ), array(), $version, $loading );
}

/**
 * Loads the script that compacts the sticky header on scroll. Every page has the header, so every page loads it.
 */
function jhumel_enqueue_header_script(): void {
	wp_enqueue_script(
		'jhumel-header',
		get_theme_file_uri( 'assets/js/header.js' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}

/**
 * One screenshot for a gallery: the image shown on the page (a smaller copy where one exists) and the full-size
 * image the viewer opens. Used by the Work and Automations patterns.
 *
 * @param string $preview File shown on the page, in assets/images.
 * @param int    $width   Width of the shown file, so the browser reserves the space before it loads.
 * @param int    $height  Height of the shown file.
 * @param string $full    Full-size file for the viewer.
 * @param string $label   Short visible label.
 * @param string $alt     What the screenshot shows, for screen readers.
 * @param string $caption Caption in the viewer: what it does for the business.
 * @return array<string, string|int>
 */
function jhumel_screenshot( string $preview, int $width, int $height, string $full, string $label, string $alt, string $caption ): array {
	return array(
		'preview' => $preview,
		'width'   => $width,
		'height'  => $height,
		'full'    => $full,
		'label'   => $label,
		'alt'     => $alt,
		'caption' => $caption,
	);
}

/**
 * Prints a gallery: screenshot links that assets/js/gallery.js opens in the page's one viewer (in the Work
 * pattern). Without JavaScript each link opens its image.
 *
 * @param string                           $variant Layout: "showcase" (one large, then thumbnails) or "stack".
 * @param array<int, array<string, mixed>> $shots   Screenshots from jhumel_screenshot().
 */
function jhumel_print_gallery( string $variant, array $shots ): void {
	echo '<div class="gallery gallery--' . esc_attr( $variant ) . '" data-gallery>';
	foreach ( $shots as $shot ) {
		printf(
			'<a class="gallery__item" href="%1$s" data-caption="%2$s"><img src="%3$s" alt="%4$s" width="%5$d" height="%6$d" loading="lazy" decoding="async"><span class="gallery__label">%7$s</span></a>',
			esc_url( get_theme_file_uri( 'assets/images/' . $shot['full'] ) ),
			esc_attr( $shot['caption'] ),
			esc_url( get_theme_file_uri( 'assets/images/' . $shot['preview'] ) ),
			esc_attr( $shot['alt'] ),
			(int) $shot['width'],
			(int) $shot['height'],
			esc_html( $shot['label'] )
		);
	}
	echo '</div>';
}

/**
 * Removes the emoji script and styles WordPress adds to every page.
 */
function jhumel_remove_emoji_support(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

/**
 * Turns off comments and trackbacks on posts and pages.
 */
function jhumel_remove_comment_support(): void {
	foreach ( array( 'post', 'page' ) as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}

/**
 * Removes the XML-RPC methods that receive pingbacks.
 *
 * @param array<string, callable> $methods XML-RPC methods.
 * @return array<string, callable>
 */
function jhumel_remove_pingback_methods( array $methods ): array {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
}

/**
 * Stops advertising the pingback endpoint in response headers.
 *
 * @param array<string, string> $headers Response headers.
 * @return array<string, string>
 */
function jhumel_remove_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}

/**
 * Prints the meta description and Open Graph tags from the site title and tagline.
 */
function jhumel_output_meta_tags(): void {
	$description = get_bloginfo( 'description' );
	$page_url    = is_singular() ? get_permalink() : home_url( '/' );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	echo '<meta property="og:type" content="website">' . "\n";
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $page_url ) );
}
