<?php
/**
 * Plugin Name:       Kntnt Transparent Header for Ollie Pro
 * Plugin URI:        https://github.com/Kntnt/kntnt-transparent-header-ollie-pro
 * Description:       Adds a transparent-over-hero mode to Ollie Pro's hide-on-scroll-down sticky header.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.3
 * Requires Plugins:  ollie-pro
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kntnt-transparent-header-ollie-pro
 * Domain Path:       /languages
 *
 * @package Kntnt\Transparent_Header_Ollie_Pro
 * @since   0.1.0
 */

declare( strict_types = 1 );

// Prevent direct file access outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The PHP floor, mirroring the `Requires PHP` header above. WordPress reads the
// header, but PHP itself cannot, so the guard below needs its own copy.
const KNTNT_TRANSPARENT_HEADER_OLLIE_PRO_MINIMUM_PHP = '8.3';

/**
 * Guards against running on a PHP version older than the declared floor.
 *
 * The plugin header already makes WordPress block activation on older
 * installs. This is a second line of defence for environments that load the
 * plugin outside the normal activation path: it shows an admin notice and
 * deactivates the plugin so it never reaches code that would fatally error.
 *
 * @since 0.1.0
 *
 * @return bool True when PHP meets the floor; false when the guard fires.
 */
function kntnt_transparent_header_ollie_pro_requirements_check(): bool {

	// Nothing to do when the runtime meets the requirement.
	if ( version_compare( PHP_VERSION, KNTNT_TRANSPARENT_HEADER_OLLIE_PRO_MINIMUM_PHP, '>=' ) ) {
		return true;
	}

	// Surface the problem as an admin notice.
	add_action(
		'admin_notices',
		static function (): void {
			$message = sprintf(
				/* translators: 1: required PHP version, 2: current version. */
				__( 'Kntnt Transparent Header for Ollie Pro requires PHP %1$s or later. This server runs PHP %2$s. The plugin has been deactivated.', 'kntnt-transparent-header-ollie-pro' ),
				KNTNT_TRANSPARENT_HEADER_OLLIE_PRO_MINIMUM_PHP,
				PHP_VERSION,
			);
			printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
		},
	);

	// Deactivate the plugin so WordPress does not try to load it again.
	add_action(
		'admin_init',
		static function (): void {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		},
	);

	return false;

}

// Abort before loading anything else if the runtime cannot support the plugin.
if ( ! kntnt_transparent_header_ollie_pro_requirements_check() ) {
	return;
}

// Load the PSR-4 autoloader for the plugin's own classes.
require_once __DIR__ . '/autoloader.php';

// Bootstrap the plugin singleton, which decides for itself how much of the
// plugin the active theme can support.
\Kntnt\Transparent_Header_Ollie_Pro\Plugin::get_instance( __FILE__ );
