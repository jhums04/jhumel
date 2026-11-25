<?php
/**
 * Title: Work
 * Slug: jhumel/work
 * Categories: jhumel
 * Inserter: no
 *
 * The portfolio: Lead Engine, ServiceOps, then automations, each in its own card with a screenshot gallery. Every screenshot is
 * a link to its full-size image, which assets/js/gallery.js opens in the shared viewer at the end.
 *
 * Lead Engine's demo opens in a panel under its case study (assets/js/demo-panel.js): the steps and the demo
 * login on one side, the demo form on the other. A PHP pattern because the dashboard address and the demo password differ between local and production
 * (JHUMEL_DASHBOARD_URL, JHUMEL_DEMO_EMAIL, and JHUMEL_DEMO_PASSWORD in wp-config.php).
 *
 * @package Jhumel
 */

$jhumel_dashboard_url = defined( 'JHUMEL_DASHBOARD_URL' ) ? JHUMEL_DASHBOARD_URL : 'https://app.bongancisojhumel.com';
$jhumel_demo_email    = defined( 'JHUMEL_DEMO_EMAIL' ) ? JHUMEL_DEMO_EMAIL : 'demo@jhumel.dev';
$jhumel_demo_password = defined( 'JHUMEL_DEMO_PASSWORD' ) ? JHUMEL_DEMO_PASSWORD : 'demo-password';

$jhumel_lead_engine_shots = array(
	jhumel_screenshot(
		'lead-engine-board.webp',
		1440,
		900,
		'lead-engine-board.webp',
		__( 'Pipeline', 'jhumel' ),
		__( 'The pipeline: today’s leads, open pipeline value, conversion rate, and response time above columns for New, Contacted, Proposal sent, Won, and Lost.', 'jhumel' ),
		__( 'The live pipeline: every lead by stage with its score and value, and the numbers that matter on top. New leads appear without a refresh.', 'jhumel' )
	),
	jhumel_screenshot(
		'lead-engine-drawer-thumb.webp',
		480,
		300,
		'lead-engine-drawer.webp',
		__( 'Lead details', 'jhumel' ),
		__( 'The pipeline with Maria Santos’s lead open beside it: a High score, the AI’s reason for it, her contact details, and her request.', 'jhumel' ),
		__( 'Open any lead to see why the AI scored it the way it did, with everything the visitor sent.', 'jhumel' )
	),
	jhumel_screenshot(
		'lead-engine-reply-thumb.webp',
		480,
		300,
		'lead-engine-reply.webp',
		__( 'AI reply draft', 'jhumel' ),
		__( 'The reply draft for Maria Santos, written by the AI, with a subject, a message, and a Send reply button.', 'jhumel' ),
		__( 'A first reply is already drafted. Edit it if you like, then send it in one click; it is logged on the lead’s history.', 'jhumel' )
	),
	jhumel_screenshot(
		'lead-engine-phone-thumb.webp',
		360,
		780,
		'lead-engine-phone.webp',
		__( 'On a phone', 'jhumel' ),
		__( 'The pipeline on a phone: the four numbers in a grid, then the New column with lead cards.', 'jhumel' ),
		__( 'The same dashboard on a phone, so the owner can act on a lead from anywhere.', 'jhumel' )
	),
	jhumel_screenshot(
		'lead-engine-form-thumb.webp',
		480,
		284,
		'lead-engine-form.webp',
		__( 'Quote form', 'jhumel' ),
		__( 'The quote form on the website with two services ticked and a live estimate of 2,400 dollars.', 'jhumel' ),
		__( 'Where it starts: the quote form on the website prices the request as the visitor picks services.', 'jhumel' )
	),
);

$jhumel_automation_shots = array(
	jhumel_screenshot(
		'workflow-send-follow-up.webp',
		1370,
		350,
		'workflow-send-follow-up.webp',
		__( 'Send follow-up', 'jhumel' ),
		__( 'The Send follow-up workflow in n8n: it checks who is signed in, validates the reply, finds the lead, sends the email unless it is a demo lead, logs it, and confirms.', 'jhumel' ),
		__( 'Send follow-up: one click in the dashboard sends your reply, logs it, and moves the lead to Contacted. It checks who is signed in first, so nobody can send on your behalf.', 'jhumel' )
	),
	jhumel_screenshot(
		'workflow-error-alert.webp',
		780,
		210,
		'workflow-error-alert.webp',
		__( 'Error alert', 'jhumel' ),
		__( 'The Error alert workflow in n8n: when any workflow fails, it prepares a message and sends it on Telegram.', 'jhumel' ),
		__( 'Error alert: if any step fails, I get a Telegram message saying what broke, often before you notice.', 'jhumel' )
	),
);

