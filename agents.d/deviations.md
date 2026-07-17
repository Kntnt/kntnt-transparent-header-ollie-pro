Read before running `phpcs`/`phpcbf`, or "fixing" style or naming.

- `composer phpcs` stays at **0 errors**. 2 long-line warnings expected: plugin-header `Description:`, translators string. Neither splittable.
- 4 deliberate WP-CS deviations, excluded in `phpcs.xml.dist`: `[ ]` arrays, PSR-4 filenames, namespaces over `kntnt_` prefixes, no Yoda. Listed in `agents.d/coding-standard/wordpress.md`. Never correct toward upstream WP-CS.
- 3 more sniffs excluded where WPCS contradicts the standard: demands the `=>` alignment `general.md` forbids; forbids the paragraph rule's trailing blank lines. `phpcbf` reverts none — sniffs off, not tolerated.
- CSS classes carry **no `kntnt-` prefix**, against `general.md`: core block-class idiom, and `has-transparent-header` is typed by hand into live Site Editors → rename breaks sites silently. Owner decision 2026-07-17. Frozen.
