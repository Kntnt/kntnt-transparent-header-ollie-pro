# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/). While the major version is `0`, the project makes no backwards-compatibility commitments: breaking changes can land in any release.

## [Unreleased]

First release. Everything below is what 0.1.0 ships.

### Added

- Transparent-over-hero mode for Ollie's sticky header: transparent at the top of the page, fading to the header group's own background colour once scrolled past 20px, and solid whenever a menu inside the header is open. The plugin ships no colours — the solid state is whatever background you set on the group block in the Site Editor.
- A workaround for Ollie Pro leaving `--sticky-full-offset` unset unless a Top Offset is configured, which left a logged-in visitor a strip of header stuck to the top of the screen on viewports of 600px and below — where the admin bar stops being fixed and no longer hides it.
- A workaround for Ollie Pro transitioning only `transform`, which made the background flip in a single frame instead of fading in step with the slide.
- `Requires Plugins: ollie-pro` header, so WordPress refuses to activate the plugin without Ollie Pro and deactivates it if Ollie Pro goes away.
- A silent guard that skips the header feature when the active theme is not Ollie (or one of its child themes). It stays quiet on purpose: Ollie Pro already reports a wrong theme, and a second notice would only repeat it.
- A PHP version guard in the main plugin file, as a second line of defence for installs that load the plugin outside the normal activation path.
- Self-updating from the project's GitHub releases: the plugin appears under *Dashboard → Updates* like any other, checking the repository named by its own `Plugin URI` header at most once every six hours (filterable via `kntnt_transparent_header_ollie_pro_update_check_ttl`). A release is only offered when it carries a ZIP asset served from GitHub's own download hosts, so a tampered header cannot redirect the update installer at an attacker's package. Update checks run under every theme, unlike the header feature — a site parked on another theme must not rot on an old version.
- `build-release-zip.sh`, which builds a release zip containing only runtime files, and can create or update the GitHub release and upload the asset. The asset name carries no version segment, keeping the `latest/download` URL stable.
