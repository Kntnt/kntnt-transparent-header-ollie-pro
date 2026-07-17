#!/usr/bin/env bash
#
# Builds a clean kntnt-transparent-header-ollie-pro.zip containing only runtime
# files.
#
# The plugin has no runtime dependency, so there is nothing to install into the
# staging tree: the build simply stages the source, drops everything that is not
# a runtime file, and zips the result. The asset name has no version segment so
# the GitHub Releases "latest/download" URL stays stable; the Updater identifies
# the asset by content type, not filename.
#
# With no arguments, the zip is written to dist/ in the repo root (created if
# missing); pass --output/--update/--create to choose a different destination.
#
# Requirements: zip.
#   With --tag: git.
#   With --update/--create: gh (GitHub CLI).
#
# Exit codes:
#   0  success
#   1  usage error, missing tool, or build failure

set -euo pipefail

REPO="Kntnt/kntnt-transparent-header-ollie-pro"
PLUGIN_DIR="kntnt-transparent-header-ollie-pro"
ZIP_NAME="${PLUGIN_DIR}.zip"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Runtime files and directories to keep in the release zip. Everything else
# (dev configs, composer manifests, the agents files, dotfiles, this script) is
# dropped.
KEEP=(
	autoloader.php
	classes
	css
	js
	kntnt-transparent-header-ollie-pro.php
	languages
	LICENSE
	README.md
)

TAG=""
OUTPUT_PATH=""
OUTPUT_FILE=""
RELEASE_ACTION=""
STAGE_ROOT=""

# Print usage and exit with the given code (default 0).
usage() {
	cat <<'HELP'
Usage:
  build-release-zip.sh [--output <path>]
  build-release-zip.sh --tag <tag> [--output <path>]
  build-release-zip.sh --tag <tag> --update
  build-release-zip.sh --tag <tag> --create
  build-release-zip.sh --help

Source:
  Without --tag, builds from the local working copy.
  With --tag <tag>, builds from the files at the given git tag.

Destination (defaults to dist/ in the repo root when none is given):
  --output <path>      Save the zip to <path>. A directory (or trailing /) saves
                       kntnt-transparent-header-ollie-pro.zip inside it;
                       otherwise the last path component is the filename. The
                       parent must exist. Omit to write
                       ./dist/kntnt-transparent-header-ollie-pro.zip.
  --update             Upload the zip to an existing GitHub release for <tag>,
                       replacing any existing zip asset. Requires --tag.
  --create             Create a new GitHub release for <tag> and upload the zip.
                       The tag must already exist. Requires --tag.

Examples:
  build-release-zip.sh
  build-release-zip.sh --output ~/Desktop/custom-name.zip
  build-release-zip.sh --tag v1.1.0 --output /tmp
  build-release-zip.sh --tag v1.1.0 --create
  build-release-zip.sh --tag v1.1.0 --update
HELP
	exit "${1:-0}"
}

# Abort with a message on stderr.
die() {
	echo "Error: $1" >&2
	exit 1
}

