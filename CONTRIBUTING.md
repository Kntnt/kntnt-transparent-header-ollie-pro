# Contributing to Kntnt Transparent Header for Ollie Pro

Thank you for considering a contribution. This plugin does one small thing — it lets Ollie's sticky header lie transparently over a hero image — and contributions of every size help, from a typo fix to a new capability.

## Ways to contribute

- **Report a bug or request a feature.** [Open an issue](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/issues), and search the existing issues first to avoid duplicates.
- **Ask a question or float an idea.** Use [Discussions](https://github.com/Kntnt/kntnt-transparent-header-ollie-pro/discussions) rather than the issue tracker.
- **Submit a pull request.** Fix a bug, improve the documentation, add a translation or implement a feature.

For anything larger than a small fix, open an issue or a discussion first so the approach can be agreed before you invest the work.

## What is likely to be merged

The plugin ships **mechanism, never colour**. The solid state is whatever background the user set on the header group in the Site Editor. A change that hard-codes a colour, adds a settings page for one, or otherwise makes design decisions on the user's behalf will not be merged — the whole point is that the plugin never guesses.

Likewise, it deliberately stays inside the gap Ollie and Ollie Pro leave open. Reimplementing sticky positioning, hide-on-scroll or menu behaviour belongs upstream in Ollie Pro, not here.

## Development setup

The step-by-step version, including what to install, is in the [Development section of the README](README.md#development). In short:

```bash
git clone https://github.com/YOUR-USERNAME/kntnt-transparent-header-ollie-pro.git
cd kntnt-transparent-header-ollie-pro
composer install
```

The plugin requires **PHP 8.3**. It has no runtime dependencies; `composer install` only fetches the coding-standard tools, and `vendor/` is neither committed nor shipped.

## Quality gates

Run these before opening a pull request:

```bash
composer phpcs                    # must be silent: 0 errors, 0 warnings
composer phpcbf                   # fixes most violations automatically
shellcheck build-release-zip.sh   # if you touched the build script
```

`phpcs` fails on warnings as well as errors, so a clean run means clean — there is no expected noise to look past. Silencing a finding needs a `phpcs:ignore` naming the sniff and the reason, at the smallest possible scope; there are none in the codebase today, and that is the preferred state.

Two rules the standard states are **not** enforced, because no phpcs sniff can express them: a comment on its own line stops at column 80, and `=`/`=>` are never vertically aligned. Both await a custom sniff ([#37](https://github.com/Kntnt/kntnt-code-skills/issues/37), [#38](https://github.com/Kntnt/kntnt-code-skills/issues/38)). A green gate does not mean you followed them — read `agents.d/coding-standard/general.md` and `php.md`.

There is deliberately **no test suite**. The plugin's only logic is deciding when to enqueue two files, so there is nothing a unit test could meaningfully constrain. If you add real logic, say so in the pull request and we will add tests with it.

## Coding and writing standards

- **Code** follows the project coding standard in [`agents.d/coding-standard/`](agents.d/coding-standard/) — read `general.md` plus the module for whatever you touch (PHP, WordPress, JavaScript). The paragraph rule in `general.md` is the most important one: group statements into paragraphs with a `//` topic sentence above each.
- **Four deliberate deviations from the WordPress Coding Standards** are intentional — `[ ]` arrays, PSR-4 filenames, namespaces over global function prefixes, and no required Yoda conditions. They are excluded in `phpcs.xml.dist` and must not be "corrected" toward upstream WP-CS. Several further sniffs are excluded where WP-CS contradicts the standard outright (it demands `=>` alignment the standard forbids, and forbids the blank lines the paragraph rule requires).
- **Naming** follows the conventions in [`AGENTS.md`](AGENTS.md): namespace `Kntnt\Transparent_Header_Ollie_Pro`, slug and text domain `kntnt-transparent-header-ollie-pro`, and the `kntnt_transparent_header_ollie_pro_` prefix for anything in a global registry. The two CSS classes are a documented exception and carry no prefix.
- **Read [`AGENTS.md`](AGENTS.md) first.** It records the decisions that look like mistakes until you know why — chiefly that the transparent state is the *absence* of a class, never a class of its own.

## Pre-1.0 policy

While the major version is `0`, the project makes **no backwards-compatibility commitments**. Pick the cleanest end state and ship the breaking change rather than carrying migrations or deprecations. This policy sunsets automatically when the version crosses `1.0.0`.

The two CSS classes are the exception in spirit: `has-transparent-header` is typed by hand into live sites' Site Editor, so renaming it breaks sites silently, with no error to warn anyone. Treat it as frozen.

## Pull-request process

1. Branch from `main` and keep each pull request focused on a single concern.
2. Make sure the quality gates above pass locally.
3. Open the pull request against `main`.
4. Describe what changed and why. Link any related issue.

## Licence

By contributing, you agree that your contributions are licensed under the [GPL-2.0-or-later](LICENSE) licence that covers the project.
