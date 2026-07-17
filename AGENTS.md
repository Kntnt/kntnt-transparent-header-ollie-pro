# kntnt-transparent-header-ollie-pro — agent guide

## Ground rules (authoritative)

Precedence over any conflicting skill, README or other doc unless the user overrides in the moment.

- Authoritative: only this file, the files it references and the actual code/state.
- The plugin ships **mechanism, never colour**. The solid state is whatever background the user set on the header group in the Site Editor; a rule that hard-codes a colour is a bug.

## Non-obvious

- Namespace `Kntnt\Transparent_Header_Ollie_Pro`; slug / text-domain / asset handle / repo `kntnt-transparent-header-ollie-pro`. Classes are `Pascal_Snake_Case`, mapped 1:1 to `classes/<Class_Name>.php` (PSR-4).
- `autoloader.php` is hand-written and must stay that way: the plugin has no runtime dependency, `vendor/` is dev-only and unshipped, so requiring `vendor/autoload.php` would make the plugin fatal on a normal install.
- **The transparent state has no class of its own, deliberately.** It is the *absence* of `is-scrolled`. A script-applied class cannot exist at first paint, so an `is-transparent` class would flash a solid header on every load. Never invert this.
- The two CSS classes (`has-transparent-header`, `is-scrolled`) deliberately carry **no `kntnt-` prefix**, against the standard's naming rule: they follow core's block-class idiom, `has-transparent-header` is typed by hand in the Site Editor, and renaming them silently breaks live sites. Owner decision, 2026-07-17.
- **Never write the `transition` shorthand** on the header group. Ollie Pro sets `transition: transform …` at specificity (0,2,0); any shorthand landing there resets it and the header snaps instead of sliding. Longhand `transition-property` only.
- The theme guard (`get_template() === 'ollie'`) is **silent by design** — Ollie Pro is a hard dependency (`Requires Plugins`) and already reports a wrong theme. `get_template()`, not `get_stylesheet()`, so Ollie child themes pass.
- Four WP-CS deviations are intentional and pinned in `phpcs.xml.dist`; `phpcbf` will not revert them — do not "fix" toward upstream WP-CS (list in `agents.d/coding-standard/wordpress.md`).

## References

- `agents.d/coding-standard/general.md` — before writing or changing any code
- `agents.d/coding-standard/php.md` — before writing or changing PHP
- `agents.d/coding-standard/wordpress.md` — before writing or changing a WordPress plugin or theme
- `agents.d/coding-standard/javascript-vanilla.md` — before writing or changing build-less browser JavaScript
- `README.md` — the Site Editor setup contract and the rationale behind the cascade decisions; read before touching the CSS
