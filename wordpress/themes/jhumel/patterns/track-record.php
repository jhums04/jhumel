<?php
/**
 * Title: Track record
 * Slug: jhumel/track-record
 * Categories: jhumel
 * Inserter: no
 *
 * Five years of internal systems before freelancing, written as business outcomes.
 *
 * @package Jhumel
 */

$jhumel_banking_results = array(
	array(
		'result'  => __( 'Ops, simplified', 'jhumel' ),
		'title'   => __( 'Made operations teams faster and more accurate', 'jhumel' ),
		'summary' => __( 'I built the CRM screens and workflows that bank and insurer operations staff use all day: fewer manual steps and less copy-pasting, checks that catch mistakes before they spread, and views that show what needs attention next. Requests got handled faster, with fewer errors.', 'jhumel' ),
	),
	array(
		'result'  => __( '12s → 1s', 'jhumel' ),
		'title'   => __( 'Made a slow banking service fast', 'jhumel' ),
		'summary' => __( 'Customers were waiting 12 to 15 seconds for a banking service to respond. I found the cause and cut the wait to about 1 second.', 'jhumel' ),
	),
	array(
		'result'  => __( 'Months-old', 'jhumel' ),
		'title'   => __( 'Solved a bug nobody could find', 'jhumel' ),
		'summary' => __( 'An error had been live in production for months. I traced it through the code, found the cause, and shipped the fix.', 'jhumel' ),
	),
	array(
		'result'  => __( '24/7', 'jhumel' ),
		'title'   => __( 'Kept a major bank running', 'jhumel' ),
		'summary' => __( 'I helped keep about 200 core banking services running for a major Philippine bank, including night shifts when things broke.', 'jhumel' ),
	),
);
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading"><?php esc_html_e( 'Before freelancing: five years of internal systems', 'jhumel' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size"><?php esc_html_e( 'Mostly for banks and insurers, where "it mostly works" is not good enough. Client names stay private.', 'jhumel' ); ?></p>
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
