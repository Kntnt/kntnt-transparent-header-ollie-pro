# Removable workarounds — what to drop when upstream catches up

Read when Ollie, Ollie Pro, or browser support moves. Everything here is a crutch the plugin would rather not carry; each entry names the exact upstream change that lets you delete it. The mechanics and specificity maths live in `header-css.md` – this file is only the removal roadmap.

## Workarounds for upstream bugs (drop when the fix ships)

Each works around a bug in the named product, written up as an upstream report. It is gone the moment that product ships its fix: verify with the check, then delete.

| In the code | Works around | Delete it when |
|---|---|---|
| `--sticky-full-offset: var(--wp-admin--admin-bar--height, 0px)` (`css/header.css`) | **Ollie Pro** leaves this variable unset unless a *Top Offset* is configured, so its own hide-on-scroll-up header doesn't translate fully off-screen and leaves a strip at ≤600px. | Ollie Pro sets `--sticky-full-offset` itself. Check: a hide-on-scroll header with no Top Offset, ≤600px, logged in — scroll down; the header hides with no leftover strip. |
| `@media (max-width: 600px) { … header:not(.has-transparent-header):has(> [data-sticky-on-scroll-up]) { top: 0 } }` (`css/header.css`) | The **Ollie theme** ships this exact reset already, but at specificity (0,1,1); its own base rule `body:not(.wp-admin) header:has(> .is-position-sticky)` (0,2,2) outweighs it, so the reset never applies and the solid sticky header stays 46px low at ≤600px. | The theme gives its own reset enough specificity to win. Check: a solid sticky header, ≤600px, logged in, scrolled — `top` computes to `0` with our rule removed. Then delete our `@media` block. |
| `transition-property: transform, background-color, box-shadow` (`css/header.css`) | **Ollie Pro** writes `transition: transform …` as a *shorthand*, which resets any transition list a later stylesheet sets — so a naïve fade declaration would kill Ollie Pro's slide. | Only *simplifiable*, not removable: the transparent fade needs `background-color` and `box-shadow` in the list regardless. If Ollie Pro moves to `transition-*` longhands, the shorthand-reset hazard (and the caution around it) disappears, but keep declaring the property list. |
| `transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), …` on the mega menu panel (`css/header.css`) | Not a bug — a **hard-coded copy of Ollie Pro's sticky-slide timing** (`0.3s cubic-bezier(0.4, 0, 0.2, 1)`), because CSS cannot read another element's transition timing and the panel's fade must match the header's. The number will drift out of sync if Ollie Pro retimes its slide. | Only *simplifiable*, never removable while a mega menu can open over a transparent header: the panel must fade on the header's timing regardless. If Ollie Pro ever exposes its sticky duration/easing as a CSS custom property, read that instead of duplicating the constant. Until then, if Ollie Pro's slide timing changes, change this to match (both places). |

## The JS kludge (replace with pure CSS when browsers catch up)

`js/admin-bar-offset.js`, and the `top: var(--kntnt-admin-bar-offset, …)` it feeds on `header.has-transparent-header`, is a **kludge, not an upstream bug fix**. It exists only because CSS today cannot read scroll position — which the `fixed` transparent header needs, so it can follow the admin bar as the bar scrolls away at ≤600px. (The solid header escapes this: being `sticky` it tucks under the bar in the flow, so the pure-CSS reset above is enough.)

It is replaceable *whole* by CSS anchor positioning:

```css
#wpadminbar { anchor-name: --wpadminbar; }
body:not(.wp-admin) header.has-transparent-header {
  top: max(0px, anchor(--wpadminbar bottom, 0px));
}
```

`anchor()` reads the bar's real bottom edge, `max(0px, …)` clamps it once the bar scrolls out, and the `0px` fallback covers logged-out visitors — the same three cases the script handles, with no JavaScript.

**Do it when CSS anchor positioning is in stable Firefox *and* Safari.** Chromium has shipped it since 125; as of now Firefox and Safari have not — and iOS Safari *is* the ≤600px audience, so shipping the CSS form early would regress the exact devices the bug lives on. Scroll-driven animations (`animation-timeline: scroll()`) could express it too, but with the same Safari gap and more moving parts.

When you switch: delete `js/admin-bar-offset.js`, its enqueue in `Plugin.php` (the `is_admin_bar_showing()` block), and the `--kntnt-admin-bar-offset` variable; change the transparent rule's `top` to the `anchor()` form above.

`js/mega-menu-offset.js`, and the `top: var(--kntnt-mega-menu-top, …) !important` it feeds on the mega menu panel, is the **same kind of kludge**. It exists only because CSS cannot read the header's rendered bottom edge, which the panel needs so it can hang flush instead of overlapping the header and flashing a bright band during the fade.

It too is replaceable *whole* by CSS anchor positioning — anchor the panel to the header group's bottom:

```css
header.has-transparent-header > .wp-block-group { anchor-name: --kntnt-header; }
@media (min-width: 600px) {
  header.has-transparent-header .wp-block-ollie-mega-menu__menu-container {
    top: anchor(--kntnt-header bottom) !important;
  }
}
```

Same browser gate as the admin-bar kludge: **do it when anchor positioning is in stable Firefox *and* Safari.** The `!important` stays either way, because Ollie Menu Designer sets the panel's `top` as an inline style. When you switch: delete `js/mega-menu-offset.js`, its enqueue in `Plugin.php`, and the `--kntnt-mega-menu-top` variable; change the panel rule's `top` to the `anchor()` form above. The fade-sync rule is unaffected — it is CSS already, and stays until Ollie Pro exposes its timing (see the table).
