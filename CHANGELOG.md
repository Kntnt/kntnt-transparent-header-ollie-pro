# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- `Requires Plugins: ollie-pro` header, so WordPress refuses to activate the plugin without Ollie Pro and deactivates it if Ollie Pro goes away.
- A silent guard that aborts the bootstrap when the active theme is not Ollie (or one of its child themes). Under a foreign theme the plugin loads no autoloader, no class and no hooks. It stays quiet on purpose: Ollie Pro already reports a wrong theme, and a second notice would only repeat it.
- A PHP version guard in the main plugin file, as a second line of defence for installs that load the plugin outside the normal activation path.

### Changed

- Renamed from *Kntnt Transparent Header* to **Kntnt Transparent Header for Ollie Pro** — directory, main file, plugin name, asset handle and the `Kntnt\Transparent_Header_Ollie_Pro` namespace.
- Restructured to the Kntnt WordPress plugin standard: `kntnt-transparent-header-ollie-pro.php` bootstraps, `autoloader.php` maps PSR-4 onto `classes/`, and the singleton `classes/Plugin.php` wires the hooks. Assets moved from `assets/` to `css/` and `js/`.
- The asset version now falls back to the version in the plugin header instead of a duplicated `VERSION` constant, so the version has one authoritative source.
- Corrected the plugin Description, which told the user to style an `is-transparent` class. No such class exists — and by design never will.

## [1.0.0] – 2026-07-17

### Added

- Transparent-over-hero mode for Ollie's sticky header: transparent at the top of the page, fading to the header group's own background colour once scrolled past 20px, and solid whenever a menu inside the header is open.
- A workaround for Ollie Pro leaving `--sticky-full-offset` unset unless a Top Offset is configured, which left logged-in users with an admin-bar-tall strip of header on screen.
- A workaround for Ollie Pro transitioning only `transform`, which made the background flip in a single frame instead of fading in step with the slide.
