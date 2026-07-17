# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/). While the major version is `0`, the project makes no backwards-compatibility commitments: breaking changes can land in any release.

## [Unreleased]

## [0.3.0] – 2026-07-17

### Changed

- Renamed the plugin from *Kntnt Transparent Header for Ollie Pro* to *Kntnt Transparent Header for Ollie*. The old name pointed at the dependency rather than the plugin: it extends the sticky header that the Ollie theme and Ollie Pro ship together, but what it is *about* is that header, not Ollie Pro. Everything that carried the old name moved with it – the slug, text domain and plugin folder are now `kntnt-transparent-header-ollie`, the PHP namespace is `Kntnt\Transparent_Header_Ollie`, and the GitHub repository is now `Kntnt/kntnt-transparent-header-ollie`. Ollie Pro is still required and still named as the dependency; only this plugin's own name changed. Because the slug changed, an existing install does not update across the rename on its own: deactivate and delete the old plugin, then install the renamed one and activate it.

## [0.2.0] – 2026-07-17

### Added

- Mega menu panels now fade in on the header's own timing over a transparent header. Ollie Menu Designer fades a panel in over about 100ms while this plugin fades the header's background over 300ms, so over a transparent header the panel snapped in a fraction of a second before the header colour caught up behind it. The panel's opacity transition is matched to the header's – the same duration and easing, copied from Ollie Pro's slide because CSS cannot read another element's timing – so the two arrive as one surface. Applies only while the header is actually fading (transparent and turning solid) and only to the desktop dropdown, not the mobile overlay.
- Removed the bright band that flashed across the header's lower edge while a mega menu opened over the transparent header. Ollie Menu Designer positions the panel from the nav item's Top spacing, which sits inside the header's padding, so the panel overlapped the header by a narrow band; two semi-transparent copies of the same colour composite brighter than either, so mid-fade that overlap showed as a bright strip. A small script now measures the header's live bottom edge and hangs the panel flush against it, edge to edge, so there is no overlap to composite. It measures the panel's top before it paints, so a companion plugin that caps a tall menu's height reads the corrected edge without a shared signal.

## [0.1.0] – 2026-07-17

### Added

- Transparent-over-hero mode for Ollie's sticky header: transparent at the top of the page, fading to the header group's own background colour once scrolled past 20px and solid whenever a menu inside the header is open. The plugin ships no styling of its own – the solid state is whatever background you set on the group block in the Site Editor.
- A workaround for Ollie Pro leaving `--sticky-full-offset` unset unless a Top Offset is configured, which left a logged-in visitor a strip of header stuck to the top of the screen on viewports of 600px and below – where the admin bar stops being fixed and no longer hides it.
- A workaround for a specificity bug in the Ollie *theme*: its own reset that should drop the sticky header's top offset to zero on narrow screens (`@media (max-width: 600px) { … top: 0 }`) is outweighed by its unscoped base rule, so it never applies. On viewports of 600px and below – where core makes the admin bar `absolute` and it scrolls away with the page – a logged-in visitor was left a 46px strip of empty space above the header once the bar had scrolled out. Reinstated in pure CSS with a selector heavy enough to win, for solid sticky headers; the transparent header, being `fixed`, is handled separately (below).
- The same flush-to-top behaviour for the transparent header on viewports of 600px and below. Being `fixed` (to overlay the hero) it can't tuck under the admin bar in the flow like the solid header, so a small script – loaded only for logged-in visitors, and working only below 600px – tracks the admin bar's live bottom edge and feeds it to the header's top offset. The header tucks under the bar while it is visible and sits flush against the top of the viewport the moment the bar scrolls away.
- A workaround for Ollie Pro transitioning only `transform`, which made the background flip in a single frame instead of fading in step with the slide.
- `Requires Plugins: ollie-pro` header, so WordPress refuses to activate the plugin without Ollie Pro and deactivates it if Ollie Pro goes away.
- A silent guard that skips the header feature when the active theme is not Ollie (or one of its child themes). It stays quiet on purpose: Ollie Pro already reports a wrong theme, and a second notice would only repeat it.
- A PHP version guard in the main plugin file, as a second line of defence for installs that load the plugin outside the normal activation path.
- Self-updating from the project's GitHub releases: the plugin appears under *Dashboard → Updates* like any other, checking the repository named by its own `Plugin URI` header at most once every six hours. A release is only offered when it carries a ZIP asset served from GitHub's own download hosts, so a tampered header cannot redirect the update installer at an attacker's package. Update checks run under every theme, unlike the header feature – a site parked on another theme must not rot on an old version.
- `build-release-zip.sh`, which builds a release zip containing only runtime files, and can create or update the GitHub release and upload the asset. The asset name carries no version segment, keeping the `latest/download` URL stable.

[Unreleased]: https://github.com/Kntnt/kntnt-transparent-header-ollie/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/Kntnt/kntnt-transparent-header-ollie/releases/tag/v0.3.0
[0.2.0]: https://github.com/Kntnt/kntnt-transparent-header-ollie/releases/tag/v0.2.0
[0.1.0]: https://github.com/Kntnt/kntnt-transparent-header-ollie/releases/tag/v0.1.0
