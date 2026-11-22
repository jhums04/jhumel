<?php
/**
 * Title: Footer
 * Slug: jhumel/footer
 * Categories: jhumel
 * Inserter: no
 *
 * A PHP pattern because the footer needs the current year and the privacy page URL.
 *
 * @package Jhumel
 */

$jhumel_privacy_url = get_privacy_policy_url();
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"textColor":"gray-600","fontSize":"small","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-gray-600-color has-text-color has-small-font-size" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:paragraph -->
		<p>Based in Cavite, Philippines. Working with clients worldwide. &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Jhumel Bonganciso</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph -->
			<p><a href="<?php echo esc_url( 'mailto:jbonganciso04@gmail.com' ); ?>">jbonganciso04@gmail.com</a></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p><a href="<?php echo esc_url( 'https://github.com/jhums04' ); ?>">GitHub</a></p>
			<!-- /wp:paragraph -->
			<?php if ( $jhumel_privacy_url ) : ?>

			<!-- wp:paragraph -->
			<p><a href="<?php echo esc_url( $jhumel_privacy_url ); ?>">Privacy policy</a></p>
			<!-- /wp:paragraph -->
			<?php endif; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
