Read when cutting a release, or adding a runtime file.

- Version lives in three places, bump all on release: the `Version:` header in `kntnt-transparent-header-ollie-pro.php` (canonical – `Plugin.php` reads it via `get_plugin_data()`), `Project-Id-Version` in `languages/kntnt-transparent-header-ollie-pro.pot` (hand-maintained; the build does not regenerate it), and the top heading in `CHANGELOG.md`. The `@since` tags are not version markers – never bump them.
- Archive build: `./build-release-zip.sh` (`composer build`). Distribution = GitHub releases, not wordpress.org; the plugin self-updates from them.
- Adding a runtime file? Add it to `KEEP` in `build-release-zip.sh` – else it works locally and is silently missing from the release.
- Never put a version segment in the asset name. `build-release-zip.sh` explains why.
