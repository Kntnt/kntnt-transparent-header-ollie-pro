Read when cutting a release, or adding a runtime file.

- Distribution = GitHub releases, not wordpress.org. Plugin self-updates from them.
- `git tag vX.Y.Z && git push origin vX.Y.Z`, then `./build-release-zip.sh --tag vX.Y.Z --create`. Notes from the matching `CHANGELOG.md` section. `--update` replaces the asset on an existing release.
- `Version:` header must match the tag.
- Asset name carries **no version segment** — keeps `latest/download` stable; the Updater matches by content type, not filename. Never version it.
- Zip ships only the `KEEP` array in `build-release-zip.sh`. Runtime file not added there → works locally, silently missing from the release.
