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

		<!-- wp:html -->
		<ul class="service-list alignwide">
			<?php foreach ( $jhumel_services as $jhumel_key => $jhumel_service ) : ?>
				<li class="service-list__item">
					<div>
						<h3 class="service-list__name"><?php echo esc_html( $jhumel_service['name'] ); ?></h3>
						<?php if ( isset( $jhumel_prices[ $jhumel_key ] ) ) : ?>
							<p class="service-list__price">
								<?php
								/* translators: %s: starting price in US dollars, for example 2,000. */
								echo esc_html( sprintf( __( 'From $%s', 'jhumel' ), number_format_i18n( $jhumel_prices[ $jhumel_key ] ) ) );
								?>
							</p>
						<?php endif; ?>
					</div>
					<p class="service-list__description"><?php echo esc_html( $jhumel_service['description'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
		<!-- /wp:html -->

		<!-- wp:paragraph -->
		<p>Not sure which one fits? <a href="#contact">Tell me what's eating your team's time</a> and I'll tell you honestly what I'd do.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
