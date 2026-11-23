<?php
/**
 * Title: How it works
 * Slug: jhumel/how-it-works
 * Categories: jhumel
 * Inserter: no
 *
 * The whole system as one pipeline, from the form to the follow-up email. Each step pairs a short explanation
 * with a visual: real screenshots where they exist, and small HTML recreations (a form, the signed request, the
 * workflow, the AI result, the Telegram alert) where a screenshot would be unreadable or show private data.
 *
 * @package Jhumel
 */

/**
 * Prints one screenshot as a core Image block in a frame, with a caption.
 *
 * @param string $file    File name in assets/images.
 * @param int    $width   Width in pixels, so the browser reserves the space before the image loads.
 * @param int    $height  Height in pixels.
 * @param string $alt     What the screenshot shows, for screen readers.
 * @param string $caption Short caption under the image.
 */
$jhumel_screenshot = static function ( string $file, int $width, int $height, string $alt, string $caption ): string {
	return sprintf(
		'<figure class="wp-block-image size-full screenshot-frame"><img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" loading="lazy" decoding="async"/><figcaption class="wp-element-caption">%5$s</figcaption></figure>',
		esc_url( get_theme_file_uri( 'assets/images/' . $file ) ),
		esc_attr( $alt ),
		$width,
		$height,
		esc_html( $caption )
	);
};

