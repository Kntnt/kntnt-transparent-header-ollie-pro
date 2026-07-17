<?php
/**
 * Plugin Name:       Kntnt Transparent Header for Ollie Pro
 * Plugin URI:        https://github.com/Kntnt/kntnt-transparent-header-ollie-pro
 * Description:       Gives Ollie's sticky header a transparent-over-hero mode, and works around two Ollie Pro defects. Ships no colours — the header's own background simply reappears once scrolled.
 * Version:           1.0.0
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
 * @since   1.0.0
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
 * @since 1.0.0
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
			$message = sprintf(
				/* translators: 1: required PHP version, 2: current PHP version. */
				__( 'Kntnt Transparent Header for Ollie Pro requires PHP %1$s or later. This server runs PHP %2$s. The plugin has been deactivated.', 'kntnt-transparent-header-ollie-pro' ),
				'8.5',
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

/**
 * Reports whether the active theme is Ollie, the theme this plugin extends.
 *
 * `get_template()` names the parent theme, so Ollie child themes — which inherit
 * the very rules this plugin patches — pass just like Ollie itself.
 *
 * Deliberately silent when it fails: Ollie Pro is a hard dependency (see the
 * `Requires Plugins` header) and already tells the user when the theme is wrong,
 * so a second notice saying the same thing would only be noise.
 *
 * @since 1.0.0
 *
 * @return bool True when Ollie or one of its child themes is active.
 */
function kntnt_transparent_header_ollie_pro_theme_check(): bool {
	return get_template() === 'ollie';
}

// Abort before loading anything else when the runtime or the active theme
// cannot support the plugin. Under a foreign theme this is the whole plugin:
// no autoloader, no class, no hooks — it costs one cached option read.
if ( ! kntnt_transparent_header_ollie_pro_requirements_check() || ! kntnt_transparent_header_ollie_pro_theme_check() ) {
	return;
}

// Load the PSR-4 autoloader for the plugin's own classes.
require_once __DIR__ . '/autoloader.php';

// Bootstrap the plugin singleton, which wires every hook.
\Kntnt\Transparent_Header_Ollie_Pro\Plugin::get_instance( __FILE__ );
