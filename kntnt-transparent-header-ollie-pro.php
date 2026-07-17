<?php
/**
 * Plugin Name:       Kntnt Transparent Header for Ollie Pro
 * Description:       Gives Ollie's sticky header a transparent-over-hero mode, and works around two Ollie Pro defects. Ships no colours — style the `is-transparent` class from your design layer.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      8.5
 * Requires Plugins:  ollie-pro
 * Author:            Thomas Barregren
 * License:           GPL-2.0-or-later
 *
 * @package Kntnt\Transparent_Header_Ollie_Pro
 */

declare(strict_types=1);

namespace Kntnt\Transparent_Header_Ollie_Pro;

defined('ABSPATH') || exit;

/**
 * Loads the assets that implement transparent header mode.
 */
final class Plugin {

	/**
	 * Handle used for both the stylesheet and the script.
	 */
	private const HANDLE = 'kntnt-transparent-header-ollie-pro';

	/**
	 * Ollie's own stylesheet handle, derived from the theme's `Ollie` namespace.
	 *
	 * The stylesheet must load after it: our transparent-mode rule and Ollie's
	 * sticky rule have identical specificity, so source order decides the winner.
	 */
	private const THEME_HANDLE = 'ollie';

	/**
	 * Ollie's template directory, i.e. the slug of the theme this plugin extends.
	 *
	 * Equal to THEME_HANDLE by coincidence, not by rule: one names a registered
	 * stylesheet, the other a directory on disk, and either could change alone.
	 */
	private const THEME_TEMPLATE = 'ollie';

	/**
	 * Fallback asset version, used when a file cannot be stat'ed.
	 */
	private const VERSION = '1.0.0';

	/**
	 * Hooks the plugin into WordPress, unless the active theme is not Ollie.
	 *
	 * Everything here extends rules that only Ollie and Ollie Pro ship, so under
	 * any other theme the plugin has nothing to act on and registers no hooks at
	 * all. It stays silent about it: Ollie Pro is a hard dependency and already
	 * tells the user when the theme is wrong, so a second notice saying the same
	 * thing would only be noise.
	 *
	 * Enqueues late so the theme's stylesheet is already registered and can be
	 * depended on; plugins hook earlier than themes, so the default priority
	 * would put this stylesheet first and silently lose the cascade.
	 *
	 * @return void
	 */
	public static function init(): void {
		// `get_template()` names the parent theme, so Ollie child themes — which
		// inherit the very rules this plugin patches — pass just like Ollie itself.
		if (get_template() !== self::THEME_TEMPLATE) {
			return;
		}

		add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets'], 20);
	}

	/**
	 * Enqueues the frontend stylesheet and script.
	 *
	 * Loaded on every page, not only the transparent ones: the stylesheet also
	 * carries two fixes that apply to every sticky header. The script goes in the
	 * footer without `defer` so it runs during parse and sets the class before
	 * first paint — deferring it makes the header flash solid on load.
	 *
	 * @return void
	 */
	public static function enqueue_assets(): void {
		$url = plugin_dir_url(__FILE__);
		$dir = plugin_dir_path(__FILE__);

		// Depending on a handle that was never registered makes WordPress drop the
		// stylesheet entirely, so only claim the dependency when it really exists.
		$deps = wp_style_is(self::THEME_HANDLE, 'registered') ? [self::THEME_HANDLE] : [];

		wp_enqueue_style(
			self::HANDLE,
			"{$url}assets/header.css",
			$deps,
			self::asset_version("{$dir}assets/header.css"),
		);

		wp_enqueue_script(
			self::HANDLE,
			"{$url}assets/header.js",
			[],
			self::asset_version("{$dir}assets/header.js"),
			['in_footer' => true],
		);
	}

	/**
	 * Returns a cache-busting version for an asset.
	 *
	 * Uses the file's modification time, so edits take effect without a version
	 * bump; falls back to the plugin version if the file is missing.
	 *
	 * @param string $path Absolute path to the asset.
	 * @return string
	 */
	private static function asset_version(string $path): string {
		return file_exists($path) ? (string) filemtime($path) : self::VERSION;
	}

}

Plugin::init();
