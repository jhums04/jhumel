<?php
/**
 * The choices on the "Start a project" brief.
 *
 * @package QuoteForm
 */

namespace QuoteForm\Form;

/**
 * The brief asks what kind of help is needed and a timeline, with no price calculator and no budget question:
 * scope is agreed on the call, so the brief stays open. Keys are what gets stored and sent to n8n.
 */
final class ProjectBrief {

	public const FORM_TYPE = 'brief';

	/**
	 * What the visitor can ask for, by key.
	 *
	 * @return array<string, string>
	 */
	public static function services(): array {
		return array(
			'systems_crm'     => __( 'Systems and CRMs', 'quote-form' ),
			'automation_ai'   => __( 'Automations and AI', 'quote-form' ),
			'integrations'    => __( 'Integrations', 'quote-form' ),
			'websites'        => __( 'Websites and web apps', 'quote-form' ),
			'dashboards'      => __( 'Dashboards and reporting', 'quote-form' ),
			'solution_design' => __( 'Solution design', 'quote-form' ),
			'other'           => __( 'Something else', 'quote-form' ),
		);
	}

	/**
	 * Timelines, using the same keys as the quote form so n8n and the dashboard treat both alike.
	 *
	 * @return array<string, string>
	 */
	public static function timelines(): array {
		return array(
			'rush'     => __( 'As soon as possible', 'quote-form' ),
			'standard' => __( 'No rush', 'quote-form' ),
		);
	}
}