$jhumel_steps = array(
	array(
		'title'  => __( 'A visitor asks for a quote', 'jhumel' ),
		'text'   => __( 'The form prices the request instantly as they pick services, and a spam check runs quietly in the background.', 'jhumel' ),
		'tools'  => array( 'WordPress plugin', 'PHP' ),
		'visual' => '<div class="mini-form" aria-hidden="true">
			<p class="mini-form__label">What can I help with?</p>
			<div class="mini-form__chips"><span class="is-selected">Lead system</span><span>Automation</span><span class="is-selected">Web app</span></div>
			<div class="mini-form__field"><span>Name</span><strong>Maria Santos</strong></div>
			<div class="mini-form__estimate">Starting at <strong>$3,500</strong></div>
			<span class="mini-form__button">Send request →</span>
		</div>',
	),
	array(
		'title'  => __( 'WordPress saves it first, then signs and sends it', 'jhumel' ),
		'text'   => __( 'The lead is stored on the website before anything else, so nothing is lost if a later step is down. It then goes to n8n with a cryptographic signature, so n8n knows exactly where it came from.', 'jhumel' ),
		'tools'  => array( 'WordPress plugin', 'HMAC-SHA256' ),
		'visual' => '<pre class="code-card" aria-label="' . esc_attr__( 'An example signed request from WordPress to n8n', 'jhumel' ) . '"><span class="code-card__method">POST</span> /webhook/lead-intake
<span class="code-card__key">X-Lead-Timestamp:</span> 1795412233
<span class="code-card__key">X-Lead-Signature:</span> sha256=9f2c41d0…a41e

{ "full_name": "Maria Santos",
  "services": ["lead_system", "supabase_app"],
  "estimated_price": 3500, … }</pre>',
	),
	array(
		'title'  => __( 'n8n checks it and stores it', 'jhumel' ),
		'text'   => __( 'Anything unsigned, stale, or tampered with is rejected. Good leads are saved in the database, and a retried delivery never creates a duplicate.', 'jhumel' ),
		'tools'  => array( 'n8n', 'Supabase Postgres' ),
		'visual' => '<ol class="node-chain" aria-label="' . esc_attr__( 'The lead intake workflow, step by step', 'jhumel' ) . '">
			<li>Receive lead</li>
			<li>Verify signature</li>
			<li>Save lead <small>Supabase</small></li>
			<li>Accept request</li>
			<li>Score with AI</li>
			<li>Notify on Telegram</li>
		</ol>',
	),
	array(
		'title'  => __( 'AI scores it and drafts the reply', 'jhumel' ),
		'text'   => __( 'Every lead gets a High, Medium, or Low score with a one-line reason, plus a first reply written in my voice, ready to edit.', 'jhumel' ),
		'tools'  => array( 'OpenAI', 'Structured output' ),
		'visual' => '<div class="ai-card" aria-hidden="true">
			<div class="ai-card__top"><span class="ai-card__label">AI score</span><span class="ai-card__badge">High</span></div>
			<p class="ai-card__reason">Clear need, budget fits, and they want to start this month.</p>
			<div class="ai-card__draft">
				<span class="ai-card__label">Draft reply</span>
				<p>Hi Maria,</p>
				<p>Losing patients who write in at night is exactly the gap a lead system closes. Could we do a 20-minute call this week?</p>
			</div>
		</div>',
	),
	array(
		'title'  => __( 'My phone buzzes', 'jhumel' ),
		'text'   => __( 'A Telegram alert arrives within seconds with the score, the request, and a link straight to the lead. Demo leads post to a public channel you can watch, first name only.', 'jhumel' ),
		'tools'  => array( 'Telegram' ),
		'visual' => '<div class="tg-chat" aria-hidden="true">
			<div class="tg-chat__header"><span class="tg-chat__avatar">LE</span><div><strong>Lead Engine</strong><span>bot</span></div></div>
			<div class="tg-chat__bubble">
				<p><strong>New lead · High priority</strong></p>
				<p><strong>Maria Santos</strong> · Santos Dental<br>maria@example.com · +63 917 000 0001</p>
				<p><strong>Wants</strong> Lead system, Web app<br><strong>Timeline</strong> Standard · <strong>Estimate</strong> $3,500</p>
				<blockquote>Why High: clear need, budget fits, ready to start this month.</blockquote>
				<p class="tg-chat__link">Open in dashboard →</p>
				<span class="tg-chat__time">9:41</span>
			</div>
		</div>',
	),
	array(
		'title'  => __( 'It appears live on the dashboard', 'jhumel' ),
		'text'   => __( 'No refresh needed. The board shows every lead by stage with its score, value, and age, with today\'s numbers on top. Drag a card to move it, on desktop or phone.', 'jhumel' ),
		'tools'  => array( 'React', 'Supabase Realtime' ),
		'visual' => $jhumel_screenshot(
			'dashboard.webp',
			2160,
			1097,
			__( 'The lead dashboard: leads today, open pipeline value, conversion rate, and response time above a board with columns for New, Contacted, Proposal sent, Won, and Lost.', 'jhumel' ),
			__( 'The live board, with the day\'s numbers on top.', 'jhumel' )
		),
	),
	array(
		'title'  => __( 'One click sends the follow-up', 'jhumel' ),
		'text'   => __( 'Open the lead, tweak the AI draft, and send. The reply is logged on the lead\'s timeline, and the lead moves to Contacted automatically.', 'jhumel' ),
		'tools'  => array( 'React', 'n8n', 'Gmail' ),
		'visual' => $jhumel_screenshot(
			'drawer.webp',
			864,
			1590,
			__( 'The lead drawer for Maria Santos: stage, High score with its reason, contact details, the request, and an editable AI reply draft.', 'jhumel' ),
			__( 'Everything about the lead, and the reply ready to send.', 'jhumel' )
		),
		'narrow' => true,
	),
);
?>
<!-- wp:group {"tagName":"section","anchor":"how-it-works","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<section id="how-it-works" class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">How this one works</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">Follow one lead from the form to the follow-up email. Seven steps, all running on this page right now, built from the same pieces I use for most business workflows: forms, integrations, automations, AI, and dashboards.</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"align":"wide","className":"pipeline","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":"0"}},"layout":{"type":"default"}} -->
		<div class="wp-block-group alignwide pipeline" style="margin-top:var(--wp--preset--spacing--50)">
			<?php foreach ( $jhumel_steps as $jhumel_index => $jhumel_step ) : ?>
			<!-- wp:group {"className":"pipeline-step","layout":{"type":"default"}} -->
			<div class="wp-block-group pipeline-step">
				<!-- wp:paragraph {"className":"pipeline-step__number"} -->
				<p class="pipeline-step__number"><?php echo esc_html( (string) ( $jhumel_index + 1 ) ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|50"}}}} -->
				<div class="wp-block-columns">
					<!-- wp:column {"width":"40%"} -->
					<div class="wp-block-column" style="flex-basis:40%">
						<!-- wp:heading {"level":3,"fontSize":"large"} -->
						<h3 class="wp-block-heading has-large-font-size"><?php echo esc_html( $jhumel_step['title'] ); ?></h3>
						<!-- /wp:heading -->

						<!-- wp:paragraph {"textColor":"gray-600"} -->
						<p class="has-gray-600-color has-text-color"><?php echo esc_html( $jhumel_step['text'] ); ?></p>
						<!-- /wp:paragraph -->

						<!-- wp:paragraph {"className":"tool-tags"} -->
						<p class="tool-tags">
							<?php foreach ( $jhumel_step['tools'] as $jhumel_tool ) : ?>
								<span><?php echo esc_html( $jhumel_tool ); ?></span>
							<?php endforeach; ?>
						</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:column -->

					<!-- wp:column {"width":"60%"} -->
					<div class="wp-block-column pipeline-step__visual<?php echo empty( $jhumel_step['narrow'] ) ? '' : ' pipeline-step__visual--narrow'; ?>" style="flex-basis:60%">
						<!-- wp:html -->
						<?php echo $jhumel_step['visual']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup above; every dynamic value in it is escaped. ?>
						<!-- /wp:html -->
					</div>
					<!-- /wp:column -->
				</div>
				<!-- /wp:columns -->
			</div>
			<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
