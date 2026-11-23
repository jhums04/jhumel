<?php
/**
 * Title: Track record
 * Slug: jhumel/track-record
 * Categories: jhumel
 * Inserter: no
 *
 * Enterprise engineering highlights from the CV, written as outcomes a business owner understands.
 *
 * @package Jhumel
 */

$jhumel_banking_results = array(
	array(
		'result'  => __( '~50', 'jhumel' ),
		'title'   => __( 'APIs and integrations built', 'jhumel' ),
		'summary' => __( 'Connected core banking and vendor systems end to end: implementation, automated tests, deployment, and QA sign-off.', 'jhumel' ),
	),
	array(
		'result'  => __( '12s → 1s', 'jhumel' ),
		'title'   => __( 'Performance where it counts', 'jhumel' ),
		'summary' => __( 'Cut a banking service\'s response time from 12 seconds to about 1 by caching its login tokens safely, verified in production.', 'jhumel' ),
	),
	array(
		'result'  => __( '~200', 'jhumel' ),
		'title'   => __( 'Production services supported', 'jhumel' ),
		'summary' => __( 'On call for about 200 core banking services at a major Philippine bank: finding the real cause fast and shipping tested fixes.', 'jhumel' ),
	),
	array(
		'result'  => __( '1,000+', 'jhumel' ),
		'title'   => __( 'Advisors on the platform', 'jhumel' ),
		'summary' => __( 'Designing and delivering features for a US bank\'s investment platform used by more than 1,000 registered advisors.', 'jhumel' ),
	),
);
?>
<!-- wp:group {"tagName":"section","anchor":"experience","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"backgroundColor":"amber-50","layout":{"type":"constrained"}} -->
<section id="experience" class="wp-block-group alignfull has-amber-50-background-color has-background" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading"><?php esc_html_e( 'Five years of enterprise engineering', 'jhumel' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size"><?php esc_html_e( 'My day job is building and running software for banks and insurers in the US and the Philippines, where downtime and mistakes cost real money. Your project gets the same care. Employer names stay private.', 'jhumel' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
		<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--40)">
			<?php foreach ( $jhumel_banking_results as $jhumel_banking_result ) : ?>
			<!-- wp:group {"style":{"border":{"top":{"color":"var:preset|color|black","width":"2px"}},"spacing":{"padding":{"top":"var:preset|spacing|20"},"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
			<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--black);border-top-width:2px;padding-top:var(--wp--preset--spacing--20)">
				<!-- wp:paragraph {"className":"result-figure"} -->
				<p class="result-figure"><?php echo esc_html( $jhumel_banking_result['result'] ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":3} -->
				<h3 class="wp-block-heading"><?php echo esc_html( $jhumel_banking_result['title'] ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"textColor":"gray-600"} -->
				<p class="has-gray-600-color has-text-color"><?php echo esc_html( $jhumel_banking_result['summary'] ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
