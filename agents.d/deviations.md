Read before running `phpcs`/`phpcbf`, or "fixing" style or naming.

- `composer phpcs` must be **silent**: 0 errors, 0 warnings. Warnings fail the gate — no expected noise to look past. Never add `ignore_warnings_on_exit`.
- The 2 unwrappable lines (plugin-header `Description:`, the translatable string) carry their own `phpcs:ignore` / `phpcs:disable` naming the sniff and the reason. That is the only sanctioned suppression: name the sniff, give the reason, smallest possible scope.
- The translators comment must sit **directly** above its `__()` call — nothing between, not even an annotation. Hence `disable`/`enable` around that block rather than `ignore` on the line.
- 4 deliberate WP-CS deviations, excluded in `phpcs.xml.dist`: `[ ]` arrays, PSR-4 filenames, namespaces over `kntnt_` prefixes, no Yoda. Listed in `agents.d/coding-standard/wordpress.md`. Never correct toward upstream WP-CS.
- 3 more sniffs excluded where WPCS contradicts the standard: demands the `=>` alignment `general.md` forbids; forbids the paragraph rule's trailing blank lines. `phpcbf` reverts none — sniffs off, not tolerated.
- CSS classes carry **no `kntnt-` prefix**, against `general.md`: core block-class idiom, and `has-transparent-header` is typed by hand into live Site Editors → rename breaks sites silently. Owner decision 2026-07-17. Frozen.
