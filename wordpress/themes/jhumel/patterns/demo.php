<?php
/**
 * Title: Live demo
 * Slug: jhumel/demo
 * Categories: jhumel
 * Inserter: no
 *
 * A PHP pattern because the dashboard address and the demo password differ between local and production
 * (JHUMEL_DASHBOARD_URL and JHUMEL_DEMO_PASSWORD in wp-config.php).
 *
 * @package Jhumel
 */

$jhumel_dashboard_url = defined( 'JHUMEL_DASHBOARD_URL' ) ? JHUMEL_DASHBOARD_URL : 'https://app.jhumel.dev';
$jhumel_demo_password = defined( 'JHUMEL_DEMO_PASSWORD' ) ? JHUMEL_DEMO_PASSWORD : 'demo-password';
?>
<!-- wp:group {"tagName":"section","anchor":"demo","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"backgroundColor":"gray-50","layout":{"type":"constrained"}} -->
<section id="demo" class="wp-block-group alignfull has-gray-50-background-color has-background" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50","top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"40%","className":"sticky-intro"} -->
		<div class="wp-block-column sticky-intro" style="flex-basis:40%">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">See one of my systems <em class="accent-underline">live</em></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Don't take my word for it. This is a lead-to-CRM system I built end to end, running for real. The same building blocks work for orders, bookings, support requests, and client onboarding.</p>
			<!-- /wp:paragraph -->

			<!-- wp:list {"ordered":true,"className":"numbered-steps"} -->
			<ol class="wp-block-list numbered-steps">
				<!-- wp:list-item -->
				<li>Send the form with any name and a test email.</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>AI scores your lead and drafts a reply, and an alert lands in a <a href="https://t.me/s/jhumelbuilds_demo">public Telegram channel</a> you can watch.</li>
				<!-- /wp:list-item -->

				<!-- wp:list-item -->
				<li>Log in to the dashboard below, find your lead, and drag it through the pipeline.</li>
				<!-- /wp:list-item -->
			</ol>
			<!-- /wp:list -->

			<!-- wp:group {"style":{"border":{"color":"var:preset|color|gray-200","width":"1px","radius":"12px"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"},"blockGap":"var:preset|spacing|10"}},"backgroundColor":"white","layout":{"type":"default"}} -->
			<div class="wp-block-group has-border-color has-white-background-color has-background" style="border-color:var(--wp--preset--color--gray-200);border-width:1px;border-radius:12px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size"><strong>Demo login</strong></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"fontSize":"small","fontFamily":"mono"} -->
				<p class="has-mono-font-family has-small-font-size">Email: demo@jhumel.dev<br>Password: <?php echo esc_html( $jhumel_demo_password ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $jhumel_dashboard_url ); ?>">Open the dashboard</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"textColor":"gray-600","fontSize":"small"} -->
			<p class="has-gray-600-color has-text-color has-small-font-size">Demo leads post to the public channel with your first name only. The demo resets every night.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"60%"} -->
		<div class="wp-block-column" style="flex-basis:60%">
			<!-- wp:group {"style":{"border":{"radius":"16px","color":"var:preset|color|gray-200","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"shadow":"var:preset|shadow|soft"},"backgroundColor":"white","className":"form-card","layout":{"type":"default"}} -->
			<div class="wp-block-group form-card has-border-color has-white-background-color has-background" style="border-color:var(--wp--preset--color--gray-200);border-width:1px;border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);box-shadow:var(--wp--preset--shadow--soft)">
			<!-- wp:html -->
			<?php
			// Patterns are not passed through the shortcode filter, so the form is rendered here directly.
			echo do_shortcode( '[quote_form organization="demo" success_message="Your lead is in. Watch the Telegram channel or log into the dashboard: it should appear in about five seconds."]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the shortcode escapes its own output.
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
