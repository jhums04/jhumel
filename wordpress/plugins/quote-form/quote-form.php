<?php
/**
 * Plugin Name:       Quote Form
 * Description:       Quote form with a live price estimate. Saves every submission and delivers it to n8n.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            Jhumel Bonganciso
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       quote-form
 *
 * @package QuoteForm
 */

use QuoteForm\Activator;
use QuoteForm\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );

( new Plugin( __FILE__ ) )->register();
