<?php
/**
 * Title: Hero
 * Slug: jhumel/hero
 * Categories: jhumel
 * Inserter: no
 *
 * The pitch on the left; on the right, real screenshots of both live projects layered together. They are
 * decorative here (each appears with a full description in the Work section), so screen readers skip them.
 *
 * @package Jhumel
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|section"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull hero" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"50%","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
			<!-- wp:heading {"level":1} -->
			<h1 class="wp-block-heading">I build the systems your business <em class="accent-underline">runs on</em>.</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"fontSize":"medium"} -->
			<p class="has-medium-font-size">I'm Jhumel, a full-stack developer. Websites, web apps, dashboards, automations, and the integrations that tie your tools together: I design them, build them end to end, and keep them running. Two of them are live below. Try them.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
			<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--20)">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#work">See my work</a></div>
				<!-- /wp:button -->

				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#contact">Start a project</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"className":"trust-row","textColor":"gray-600","fontSize":"small"} -->
			<p class="trust-row has-gray-600-color has-text-color has-small-font-size"><span>2 live systems you can try</span><span>5 years building software businesses rely on</span><span>Replies within one business day</span></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
			<!-- wp:html -->
			<div class="hero-showcase" aria-hidden="true">
				<figure class="hero-showcase__desktop">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/lead-engine-board.webp' ) ); ?>" alt="" width="1440" height="900" fetchpriority="high" decoding="async">
					<figcaption>Lead Engine</figcaption>
				</figure>
				<figure class="hero-showcase__phone">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/serviceops-technician-thumb.webp' ) ); ?>" alt="" width="360" height="780" decoding="async">
					<figcaption>ServiceOps</figcaption>
				</figure>
			</div>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
