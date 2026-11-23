<?php
/**
 * Title: Automations
 * Slug: jhumel/automations
 * Categories: jhumel
 * Inserter: no
 *
 * The n8n side of the live system, shown with real workflow screenshots. Clients often look for n8n work
 * specifically. No list of example automations: a list reads as a menu and makes the range look narrower.
 *
 * @package Jhumel
 */

/**
 * Prints one screenshot as a framed figure with a caption.
 *
 * @param string $file    File name in assets/images.
 * @param int    $width   Width in pixels, so the browser reserves the space before the image loads.
 * @param int    $height  Height in pixels.
 * @param string $alt     What the screenshot shows, for screen readers.
 * @param string $caption What it does for the business.
 */
$jhumel_print_workflow = static function ( string $file, int $width, int $height, string $alt, string $caption ): void {
	printf(
		'<figure class="wp-block-image size-full screenshot-frame"><img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" loading="lazy" decoding="async"/><figcaption class="wp-element-caption">%5$s</figcaption></figure>',
		esc_url( get_theme_file_uri( 'assets/images/' . $file ) ),
		esc_attr( $alt ),
		(int) $width,
		(int) $height,
		esc_html( $caption )
	);
};
?>
<!-- wp:group {"tagName":"section","anchor":"automations","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}},"elements":{"link":{"color":{"text":"var:preset|color|sky"}},"heading":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"navy","textColor":"navy-muted","className":"band-dark","layout":{"type":"constrained"}} -->
<section id="automations" class="wp-block-group alignfull band-dark has-navy-muted-color has-navy-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Automations that do the <em class="accent-underline">busywork</em></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">I build automations in n8n, a visual workflow tool. You can see every step, change it later, and you own it outright. If your team does something by hand more than once a week, it can probably be automated. Here are two that run the system above.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:html -->
		<?php
		$jhumel_print_workflow(
			'workflow-send-follow-up.webp',
			1370,
			350,
			__( 'The Send follow-up workflow in n8n: it checks who is signed in, validates the reply, finds the lead, sends the email unless it is a demo lead, logs it, and confirms.', 'jhumel' ),
			__( 'Send follow-up: one click in the dashboard sends your reply, logs it, and moves the lead to Contacted. It checks who is signed in first, so nobody can send on your behalf.', 'jhumel' )
		);
		$jhumel_print_workflow(
			'workflow-error-alert.webp',
			780,
			210,
			__( 'The Error alert workflow in n8n: when any workflow fails, it prepares a message and sends it on Telegram.', 'jhumel' ),
			__( 'Error alert: if any step fails, I get a Telegram message saying what broke, often before you notice.', 'jhumel' )
		);
		?>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