$jhumel_serviceops_shots = array(
	jhumel_screenshot(
		'serviceops-dashboard.webp',
		1440,
		900,
		'serviceops-dashboard.webp',
		__( 'Dashboard', 'jhumel' ),
		__( 'The ServiceOps dashboard: revenue received, outstanding invoices, upcoming and completed jobs, a revenue chart, and recent activity.', 'jhumel' ),
		__( 'The owner’s dashboard: money in, money owed, and the week’s jobs on one screen.', 'jhumel' )
	),
	jhumel_screenshot(
		'serviceops-calendar-thumb.webp',
		480,
		300,
		'serviceops-calendar.webp',
		__( 'Calendar', 'jhumel' ),
		__( 'A week calendar of jobs, each showing the service and the assigned technicians.', 'jhumel' ),
		__( 'The week calendar. A technician cannot be booked on two jobs at once: the database itself refuses it.', 'jhumel' )
	),
	jhumel_screenshot(
		'serviceops-invoice-thumb.webp',
		480,
		300,
		'serviceops-invoice.webp',
		__( 'Invoices', 'jhumel' ),
		__( 'The invoice list with one unpaid invoice open beside it, offering a PDF download and a payment link.', 'jhumel' ),
		__( 'Invoices: download a PDF, or copy a payment link to send to the customer.', 'jhumel' )
	),
	jhumel_screenshot(
		'serviceops-technician-thumb.webp',
		360,
		780,
		'serviceops-technician.webp',
		__( 'Technician app', 'jhumel' ),
		__( 'The technician’s phone view: today’s jobs and upcoming jobs, each with a time, a customer, and an address.', 'jhumel' ),
		__( 'The technician’s phone view: today’s jobs with times and addresses, and nothing else to learn.', 'jhumel' )
	),
	jhumel_screenshot(
		'serviceops-payment-thumb.webp',
		360,
		780,
		'serviceops-payment.webp',
		__( 'Payment page', 'jhumel' ),
		__( 'The customer’s payment page for a 125 dollar invoice, with a Pay button and a PDF download.', 'jhumel' ),
		__( 'The customer’s payment page. They pay by card through Stripe, and the invoice marks itself paid.', 'jhumel' )
	),
);
?>
<!-- wp:group {"tagName":"section","anchor":"work","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"backgroundColor":"gray-50","layout":{"type":"constrained"}} -->
<section id="work" class="wp-block-group alignfull has-gray-50-background-color has-background" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Systems I’ve built and <em class="accent-underline">run</em></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">Two complete systems and the automations behind them, all live. Each one takes a minute to try, and none needs a sign-up.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"tagName":"article","anchor":"lead-engine","align":"wide","className":"project-card","layout":{"type":"default"}} -->
	<article id="lead-engine" class="wp-block-group alignwide project-card">
		<!-- wp:html -->
		<header class="project-card__header">
			<div>
				<h3 class="project-card__title">Lead Engine</h3>
				<p class="project-card__summary"><?php esc_html_e( 'A lead-to-CRM system for service businesses', 'jhumel' ); ?></p>
			</div>
			<p class="project-card__meta"><span class="project-card__live"><?php esc_html_e( 'Live', 'jhumel' ); ?></span><span><?php esc_html_e( '2026', 'jhumel' ); ?></span><span><?php esc_html_e( 'Designed, built, and run solo', 'jhumel' ); ?></span></p>
		</header>
		<!-- /wp:html -->

		<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50","top":"var:preset|spacing|40"}}}} -->
		<div class="wp-block-columns">
			<!-- wp:column {"width":"40%"} -->
			<div class="wp-block-column" style="flex-basis:40%">
				<!-- wp:paragraph -->
				<p>For businesses that cannot afford to miss an enquiry. A visitor fills in a quote form, and seconds later the lead is scored, on the owner’s phone, and on a live dashboard.</p>
				<!-- /wp:paragraph -->

				<!-- wp:list {"className":"check-list"} -->
				<ul class="wp-block-list check-list">
					<!-- wp:list-item -->
					<li>On the dashboard in about half a second, with no refresh, after a spam check and a signature check on arrival.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>AI scores every lead High, Medium, or Low with a one-line reason, and drafts a first reply, in under 4 seconds.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>A Telegram alert lands on the owner’s phone, and the reply goes out from the dashboard in one click.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Runs on its own server with HTTPS, nightly backups, and automated checks on every change.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->

				<!-- wp:paragraph {"className":"tool-tags"} -->
				<p class="tool-tags"><span>WordPress</span><span>PHP</span><span>n8n</span><span>OpenAI API</span><span>React</span><span>Supabase</span></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"tagName":"button","className":"demo-opener"} -->
					<div class="wp-block-button demo-opener" hidden><button type="button" class="wp-block-button__link wp-element-button" aria-expanded="false" aria-controls="demo" data-open-demo><?php esc_html_e( 'Try it live', 'jhumel' ); ?></button></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $jhumel_dashboard_url ); ?>"><?php esc_html_e( 'Open the dashboard', 'jhumel' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

				<!-- wp:paragraph {"textColor":"gray-600","fontSize":"small"} -->
				<p class="has-gray-600-color has-text-color has-small-font-size">
					<?php
					/* translators: %s: link to the code on GitHub. */
					printf( esc_html__( 'Send a test lead and watch it arrive. The code is on %s.', 'jhumel' ), '<a href="https://github.com/jhums04/jhumel">GitHub</a>' );
					?>
				</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"60%"} -->
			<div class="wp-block-column" style="flex-basis:60%">
				<!-- wp:html -->
				<?php jhumel_print_gallery( 'showcase', $jhumel_lead_engine_shots ); ?>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:html -->
		<div id="demo" class="demo-panel" role="region" aria-labelledby="demo-panel-title" tabindex="-1" hidden data-demo-panel>
			<div class="demo-panel__header">
				<h4 class="demo-panel__title" id="demo-panel-title"><?php esc_html_e( 'Try Lead Engine', 'jhumel' ); ?></h4>
				<button class="text-button" type="button" data-close-demo><?php esc_html_e( 'Close the demo', 'jhumel' ); ?></button>
			</div>
			<div class="demo-panel__columns">
				<div class="demo-panel__guide">
					<ol class="demo-steps" data-demo-steps>
						<li class="is-current">
							<strong><?php esc_html_e( 'Send a test lead.', 'jhumel' ); ?></strong>
							<?php esc_html_e( 'Any name and a test email will do. Demo leads post with your first name only.', 'jhumel' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Watch it arrive.', 'jhumel' ); ?></strong>
							<?php
							/* translators: %s: link to the public Telegram channel. */
							printf( esc_html__( 'Within about five seconds an alert with its AI score lands in the %s.', 'jhumel' ), '<a href="https://t.me/s/jhumelbuilds_demo">' . esc_html__( 'public Telegram channel', 'jhumel' ) . '</a>' );
							?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Find it on the dashboard.', 'jhumel' ); ?></strong>
							<?php esc_html_e( 'Log in, open your lead to read the AI’s draft reply, and drag it through the pipeline.', 'jhumel' ); ?>
							<span class="demo-login has-mono-font-family has-small-font-size">
								<?php esc_html_e( 'Email:', 'jhumel' ); ?> <?php echo esc_html( $jhumel_demo_email ); ?><br>
								<?php esc_html_e( 'Password:', 'jhumel' ); ?> <?php echo esc_html( $jhumel_demo_password ); ?>
							</span>
							<span class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $jhumel_dashboard_url ); ?>"><?php esc_html_e( 'Open the dashboard', 'jhumel' ); ?></a></span>
						</li>
					</ol>
					<p class="has-gray-600-color has-text-color has-small-font-size"><?php esc_html_e( 'The demo resets every night.', 'jhumel' ); ?></p>
				</div>
				<div class="demo-panel__form">
					<?php
					// Patterns are not passed through the shortcode filter, so the form is rendered here directly.
					echo do_shortcode( '[quote_form organization="demo" success_message="Your lead is in. Follow steps 2 and 3 to watch it arrive: it takes about five seconds."]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the shortcode escapes its own output.
					?>
				</div>
			</div>
		</div>
		<!-- /wp:html -->
	</article>
	<!-- /wp:group -->

	<!-- wp:group {"tagName":"article","anchor":"serviceops","align":"wide","className":"project-card","layout":{"type":"default"}} -->
	<article id="serviceops" class="wp-block-group alignwide project-card">
		<!-- wp:html -->
		<header class="project-card__header">
			<div>
				<h3 class="project-card__title">ServiceOps</h3>
				<p class="project-card__summary"><?php esc_html_e( 'Scheduling, invoicing, and payments for field-service businesses', 'jhumel' ); ?></p>
			</div>
			<p class="project-card__meta"><span class="project-card__live"><?php esc_html_e( 'Live', 'jhumel' ); ?></span><span><?php esc_html_e( '2026', 'jhumel' ); ?></span><span><?php esc_html_e( 'Designed and built solo', 'jhumel' ); ?></span></p>
		</header>
		<!-- /wp:html -->

		<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50","top":"var:preset|spacing|40"}}}} -->
		<div class="wp-block-columns">
			<!-- wp:column {"width":"40%"} -->
			<div class="wp-block-column" style="flex-basis:40%">
				<!-- wp:paragraph -->
				<p>For a small plumbing, HVAC, or repair business. The owner runs customers, jobs, schedules, and invoices from one dashboard, technicians get a simple phone view of their day, and customers pay by card.</p>
				<!-- /wp:paragraph -->

				<!-- wp:list {"className":"check-list"} -->
				<ul class="wp-block-list check-list">
					<!-- wp:list-item -->
					<li>A technician can never be double-booked. The database refuses overlapping jobs, so no screen can get it wrong.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Card payments run through Stripe, and each one is recorded exactly once, even if Stripe sends the notice twice.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>An invoice’s amount is locked when it is issued, so what the customer pays always matches what they were sent.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Every change is tested automatically, including full walkthroughs in a real browser and accessibility checks.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->

				<!-- wp:paragraph {"className":"tool-tags"} -->
				<p class="tool-tags"><span>Next.js</span><span>TypeScript</span><span>Supabase</span><span>Stripe</span><span>Playwright</span></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://service-ops-nine.vercel.app/">Try it live</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://github.com/jhums04/ServiceOps">View the code</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

				<!-- wp:paragraph {"textColor":"gray-600","fontSize":"small"} -->
				<p class="has-gray-600-color has-text-color has-small-font-size">One click signs you in as the owner or as a technician, no sign-up. Payments are in Stripe’s test mode, and the demo resets every hour.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"60%"} -->
			<div class="wp-block-column" style="flex-basis:60%">
				<!-- wp:html -->
				<?php jhumel_print_gallery( 'showcase', $jhumel_serviceops_shots ); ?>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</article>
	<!-- /wp:group -->

	<!-- wp:group {"tagName":"article","anchor":"automations","align":"wide","className":"project-card","layout":{"type":"default"}} -->
	<article id="automations" class="wp-block-group alignwide project-card">
		<!-- wp:html -->
		<header class="project-card__header">
			<div>
				<h3 class="project-card__title"><?php esc_html_e( 'Automations', 'jhumel' ); ?></h3>
				<p class="project-card__summary"><?php esc_html_e( 'Workflows that take repetitive work off a team', 'jhumel' ); ?></p>
			</div>
			<p class="project-card__meta"><span class="project-card__live"><?php esc_html_e( 'Live', 'jhumel' ); ?></span><span><?php esc_html_e( 'n8n, Make, Zapier', 'jhumel' ); ?></span></p>
		</header>
		<!-- /wp:html -->

		<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50","top":"var:preset|spacing|40"}}}} -->
		<div class="wp-block-columns">
			<!-- wp:column {"width":"40%"} -->
			<div class="wp-block-column" style="flex-basis:40%">
				<!-- wp:paragraph -->
				<p>I build automations in n8n, Make, or Zapier, whichever fits your team and budget. You can see every step, change it later, and you own it outright. If your team does something by hand more than once a week, it can probably be automated.</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"textColor":"gray-600","fontSize":"small"} -->
				<p class="has-gray-600-color has-text-color has-small-font-size">The two shown here are samples: the n8n workflows that run Lead Engine’s leads. My Make and Zapier work belongs to the businesses I built it for, so it stays private.</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"tool-tags"} -->
				<p class="tool-tags"><span>n8n</span><span>Make</span><span>Zapier</span><span>Webhooks</span><span>REST APIs</span></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"60%"} -->
			<div class="wp-block-column" style="flex-basis:60%">
				<!-- wp:html -->
				<?php jhumel_print_gallery( 'stack', $jhumel_automation_shots ); ?>
				<!-- /wp:html -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</article>
	<!-- /wp:group -->

	<!-- wp:html -->
	<p class="gallery__hint has-gray-600-color has-text-color has-small-font-size alignwide"><?php esc_html_e( 'Select any screenshot to see it full size.', 'jhumel' ); ?></p>

	<?php /* translators: 1: number of the screenshot shown, 2: number of screenshots. The script fills them in. */ ?>
	<dialog class="lightbox" aria-label="<?php esc_attr_e( 'Screenshots', 'jhumel' ); ?>" data-counter="<?php esc_attr_e( '%1$s of %2$s', 'jhumel' ); ?>" data-lightbox>
		<div class="lightbox__bar">
			<p class="lightbox__counter" data-role="counter"></p>
			<button class="lightbox__button" type="button" data-role="close"><?php esc_html_e( 'Close', 'jhumel' ); ?></button>
		</div>
		<figure class="lightbox__figure">
			<img class="lightbox__image" src="" alt="" draggable="false" data-role="image">
			<figcaption class="lightbox__caption" data-role="caption"></figcaption>
		</figure>
		<button class="lightbox__button lightbox__nav lightbox__nav--previous" type="button" data-role="previous"><span aria-hidden="true">←</span> <?php esc_html_e( 'Previous', 'jhumel' ); ?></button>
		<button class="lightbox__button lightbox__nav lightbox__nav--next" type="button" data-role="next"><?php esc_html_e( 'Next', 'jhumel' ); ?> <span aria-hidden="true">→</span></button>
	</dialog>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
