<?php
/**
 * Plugin singleton — bootstrap and asset wiring.
 *
 * Holds the absolute path to the main plugin file and registers the hooks that
 * load the stylesheet and script implementing transparent header mode.
 *
 * @package Kntnt\Transparent_Header_Ollie_Pro
 * @since   1.0.0
 */

declare( strict_types = 1 );

namespace Kntnt\Transparent_Header_Ollie_Pro;

use LogicException;

/**
 * Singleton entry point for the Kntnt Transparent Header for Ollie Pro plugin.
 *
 * Constructed once by get_instance(), only after the main plugin file has
 * established that the runtime and the active theme can support the plugin. The
 * constructor registers every WordPress hook, so it stays the single
 * authoritative place to trace the hook graph.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Handle used for both the stylesheet and the script.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const HANDLE = 'kntnt-transparent-header-ollie-pro';

	/**
	 * Ollie's own stylesheet handle, derived from the theme's `Ollie` namespace.
	 *
	 * The plugin stylesheet must load after it: the transparent-mode rule and
	 * Ollie's sticky rule have identical specificity, so source order decides the
	 * winner.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const THEME_HANDLE = 'ollie';

	/**
	 * The sole instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Absolute path to the main plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static string $plugin_file = '';

	/**
	 * Returns (and on the first call, creates) the singleton instance.
	 *
	 * The first call must pass the absolute path to the main plugin file so the
	 * asset helpers can resolve URLs without globals. Subsequent calls ignore the
	 * argument and return the existing instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file Absolute path to the main plugin file. Ignored
	 *                            on calls after the first.
	 * @return self
	 */
	public static function get_instance( string $plugin_file = '' ): self {

		// Return early when already bootstrapped.
		if ( self::$instance !== null ) {
			return self::$instance;
		}

		// Capture the plugin file path and initialise the singleton.
		self::$plugin_file = $plugin_file;
		self::$instance = new self();

		return self::$instance;

	}

	/**
	 * Registers the plugin's WordPress hooks.
	 *
	 * Enqueues at priority 20 so the theme's stylesheet is already registered and
	 * can be depended on; plugins hook earlier than themes, so the default
	 * priority would put this stylesheet first and silently lose the cascade.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );
	}

	/**
	 * Enqueues the frontend stylesheet and script.
	 *
	 * Loaded on every page, not only the transparent ones: the stylesheet also
	 * carries two fixes that apply to every sticky header. The script goes in the
	 * footer without `defer` so it runs during parse and sets the class before
	 * first paint — deferring it makes the header flash solid on load.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {

		// Depending on a handle that was never registered makes WordPress drop the
		// stylesheet entirely, so only claim the dependency when it really exists.
		$deps = wp_style_is( self::THEME_HANDLE, 'registered' ) ? [ self::THEME_HANDLE ] : [];

		wp_enqueue_style(
			self::HANDLE,
			plugins_url( 'css/header.css', self::$plugin_file ),
			$deps,
			self::asset_version( 'css/header.css' ),
		);

		wp_enqueue_script(
			self::HANDLE,
			plugins_url( 'js/header.js', self::$plugin_file ),
			[],
			self::asset_version( 'js/header.js' ),
			[ 'in_footer' => true ],
		);

	}

	/**
	 * Returns a cache-busting version for an asset.
	 *
	 * Uses the file's modification time, so edits take effect without a version
	 * bump; falls back to the plugin version if the file cannot be stat'ed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path Path to the asset, relative to the plugin root.
	 * @return string
	 */
	private static function asset_version( string $relative_path ): string {

		// A readable file dates itself; anything else falls back to the header's
		// version, which at least changes on release.
		$path = plugin_dir_path( self::$plugin_file ) . $relative_path;
		$mtime = is_readable( $path ) ? filemtime( $path ) : false;

		return $mtime !== false ? (string) $mtime : self::get_version();

	}

	/**
	 * Returns the plugin version from the plugin header.
	 *
	 * Read from the header rather than duplicated in a constant, so the version
	 * has exactly one authoritative source.
	 *
	 * @since 1.0.0
	 *
	 * @return string The version string, or '' when the header is unreadable.
	 */
	private static function get_version(): string {

		// Translation is disabled to avoid triggering a just-in-time textdomain
		// load before `init`.
		$data = get_file_data( self::$plugin_file, [ 'Version' => 'Version' ] );

		return $data['Version'] ?? '';

	}

	/**
	 * Prevents cloning of the singleton.
	 *
	 * @since 1.0.0
	 *
	 * @throws LogicException Always, because a singleton must not be cloned.
	 *
	 * @return void
	 */
	public function __clone() {
		throw new LogicException( 'Cannot clone a singleton.' );
	}

	/**
	 * Prevents unserialisation of the singleton.
	 *
	 * @since 1.0.0
	 *
	 * @throws LogicException Always, because a singleton must not be unserialised.
	 *
	 * @return void
	 */
	public function __wakeup() {
		throw new LogicException( 'Cannot unserialize a singleton.' );
	}

}
