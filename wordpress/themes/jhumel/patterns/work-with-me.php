<?php
/**
 * Title: Work with me
 * Slug: jhumel/work-with-me
 * Categories: jhumel
 * Inserter: no
 *
 * A PHP pattern because patterns are not passed through the shortcode filter, so the form is rendered here.
 *
 * @package Jhumel
 */

?>
<!-- wp:group {"tagName":"section","anchor":"contact","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}},"elements":{"link":{"color":{"text":"var:preset|color|sky"},":hover":{"color":{"text":"var:preset|color|white"}}},"heading":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"navy","textColor":"navy-muted","className":"band-dark","layout":{"type":"constrained"}} -->
<section id="contact" class="wp-block-group alignfull band-dark has-navy-muted-color has-navy-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50","top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"40%","className":"sticky-intro"} -->
		<div class="wp-block-column sticky-intro" style="flex-basis:40%">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Let's build what your business <em class="accent-underline">needs</em></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Tell me what's eating your team's time. I'll tell you honestly what I'd build, how long it would take, and what it would cost. No sales call needed to get a straight answer.</p>
			<!-- /wp:paragraph -->

			<!-- wp:list {"className":"check-list check-list--dark"} -->
			<ul class="wp-block-list check-list check-list--dark">
				<!-- wp:list-item -->
				<li>Systems, CRMs, and internal tools</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>Automations and AI that remove manual work</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>Integrations that make your tools share data</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>Websites, web apps, and client portals</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>Dashboards and reporting</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>Solution design, rescues, and audits</li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size">A fixed quote before any work starts, updates every few days in plain English, and clean code you own outright.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>I reply within one business day. Prefer email? <a href="<?php echo esc_url( 'mailto:jbonganciso04@gmail.com' ); ?>">jbonganciso04@gmail.com</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"60%"} -->
		<div class="wp-block-column" style="flex-basis:60%">
			<!-- wp:group {"style":{"border":{"radius":"16px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"shadow":"var:preset|shadow|soft","elements":{"link":{"color":{"text":"var:preset|color|navy"}}}},"backgroundColor":"white","textColor":"black","className":"form-card","layout":{"type":"default"}} -->
			<div class="wp-block-group form-card has-black-color has-white-background-color has-text-color has-background has-link-color" style="border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);box-shadow:var(--wp--preset--shadow--soft)">
			<!-- wp:html -->
			<?php
			echo do_shortcode( '[quote_form organization="jhumel" require_message="yes"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the shortcode escapes its own output.
			?>
			<!-- /wp:html -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
