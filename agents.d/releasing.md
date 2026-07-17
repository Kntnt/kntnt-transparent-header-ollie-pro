Read when cutting a release, or adding a runtime file.

- Archive build: `./build-release-zip.sh` (`composer build`). Distribution = GitHub releases, not wordpress.org; the plugin self-updates from them.
- Adding a runtime file? Add it to `KEEP` in `build-release-zip.sh` – else it works locally and is silently missing from the release.
- Never put a version segment in the asset name. `build-release-zip.sh` explains why.
