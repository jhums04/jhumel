<?php
/**
 * Title: About
 * Slug: jhumel/about
 * Categories: jhumel
 * Inserter: no
 *
 * A first-person story in a plain, senior voice, and beside it the stack a small or medium business would hire for.
 * No job timeline: big-company titles make small clients worry about cost and over-engineering, so the background
 * is one sentence about what it means for them.
 *
 * @package Jhumel
 */

$jhumel_stack = array(
	__( 'Front end', 'jhumel' )               => array( 'Next.js', 'React', 'TypeScript', 'JavaScript', 'Tailwind CSS', 'HTML', 'CSS' ),
	__( 'Back end', 'jhumel' )                => array( 'Node.js', 'PHP', 'Java', 'Spring Boot', 'REST APIs', 'Webhooks' ),
	__( 'Data', 'jhumel' )                    => array( 'PostgreSQL', 'Supabase', 'SQL', 'Redis' ),
	__( 'Integrations and AI', 'jhumel' )     => array( 'Stripe', 'OpenAI API', 'n8n', 'Make', 'Zapier', 'Telegram bots' ),
	__( 'CMS', 'jhumel' )                     => array( 'WordPress themes and plugins' ),
	__( 'Design', 'jhumel' )                  => array( 'Figma' ),
	__( 'Hosting and delivery', 'jhumel' )    => array( 'Vercel', 'Cloudflare', 'Linux VPS', 'Docker', 'GitHub Actions', 'AWS' ),
	__( 'Testing', 'jhumel' )                 => array( 'Vitest', 'Playwright' ),
	__( 'AI-assisted development', 'jhumel' ) => array( 'Claude Code', 'Figma MCP', 'OpenAI Codex', 'ChatGPT' ),
);
?>
<!-- wp:group {"tagName":"section","anchor":"about","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section","bottom":"var:preset|spacing|section"}}},"backgroundColor":"gray-50","layout":{"type":"constrained"}} -->
<section id="about" class="wp-block-group alignfull has-gray-50-background-color has-background" style="padding-top:var(--wp--preset--spacing--section);padding-bottom:var(--wp--preset--spacing--section)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50","top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"45%","className":"sticky-intro"} -->
		<div class="wp-block-column sticky-intro" style="flex-basis:45%">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">About <em class="accent-underline">me</em></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"fontSize":"medium"} -->
			<p class="has-medium-font-size">I'm Jhumel, a full-stack developer. I build websites, web applications, internal tools, and automations, and I care most about the unglamorous part: making them work, and keep working.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>I work across the whole stack: interfaces, back-end services, databases, APIs and integrations, and the hosting underneath, from Vercel to a Linux server with its domains, DNS, SSL, and deployments. I can take an idea to production, or step into an existing codebase and improve it.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>For five years I've built software that people depend on every day, from internal tools for operations teams to platforms used by more than a thousand people. You get that discipline without the big-company overhead: one person who builds it, explains it, and answers your messages. Most of that work is private to the companies I built it for, which is why the projects on this page exist: they show the same standards in code you can open.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>I use AI tools like Claude Code and Codex to explore options, draft code, and write tests faster. Nothing ships that I haven't read, tested, and debugged myself.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>No design yet? I'll create one. You get the screens as an editable Figma file to review and comment on before any code is written, and then I build exactly what you approved. Already have a design or a site you like? I'll build from that.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>I work well on my own, inside a team, or dropped into a project halfway through, even when the documentation is thin.</p>
			<!-- /wp:paragraph -->

			<!-- wp:html -->
			<dl class="fact-row">
				<div><dt><?php esc_html_e( 'Based in', 'jhumel' ); ?></dt><dd><?php esc_html_e( 'Cavite, Philippines', 'jhumel' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Hours', 'jhumel' ); ?></dt><dd><?php esc_html_e( 'Flexible, set around your time zone', 'jhumel' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Replies', 'jhumel' ); ?></dt><dd><?php esc_html_e( 'Within one business day', 'jhumel' ); ?></dd></div>
			</dl>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"55%"} -->
		<div class="wp-block-column" style="flex-basis:55%">
			<!-- wp:html -->
			<h3 class="tool-groups__heading"><?php esc_html_e( 'What I build with', 'jhumel' ); ?></h3>
			<div class="tool-groups">
				<?php foreach ( $jhumel_stack as $jhumel_group_name => $jhumel_tools ) : ?>
					<div>
						<p class="tool-groups__name"><?php echo esc_html( $jhumel_group_name ); ?></p>
						<p class="tool-tags">
							<?php foreach ( $jhumel_tools as $jhumel_tool ) : ?>
								<span><?php echo esc_html( $jhumel_tool ); ?></span>
							<?php endforeach; ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