# Parse the command line into TAG, OUTPUT_PATH and RELEASE_ACTION.
parse_args() {

	while [[ $# -gt 0 ]]; do
		case "$1" in
			--help | -h)
				usage 0
				;;
			--tag)
				[[ $# -lt 2 ]] && die "--tag requires a value."
				TAG="$2"
				shift 2
				;;
			--output)
				[[ $# -lt 2 ]] && die "--output requires a value."
				OUTPUT_PATH="$2"
				shift 2
				;;
			--update | --create)
				[[ -n "$RELEASE_ACTION" ]] && die "--update and --create are mutually exclusive."
				RELEASE_ACTION="${1#--}"
				shift
				;;
			*)
				echo "Error: Unknown option: $1" >&2
				echo >&2
				usage 1
				;;
		esac
	done

}

# Reject contradictory flag combinations and fill in the default destination.
validate_args() {

	# With no destination given, default to building into dist/ in the repo root.
	if [[ -z "$OUTPUT_PATH" && -z "$RELEASE_ACTION" ]]; then
		OUTPUT_PATH="$SCRIPT_DIR/dist"
		mkdir -p "$OUTPUT_PATH"
	fi

	[[ -n "$OUTPUT_PATH" && -n "$RELEASE_ACTION" ]] && die "--output and --${RELEASE_ACTION} cannot be combined."
	[[ -n "$RELEASE_ACTION" && -z "$TAG" ]] && die "--${RELEASE_ACTION} requires --tag."

	return 0

}

# Resolve OUTPUT_PATH into the absolute OUTPUT_FILE the zip is copied to. A
# directory gets the default filename; a file path's parent must already exist.
resolve_output_file() {

	[[ -z "$OUTPUT_PATH" ]] && return 0

	if [[ -d "$OUTPUT_PATH" ]]; then
		OUTPUT_FILE="$(cd "$OUTPUT_PATH" && pwd)/$ZIP_NAME"
	elif [[ "$OUTPUT_PATH" == */ ]]; then
		die "Directory '${OUTPUT_PATH}' does not exist."
	else
		local parent_dir="${OUTPUT_PATH%/*}"
		[[ "$parent_dir" == "$OUTPUT_PATH" ]] && parent_dir="."
		[[ ! -d "$parent_dir" ]] && die "Directory '${parent_dir}' does not exist."
		OUTPUT_FILE="$(cd "$parent_dir" && pwd)/${OUTPUT_PATH##*/}"
	fi

}

# Verify that every tool this invocation needs is on PATH.
require_tools() {

	local missing=()
	local cmd

	command -v zip &>/dev/null || missing+=("zip")
	[[ -n "$TAG" ]] && { command -v git &>/dev/null || missing+=("git"); }
	[[ -n "$RELEASE_ACTION" ]] && { command -v gh &>/dev/null || missing+=("gh"); }

	if [[ ${#missing[@]} -gt 0 ]]; then
		for cmd in "${missing[@]}"; do
			echo "Missing required tool: $cmd" >&2
		done
		exit 1
	fi

}

# Verify the tag exists and that the target release state matches the action.
check_release_state() {

	[[ -z "$TAG" ]] && return 0

	if [[ -z "$(git -C "$SCRIPT_DIR" tag -l "$TAG")" ]]; then
		echo "Error: Tag '$TAG' does not exist." >&2
		echo "Create it first:  git tag $TAG && git push origin $TAG" >&2
		exit 1
	fi

	if [[ "$RELEASE_ACTION" == "update" ]] && ! gh release view "$TAG" --repo "$REPO" &>/dev/null; then
		die "Release '$TAG' does not exist. Use --create instead."
	fi

	if [[ "$RELEASE_ACTION" == "create" ]] && gh release view "$TAG" --repo "$REPO" &>/dev/null; then
		die "Release '$TAG' already exists. Use --update instead."
	fi

	return 0

}

# Copy the source into the staging tree: either the tagged tree or the local
# working copy, minus the directories no release ever contains.
stage_source() {

	if [[ -n "$TAG" ]]; then
		echo "Source: git tag $TAG"
		git -C "$SCRIPT_DIR" archive --prefix="${PLUGIN_DIR}/" "$TAG" | tar -xf - -C "$STAGE_ROOT"
	else
		echo "Source: local working copy"
		rsync -a \
			--exclude='.git' \
			--exclude='vendor' \
			--exclude='dist' \
			--exclude="$ZIP_NAME" \
			"$SCRIPT_DIR/" "$STAGE_ROOT/$PLUGIN_DIR/"
	fi

}

# Delete everything in the staging tree that is not on the keep list.
prune_to_runtime_files() {

	local entry allowed keep

	cd "$STAGE_ROOT/$PLUGIN_DIR"
	shopt -s dotglob

	for entry in *; do
		keep=false
		for allowed in "${KEEP[@]}"; do
			[[ "$entry" == "$allowed" ]] && keep=true && break
		done
		if [[ "$keep" == false ]]; then
			rm -rf "$entry"
			echo "  Removed: $entry"
		fi
	done

	shopt -u dotglob
	cd "$STAGE_ROOT"

}

# Extract the CHANGELOG section for a version into a release-notes file. Prints
# the file path; the section may legitimately be empty.
write_release_notes() {

	local version="$1"
	local notes_file="$STAGE_ROOT/release-notes.md"

	awk -v ver="$version" '
		index($0, "## [" ver "]") == 1 { capture = 1; next }
		capture && /^## \[/ { exit }
		capture && /^\[[^][]+\]:[[:space:]]/ { exit }
		capture { print }
	' "$SCRIPT_DIR/CHANGELOG.md" > "$notes_file"

	echo "$notes_file"

}

# Create the GitHub release, sourcing its body from the matching CHANGELOG
# section rather than GitHub's auto-generated commit digest.
create_release() {

	# The tag is v-prefixed; the changelog heading carries the bare version.
	local version="${TAG#v}"
	local notes_file
	notes_file="$(write_release_notes "$version")"

	# Use the changelog notes when the section had real content; otherwise fall
	# back to auto-generated notes so a release is never published note-less.
	if grep -q '[^[:space:]]' "$notes_file"; then
		printf '\n**Full changelog:** https://github.com/%s/blob/%s/CHANGELOG.md\n' "$REPO" "$TAG" >> "$notes_file"
		gh release create "$TAG" --title "$TAG" --notes-file "$notes_file" --repo "$REPO"
	else
		echo "Warning: no CHANGELOG section for ${version}; using auto-generated notes." >&2
		gh release create "$TAG" --title "$TAG" --generate-notes --repo "$REPO"
	fi

	echo "Created release: $TAG"

}

# Upload the zip, replacing an existing asset of the same name.
upload_asset() {

	if gh release view "$TAG" --repo "$REPO" --json assets --jq '.assets[].name' | grep -qx "$ZIP_NAME"; then
		echo "Replacing existing $ZIP_NAME in release ${TAG}…"
		gh release delete-asset "$TAG" "$ZIP_NAME" --repo "$REPO" --yes
	fi

	gh release upload "$TAG" "$ZIP_NAME" --repo "$REPO"
	echo "Uploaded $ZIP_NAME to release $TAG"

}

main() {

	parse_args "$@"
	validate_args
	resolve_output_file
	require_tools
	check_release_state

	# Work in a staging directory that is removed on any exit. Deliberately not
	# named TMPDIR: that variable steers mktemp and other tools, and reassigning
	# it would redirect every child process's temp files into the staging tree.
	STAGE_ROOT="$(mktemp -d)"
	trap 'rm -rf "$STAGE_ROOT"' EXIT

	stage_source
	prune_to_runtime_files

	# Create the zip with a single top-level plugin directory.
	zip -qr "$ZIP_NAME" "$PLUGIN_DIR"
	echo "Created: $ZIP_NAME ($(du -h "$ZIP_NAME" | cut -f1))"

	# Deliver the zip to the requested destination.
	if [[ -n "$OUTPUT_FILE" ]]; then
		cp "$ZIP_NAME" "$OUTPUT_FILE"
		echo "Saved: $OUTPUT_FILE"
	fi

	[[ "$RELEASE_ACTION" == "create" ]] && create_release
	[[ -n "$RELEASE_ACTION" ]] && upload_asset

	return 0

}

main "$@"
