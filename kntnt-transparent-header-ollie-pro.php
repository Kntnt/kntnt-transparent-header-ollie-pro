<?php
/**
 * Plugin Name:       Kntnt Transparent Header for Ollie Pro
 * Plugin URI:        https://github.com/Kntnt/kntnt-transparent-header-ollie-pro
 * @phpcs:ignore Generic.Files.LineLength.TooLong -- WordPress parses Description from a single line; wrapping it truncates the plugin list entry.
 * Description:       Gives Ollie's sticky header a transparent-over-hero mode, and works around two Ollie Pro defects. Ships no colours — the header's own background simply reappears once scrolled.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      8.5
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

/**
 * Guards against running on a PHP version older than the 8.5 floor.
 *
 * The plugin header already makes WordPress block activation on older installs.
 * This is a second line of defence for environments that load the plugin outside
 * the normal activation path: it shows an admin notice and deactivates the
 * plugin so it never reaches code that would fatally error.
 *
 * @since 0.1.0
 *
 * @return bool True when PHP is 8.5 or newer; false when the guard fires.
 */
function kntnt_transparent_header_ollie_pro_requirements_check(): bool {

	// Nothing to do when the runtime meets the requirement.
	if ( version_compare( PHP_VERSION, '8.5', '>=' ) ) {
		return true;
	}

	// Surface the problem as an admin notice.
	add_action(
		'admin_notices',
		static function (): void {
			// phpcs:disable Generic.Files.LineLength.TooLong -- splitting a translatable string breaks extraction, and the translators comment must sit directly above the __() call.
			$message = sprintf(
				/* translators: 1: required PHP version, 2: current PHP version. */
				__( 'Kntnt Transparent Header for Ollie Pro requires PHP %1$s or later. This server runs PHP %2$s. The plugin has been deactivated.', 'kntnt-transparent-header-ollie-pro' ),
				'8.5',
				PHP_VERSION,
			);
			// phpcs:enable Generic.Files.LineLength.TooLong
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

// Abort before loading anything else when the runtime cannot support the plugin.
if ( ! kntnt_transparent_header_ollie_pro_requirements_check() ) {
	return;
}

// Load the PSR-4 autoloader for the plugin's own classes.
require_once __DIR__ . '/autoloader.php';

// Bootstrap the plugin singleton, which decides for itself how much of the
// plugin the active theme can support.
\Kntnt\Transparent_Header_Ollie_Pro\Plugin::get_instance( __FILE__ );
