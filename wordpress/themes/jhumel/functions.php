<?php
/**
 * Theme setup: pattern category, cleanup, and meta tags.
 *
 * @package Jhumel
 */

add_action( 'init', 'jhumel_register_pattern_category' );
add_action( 'wp_enqueue_scripts', 'jhumel_enqueue_stylesheet' );
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
