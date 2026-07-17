# Contributing to Kntnt Transparent Header for Ollie

Thank you for considering a contribution. This plugin does one small thing – it lets Ollie's sticky header lie transparently over a hero image – and contributions of every size help, from a typo fix to a new capability.

This file assumes nothing beyond a terminal. If you have never contributed to a GitHub project before, follow it top to bottom.

## Ways to contribute

- **Report a bug or request a feature.** [Open an issue](https://github.com/Kntnt/kntnt-transparent-header-ollie/issues), and search the existing issues first to avoid duplicates.
- **Ask a question or float an idea.** Use [Discussions](https://github.com/Kntnt/kntnt-transparent-header-ollie/discussions) rather than the issue tracker.
- **Submit a pull request.** Fix a bug, improve the documentation, add a translation or implement a feature.

For anything larger than a small fix, open an issue or a discussion first so the approach can be agreed before you invest the work.

## What is likely to be merged

The plugin ships **mechanism, never style**. It decides *when* the header is transparent; every question of what it then looks like – colour, opacity, shadow, radius, spacing, type – stays with the user, in the Site Editor. The solid state is simply whatever background they set on the header group.

So a change that hard-codes any of those, adds a settings page for one or otherwise makes a design decision on the user's behalf will not be merged. The whole point is that the plugin never guesses.

Likewise, it deliberately stays inside the gap Ollie and Ollie Pro leave open. Reimplementing sticky positioning, hide-on-scroll or menu behaviour belongs upstream in Ollie Pro, not here.

Before you change anything, read [`AGENTS.md`](AGENTS.md) and the [Development section of the README](README.md#development). Both record the decisions that look like mistakes until you know why – chiefly that the transparent state is the *absence* of a class, never a class of its own.

## What you need

- **Git** – [installation guide](https://git-scm.com/downloads).
- **PHP 8.3 or later** – check with `php -v`.
- **Composer** – [installation guide](https://getcomposer.org/download/).
- **GitHub account** – [sign up](https://github.com/signup) if you have none.
- **GitHub CLI (`gh`)** – [installation guide](https://cli.github.com/)

## Getting the code

Every change reaches this project as a **pull request from your own fork** – that is the only route in, since pushing a branch straight to this repository needs write access nobody outside the project has.

Click **Fork** at the top of [the repository](https://github.com/Kntnt/kntnt-transparent-header-ollie) to get your own copy, then replace `YOUR-USERNAME` below:

```bash
git clone https://github.com/YOUR-USERNAME/kntnt-transparent-header-ollie.git
cd kntnt-transparent-header-ollie
composer install
```

`composer install` creates a `vendor/` directory with the coding-standard tools. It is not shipped and not committed – the plugin loads its own classes through the hand-written `autoloader.php`, so the plugin works with no `vendor/` at all.

To run your copy on a real site, symlink or copy the directory into `wp-content/plugins/` of a WordPress install using [Ollie](https://wordpress.org/themes/ollie/) and [Ollie Pro](https://olliewp.com/pro/).

## Making a change

With the fork cloned, the work itself is an ordinary Git loop: branch, edit, check, commit, push. Keep each branch to a single concern – it becomes one pull request, and a focused one is both easier to review and easier to merge than a mixed one.

```bash
git checkout -b my-change      # never work directly on main
# … edit files …
composer gate                  # must be clean
git add -A
git commit -m "Describe what changed and why"
git push origin my-change      # origin is your fork, from the clone above
```

That last line goes to **your fork**, which is the clone's `origin` – never to this project.

## Commit messages

[`CHANGELOG.md`](CHANGELOG.md) follows [Keep a Changelog](https://keepachangelog.com/), and it is written from the commits at release time. Your commit messages are the raw material for it, so they are worth a minute each.

Write the subject line in the imperative, as an instruction to the codebase – ‘Fix the header snapping on Safari’, not ‘Fixed the header’ or ‘header fix’. Capitalise it, leave off the closing full stop and keep it to roughly 70 characters. This project does **not** use Conventional Commits: no `feat:`, no `fix(scope):`. Plain English reads better in a changelog and needs no translation to get there.

Name the change, not the file you touched. ‘Update header.css’ tells the changelog nothing; ‘Keep the header solid while a mega menu is open’ tells it everything. Where the *why* is not obvious from the subject, put it in the body – a blank line, then prose wrapped at 72 columns. The body is where a workaround explains which upstream bug it is dodging.

Do not edit `CHANGELOG.md` or the `Version:` header yourself. The maintainer reconciles both at release time; a version bump in a pull request only causes a conflict.

## Quality gates

```bash
composer gate      # both of the below, in order
composer phpcs     # WordPress Coding Standards, with the documented deviations
composer phpstan   # static analysis at level max, and the PHP floor
composer phpcbf    # fixes most violations automatically
shellcheck build-release-zip.sh
```

Both gates must be **clean** before you open a pull request.

`composer phpcs` must be **silent** – no errors and no warnings. Warnings fail the gate too, so anything it prints is a real finding, not noise to look past. Suppressing a finding needs a `phpcs:ignore` that names the sniff and gives the reason, at the smallest possible scope; there are currently none in the codebase, and that is the preferred state.

`composer phpstan` runs at `level: max` and is also the only thing enforcing the `Requires PHP` floor: `phpVersion` in `phpstan.neon.dist` is set to it, so syntax newer than the floor is reported – and that diagnostic is non-ignorable, so a baseline cannot bury it. Keep the two in step. phpcs cannot do this job; its `testVersion` setting belongs to PHPCompatibility, whose last stable release (9.3.5, 2019) knows nothing of PHP 8.0 or later and would pass this codebase in silence at any setting.

**A green gate is not the same as a change that follows the standard.** phpcs checks the mechanical surface – spacing, braces, array syntax, naming patterns – and that is the smaller half of what `agents.d/coding-standard/` asks of you. The rules that carry the most weight are precisely the ones no sniff can express: paragraph each block and give every paragraph a `//` topic sentence, write doc comments that carry the why instead of the what, leave out defensive code that the surrounding invariants already rule out, name things so the code reads as prose. Nothing will flag those. Read the standard and follow them anyway; review is where they surface.

Two of the unenforced rules are mechanical enough that a sniff *could* catch them, and one is planned for each: a comment on its own line stops at column 80 ([#37](https://github.com/Kntnt/kntnt-code-skills/issues/37)), and `=`/`=>` are never vertically aligned ([#38](https://github.com/Kntnt/kntnt-code-skills/issues/38)). Until those exist, aligned `=>` passes this gate in silence. Code lines, on the other hand, have no length limit at all – a long line is preferred over breaking arguments apart to satisfy a column count.

There is deliberately **no test suite**. The plugin's only logic is deciding when to enqueue two files, so there is nothing a unit test could meaningfully constrain that the two gates and a browser do not. If you add real logic, say so in the pull request and we will add tests with it.

## Coding and writing standards

- **Code** follows the project coding standard in [`agents.d/coding-standard/`](agents.d/coding-standard/) – read `general.md` plus the module for whatever you touch (WordPress, PHP, JavaScript, Bash).
- **The surface is WordPress's, not PSR-12's.** Tabs, `$snake_case`, `Pascal_Snake_Case` classes, padded parentheses – that is WP-CS, and PSR-12 gives way wherever the two disagree about how code should look.
- **The departures from WP-CS are where it shows its age.** WP-CS still carries rules written for a PHP that no longer exists, and there the project standard wins instead: `array()` predates the `[ ]` literal, `class-foo.php` filenames predate PSR-4 autoloading, `kntnt_` function prefixes predate namespaces, and Yoda conditions predate `strict_types`, which removed the assignment-typo hazard they were guarding against. `wordpress.md` names those four outright, and they are not quite the whole list – WP-CS also demands the `=`/`=>` alignment the general rules forbid, and forbids the blank lines the paragraph rule requires. `phpcs.xml.dist` switches off the sniff behind each one, with the reason spelled out above every exclusion. So the code follows neither standard whole, by intent: do not ‘correct’ any of it towards upstream WP-CS, and note that **`phpcbf` will not revert those deviations** – they are excluded, not tolerated.
- **Naming** follows the conventions in [`AGENTS.md`](AGENTS.md): namespace `Kntnt\Transparent_Header_Ollie`, slug and text domain `kntnt-transparent-header-ollie`, and the `kntnt_transparent_header_ollie_` prefix for anything in a global registry. The two CSS classes `has-transparent-header` and `is-scrolled` are a documented exception and carry no prefix.

## Pre-1.0 policy

While the major version is `0`, the project makes **no backwards-compatibility commitments**. Pick the cleanest end state and ship the breaking change rather than carrying migrations or deprecations. This policy sunsets automatically when the version crosses `1.0.0`.

The two CSS classes are the exception in spirit. `has-transparent-header` is typed by hand into live sites' Site Editor, and `is-scrolled` is what extenders' own CSS hangs on. Rename either and those sites break silently, with no error to warn anyone. Treat both as frozen.

## Pull-request process

1. Branch from `main` and keep each pull request focused on a single concern.
2. Make sure the quality gates above pass locally.
3. Open the pull request against `main`. GitHub shows a **Compare & pull request** button after you push to your fork, or use `gh pr create`.
4. Describe what changed and why. Link any related issue.

Your contribution ends there. The maintainer merges it and cuts the release afterwards; you never tag or publish anything yourself.

## Building a release ZIP locally

You do not need this to contribute – it is here for when you want to install your own build on a site, or you changed the build script and need to see what comes out.

```bash
./build-release-zip.sh                       # → dist/kntnt-transparent-header-ollie.zip
./build-release-zip.sh --output ~/Desktop    # → somewhere else
./build-release-zip.sh --help                # every option
```

The script stages the tree in a temporary directory, deletes everything that is not a runtime file and zips the rest. Your working tree is untouched. There is no compile or bundle step – the plugin ships the PHP, CSS and JS exactly as they are in the repository.

If you add a new runtime file or directory, add it to the `KEEP` array in `build-release-zip.sh`, or it will work locally and be missing from the release.

## Releasing

**Maintainers only.** It is documented here so the process is not a secret, not as a step for you to run. It requires push access to this repository and `gh`, and the `Version:` header in the main plugin file must already match the tag.

```bash
git tag v0.1.0
git push origin v0.1.0
./build-release-zip.sh --tag v0.1.0 --create   # opens the release and uploads the ZIP
```

`--create` takes the release notes from the matching section of [`CHANGELOG.md`](CHANGELOG.md). Use `--update` to replace the asset on a release that already exists. The asset name deliberately carries no version number, which is what keeps the permanent `latest/download` link working.

## Licence

By contributing, you agree that your contributions are licensed under the [GPL-2.0-or-later](LICENSE) licence that covers the project.
