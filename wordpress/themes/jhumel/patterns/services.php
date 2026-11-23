<?php
/**
 * Title: Services
 * Slug: jhumel/services
 * Categories: jhumel
 * Inserter: no
 *
 * A PHP pattern because the starting prices come from the quote form plugin's settings,
 * so the prices here and in the form can never disagree.
 *
 * @package Jhumel
 */

$jhumel_services = array(
	'lead_system'      => array(
		'name'        => __( 'Lead systems', 'jhumel' ),
		'description' => __( 'A form that quotes instantly, an AI that sorts hot leads from tire-kickers, and a dashboard that shows who to call first.', 'jhumel' ),
	),
	'automation'       => array(
		'name'        => __( 'Automations with n8n', 'jhumel' ),
		'description' => __( 'If someone on your team copies data from one app to another, I can probably make that disappear.', 'jhumel' ),
	),
	'supabase_app'     => array(
		'name'        => __( 'Web apps with React and Supabase', 'jhumel' ),
		'description' => __( 'Client portals, booking systems, internal tools. Fast, secure, and yours to own.', 'jhumel' ),
	),
	'wordpress_plugin' => array(
		'name'        => __( 'Custom WordPress plugins', 'jhumel' ),
		'description' => __( 'When a plugin from the store almost does what you need, I build the one that does exactly that.', 'jhumel' ),
	),
	'rescue'           => array(
		'name'        => __( 'Rescue and audits', 'jhumel' ),
		'description' => __( "Built your app with AI and something feels off? I'll find the security holes and slow spots before your customers do.", 'jhumel' ),
	),
);

// Starting prices by service key, as saved on the quote form settings page. Empty if the plugin was never set up.
$jhumel_quote_form_settings = get_option( 'quote_form_settings', array() );
$jhumel_prices              = array_column( $jhumel_quote_form_settings['services'] ?? array(), 'price', 'key' );
?>
<!-- wp:group {"tagName":"section","anchor":"services","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"layout":{"type":"constrained"}} -->
<section id="services" class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">What I build</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">Fixed starting prices, so you know the ballpark before we talk.</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"grid","minimumColumnWidth":"19rem"}} -->
		<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--40)">
			<?php foreach ( $jhumel_services as $jhumel_key => $jhumel_service ) : ?>
			<!-- wp:group {"style":{"border":{"radius":"16px","color":"var:preset|color|gray-200","width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|20"}},"backgroundColor":"white","className":"lift-card","layout":{"type":"default"}} -->
			<div class="wp-block-group lift-card has-border-color has-white-background-color has-background" style="border-color:var(--wp--preset--color--gray-200);border-width:1px;border-radius:16px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
				<?php if ( isset( $jhumel_prices[ $jhumel_key ] ) ) : ?>
				<!-- wp:paragraph {"className":"pill"} -->
				<p class="pill">
					<?php
					/* translators: %s: starting price in US dollars, for example 2,000. */
					echo esc_html( sprintf( __( 'From $%s', 'jhumel' ), number_format_i18n( $jhumel_prices[ $jhumel_key ] ) ) );
					?>
				</p>
				<!-- /wp:paragraph -->
				<?php endif; ?>

				<!-- wp:heading {"level":3} -->
				<h3 class="wp-block-heading"><?php echo esc_html( $jhumel_service['name'] ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"textColor":"gray-600"} -->
				<p class="has-gray-600-color has-text-color"><?php echo esc_html( $jhumel_service['description'] ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<?php endforeach; ?>

			<!-- wp:group {"style":{"border":{"radius":"16px"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|20"},"elements":{"link":{"color":{"text":"var:preset|color|navy"}}}},"backgroundColor":"amber-50","textColor":"black","className":"lift-card","layout":{"type":"default"}} -->
			<div class="wp-block-group lift-card has-black-color has-amber-50-background-color has-text-color has-background has-link-color" style="border-radius:16px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
				<!-- wp:heading {"level":3} -->
				<h3 class="wp-block-heading"><?php esc_html_e( 'Not sure which one fits?', 'jhumel' ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php esc_html_e( "Tell me what's eating your team's time, and I'll tell you honestly what I'd build and what it would cost.", 'jhumel' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
				<p style="font-weight:600"><a href="#contact"><?php esc_html_e( 'Start a project →', 'jhumel' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
