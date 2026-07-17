Read before touching `css/header.css` or `js/header.js`.

Digest of `README.md` §Extending and §Why you can't simplify this, which hold the full rationale and the measurements. Change one, change both.

- Contract = 2 classes: `has-transparent-header` (user adds, on Template Part block), `is-scrolled` (plugin adds, past 20px).
- Transparent = **absence** of `is-scrolled`. Never an `is-transparent` class: a script-set class cannot exist at first paint → header flashes solid, then fades. Never invert.
- Transparent-mode rules are scoped to `header.has-transparent-header`. The two Ollie Pro fixes key off `.wp-block-group[data-sticky-on-scroll-up="true"]` and are unscoped **on purpose** – the defects hit every sticky header, transparent or not; scoping them re-breaks solid headers. Never ‘tidy’ them under the class. Corollary: only the transparent-mode rules (and Ollie's own sticky rule) need the `<header>` tag – the two fixes don't name it.
- Transparent-state rule keeps both its `!important` (core emits the block's own preset background as `.has-<slug>-background-color { background-color: … !important }` – nothing weaker beats it) and its `:not(:has([aria-expanded="true"]))` clause (an open menu drops the header to solid, so the panel gets a backdrop). Phrased ‘nothing is open’, never ‘something is closed’: the latter needs a closed element to exist.
- Never write the `transition` shorthand on the header group. Ollie Pro sets `transition: transform …` at (0,2,0); a shorthand landing there resets it → header snaps, not slides. `transition-property` longhand only.
- Stylesheet must load after the theme's: enqueue priority 20, dep on the `ollie` handle. `position: fixed` ties Ollie's sticky rule at (0,2,2) – source order alone decides.
- Requires *Hide in Scroll Down* (`data-sticky-on-scroll-up="true"`) on the group. Without it Ollie Pro's JS writes an inline `position` on the `<header>`, which beats the plugin's non-`!important` `fixed` rule → header never goes transparent. This is why the setup contract makes that toggle mandatory, and part of why the `fixed` rule can't just add `!important`.
