<?php
/**
 * Title: Work
 * Slug: jhumel/work
 * Categories: jhumel
 * Inserter: no
 *
 * The navy band: what I build for businesses, shown with real screenshots. Ordered for small and medium
 * businesses, so the dashboard comes first and the workflows behind it second.
 *
 * @package Jhumel
 */

/**
 * Prints one screenshot as a core Image block in a white frame, with a caption.
 *
 * @param string $file    File name in assets/images.
 * @param int    $width   Width in pixels, so the browser reserves the space before the image loads.
 * @param int    $height  Height in pixels.
 * @param string $alt     What the screenshot shows, for screen readers.
 * @param string $caption What it does for the business.
 */
$jhumel_print_screenshot = static function ( string $file, int $width, int $height, string $alt, string $caption ): void {
	?>
	<!-- wp:image {"sizeSlug":"full","className":"screenshot-frame"} -->
	<figure class="wp-block-image size-full screenshot-frame"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $file ) ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="<?php echo esc_attr( (string) $width ); ?>" height="<?php echo esc_attr( (string) $height ); ?>" loading="lazy" decoding="async"/><figcaption class="wp-element-caption"><?php echo esc_html( $caption ); ?></figcaption></figure>
	<!-- /wp:image -->
	<?php
};
?>
<!-- wp:group {"tagName":"section","anchor":"work","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}},"elements":{"link":{"color":{"text":"var:preset|color|sky"},":hover":{"color":{"text":"var:preset|color|white"}}},"heading":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"navy","textColor":"navy-muted","className":"band-dark","layout":{"type":"constrained"}} -->
<section id="work" class="wp-block-group alignfull band-dark has-navy-muted-color has-navy-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Selected work</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">What I build for businesses, shown with the real thing.</p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|50"}}}} -->
		<div class="wp-block-columns alignwide are-vertically-aligned-center" style="margin-top:var(--wp--preset--spacing--50)">
			<!-- wp:column {"verticalAlignment":"center","width":"36%"} -->
			<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:36%">
				<!-- wp:paragraph {"className":"pill pill--dark"} -->
				<p class="pill pill--dark"><?php esc_html_e( 'Lead system', 'jhumel' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":3,"fontSize":"large"} -->
				<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'A lead system that answers in seconds', 'jhumel' ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'A quote form that prices instantly, AI that scores every inquiry and drafts your reply, a phone alert within seconds, and one screen that shows who to call first.', 'jhumel' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'If any step fails, the lead is kept and retried, and I get alerted. No lead gets lost.', 'jhumel' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size"><?php esc_html_e( 'Built with WordPress, n8n, Supabase, and React.', 'jhumel' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
				<p style="font-weight:600"><a href="#demo"><?php esc_html_e( 'Try the live demo', 'jhumel' ); ?></a> · <a href="<?php echo esc_url( 'https://github.com/jhums04/jhumel' ); ?>"><?php esc_html_e( 'See the code', 'jhumel' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"center","width":"64%"} -->
			<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:64%">
				<?php
				$jhumel_print_screenshot(
					'dashboard.webp',
					2160,
					1097,
					__( 'The lead dashboard: leads today, open pipeline value, conversion rate, and response time above a board with columns for New, Contacted, Proposal sent, Won, and Lost.', 'jhumel' ),
					__( 'The dashboard: every lead with its AI score, and the whole pipeline on one screen.', 'jhumel' )
				);
				?>
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","justifyContent":"left"}} -->
		<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--60)">
			<!-- wp:paragraph {"className":"pill pill--dark"} -->
			<p class="pill pill--dark"><?php esc_html_e( 'Automation', 'jhumel' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":3,"fontSize":"large"} -->
			<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Automations that run themselves', 'jhumel' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Behind the dashboard, automated workflows do the busywork your team would otherwise do by hand: saving each lead, scoring it, sending replies, and watching for problems. They are built in n8n, so you can see every step, and you own them outright.', 'jhumel' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
			<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--30)">
				<?php
				// Full width: this workflow is long, and its step names need the room to stay readable.
				$jhumel_print_screenshot(
					'workflow-send-follow-up.webp',
					1370,
					350,
					__( 'The Send follow-up workflow in n8n: it checks who is signed in, validates the reply, finds the lead, sends the email unless it is a demo lead, logs it, and confirms.', 'jhumel' ),
					__( 'Send follow-up: one click in the dashboard sends your reply, logs it, and moves the lead to Contacted. It checks who is signed in first, so nobody can send on your behalf.', 'jhumel' )
				);
				$jhumel_print_screenshot(
					'workflow-error-alert.webp',
					780,
					210,
					__( 'The Error alert workflow in n8n: when any workflow fails, it prepares a message and sends it on Telegram.', 'jhumel' ),
					__( 'Error alert: if any step fails, I get a Telegram message saying what broke, often before you notice.', 'jhumel' )
				);
				?>
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
