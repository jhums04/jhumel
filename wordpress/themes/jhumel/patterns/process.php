<?php
/**
 * Title: How we'd work together
 * Slug: jhumel/process
 * Categories: jhumel
 * Inserter: no
 *
 * Answers what a client worries about before hiring: what it costs, whether they stay in the loop, what they get
 * at the end, and who helps afterwards. Four steps, each with the one thing the client walks away with.
 *
 * @package Jhumel
 */

$jhumel_process_steps = array(
	array(
		'title'   => __( 'A call and a fixed quote', 'jhumel' ),
		'text'    => __( 'Tell me what is slowing the business down. I reply within one business day, and before any work starts you get a written plan with a fixed price.', 'jhumel' ),
		'outcome' => __( 'A plan and a price, in writing', 'jhumel' ),
	),
	array(
		'title'   => __( 'Progress you can click', 'jhumel' ),
		'text'    => __( 'I build in small pieces and send you a working link every few days with a plain-English update, so you can steer early instead of waiting for a big reveal.', 'jhumel' ),
		'outcome' => __( 'A working link every few days', 'jhumel' ),
	),
	array(
		'title'   => __( 'Launch and handover', 'jhumel' ),
		'text'    => __( 'I deploy it, set up the domain and HTTPS, and hand over the code, the logins, and a short guide your team can follow.', 'jhumel' ),
		'outcome' => __( 'Everything, in your name', 'jhumel' ),
	),
	array(
		'title'   => __( 'Support after launch', 'jhumel' ),
		'text'    => __( 'Need a change or a fix later? I am a message away. I set up backups and error alerts, so most problems reach me before they reach you.', 'jhumel' ),
		'outcome' => __( 'Someone who already knows your system', 'jhumel' ),
	),
);
?>
<!-- wp:group {"tagName":"section","anchor":"process","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<section id="process" class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">How we’d work <em class="accent-underline">together</em></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">I keep things practical: understand what you are trying to accomplish, choose the simplest approach that will hold up, and get a working version in front of you early. At every step you know what is being built, what it costs, and when it lands.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<ol class="process-steps alignwide">
		<?php foreach ( $jhumel_process_steps as $jhumel_process_step ) : ?>
			<li class="process-steps__item">
				<h3 class="process-steps__title"><?php echo esc_html( $jhumel_process_step['title'] ); ?></h3>
				<p class="process-steps__text"><?php echo esc_html( $jhumel_process_step['text'] ); ?></p>
				<p class="process-steps__outcome"><?php echo esc_html( $jhumel_process_step['outcome'] ); ?></p>
			</li>
		<?php endforeach; ?>
	</ol>

	<ul class="promise-strip alignwide">
		<li><?php esc_html_e( 'Fixed price before any work starts', 'jhumel' ); ?></li>
		<li><?php esc_html_e( 'You own the code and every account', 'jhumel' ); ?></li>
		<li><?php esc_html_e( 'Plain-English updates every few days', 'jhumel' ); ?></li>
	</ul>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
