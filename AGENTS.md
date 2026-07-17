# kntnt-transparent-header-ollie-pro — agent guide

## Ground rules (authoritative)

Precedence over any conflicting skill, README or other doc unless the user overrides in the moment.

- Authoritative: only this file, the files it references and the actual code/state. Ignore `README*` and other narrative docs unless referenced here.
- Ships mechanism, never colour. Solid state = the background the user set on the header group in the Site Editor. A rule hard-coding a colour is a bug.

## Non-obvious

- `autoloader.php` stays hand-written. No runtime deps; `vendor/` is dev-only and unshipped → `vendor/autoload.php` is fatal on a normal install.

## References

- `agents.d/coding-standard/general.md` — before any code change
- `agents.d/coding-standard/php.md` — before PHP
- `agents.d/coding-standard/wordpress.md` — before WordPress plugin/theme code
- `agents.d/coding-standard/javascript-vanilla.md` — before build-less browser JS
- `agents.d/header-css.md` — before touching `css/header.css` or `js/header.js`
- `agents.d/deviations.md` — before `phpcs`/`phpcbf`, or "fixing" style or naming
- `agents.d/releasing.md` — cutting a release, or adding a runtime file
- `README.md` — Site Editor setup contract; cascade rationale
