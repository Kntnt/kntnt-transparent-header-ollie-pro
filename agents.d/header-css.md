Read before touching `css/header.css` or `js/header.js`.

Digest of `README.md` §Extending and §Why you can't simplify this, which hold the full rationale and the measurements. Change one, change both.

- Contract = 2 classes: `has-transparent-header` (user adds, on Template Part block), `is-scrolled` (plugin adds, past 20px).
- Transparent = **absence** of `is-scrolled`. Never an `is-transparent` class: a script-set class cannot exist at first paint → header flashes solid, then fades. Never invert.
- Never write the `transition` shorthand on the header group. Ollie Pro sets `transition: transform …` at (0,2,0); a shorthand landing there resets it → header snaps, not slides. `transition-property` longhand only.
- Stylesheet must load after the theme's: enqueue priority 20, dep on the `ollie` handle. `position: fixed` ties Ollie's sticky rule at (0,2,2) — source order alone decides.
