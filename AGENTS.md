# kntnt-transparent-header-ollie – agent guide

## Ground rules (authoritative)

Precedence over any conflicting skill, README or other doc unless the user overrides in the moment.

- Authoritative: only this file, the files it references and the actual code/state. Ignore `README*` and other narrative docs unless referenced here.
- Ships mechanism, never style. The look — colour, opacity, shadow, spacing, type — stays with the user in the Site Editor; the solid state is just the background they set on the header group. A rule that hard-codes any of it is a bug.

## Non-obvious

- `autoloader.php` stays hand-written. No runtime deps; `vendor/` is dev-only and unshipped → `vendor/autoload.php` is fatal on a normal install.

## References

- `agents.d/coding-standard/general.md` – before any code change
- `agents.d/coding-standard/php.md` – before PHP
- `agents.d/coding-standard/wordpress.md` – before WordPress plugin/theme code
- `agents.d/coding-standard/javascript-vanilla.md` – before build-less browser JS
- `agents.d/coding-standard/bash.md` – before Bash
- `agents.d/header-css.md` – before touching `css/header.css`, `js/header.js`, `js/admin-bar-offset.js`, or `js/mega-menu-offset.js`
- `agents.d/removable-workarounds.md` – which fixes are crutches for upstream (Ollie theme/Ollie Pro) bugs or browser-support gaps, and the exact condition for dropping each
- `agents.d/deviations.md` – before `phpcs`/`phpcbf`, or ‘fixing’ style or naming
- `agents.d/releasing.md` – cutting a release, or adding a runtime file
- `README.md` – Site Editor setup contract; cascade rationale. §Extending and §Why you can't simplify this are mirrored by `agents.d/header-css.md` – change both.
