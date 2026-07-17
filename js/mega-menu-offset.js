/**
 * Hangs the mega menu panel from the transparent header's rendered bottom edge.
 *
 * Ollie Menu Designer opens a desktop mega menu as a `position: absolute` panel
 * whose `top` it measures from the nav item the menu belongs to (the block's
 * Top spacing setting), written as an inline style by the block's own script.
 * The nav item sits inside the header's vertical padding, so that top lands a
 * little above the header's bottom edge: the panel and the header overlap by a
 * narrow band. Both carry the same colour, so during this plugin's transparent-
 * to-solid fade the two semi-transparent copies composite to something brighter
 * than either – a bright band flashing across the header's lower edge for the
 * length of the fade. Meeting the two edge-to-edge removes the overlap, and
 * with it the band.
 *
 * The correct top is the header's bottom edge, which depends on the header's
 * padding and where the nav item sits within it – per-site, per-design, and
 * unknowable to CSS. Reading a rendered edge is the one thing CSS cannot do, so
 * this script measures the header group's live bottom edge, relative to the
 * panel's offset parent, into `--kntnt-mega-menu-top`; css/header.css consumes
 * it with `top: var(--kntnt-mega-menu-top, …) !important`, which beats the
 * inline top the block's script sets. (CSS anchor positioning is the eventual
 * pure-CSS replacement – see agents.d/removable-workarounds.md.)
 *
 * The value is written to a custom property, never to `top` directly: the
 * block's script rewrites the inline `top` on load and on resize, so a value
 * written there would be clobbered, whereas the stylesheet's `!important` reads
 * the property and wins regardless of when the block's script runs. Writing it
 * synchronously on the aria-expanded flip also settles the panel's top before
 * it paints, which lets the companion height-capping plugin (kntnt-modal-mega-
 * menu-ollie) read the corrected edge: that plugin measures the panel's live
 * top ~100ms after the menu opens, well after this has run.
 *
 * Desktop only. Ollie's mobile menu is a `position: fixed` full-screen overlay
 * with its own scroll and its own `top`; repositioning it would fight Ollie, so
 * a fixed panel is skipped.
 */
(() => {

    'use strict';

    // The sticky group carries the header's background, so its bottom edge is
    // the colour boundary the panel must meet. Every page loads this script, but
    // only a transparent header has anything to position against.
    const headerGroup = document.querySelector('header.has-transparent-header > .wp-block-group');

    if (!headerGroup) {
        return;
    }

    const menus = [...headerGroup.querySelectorAll('.wp-block-ollie-mega-menu')];

    if (menus.length === 0) {
        return;
    }

    const TOP_PROPERTY = '--kntnt-mega-menu-top';

    /**
     * Feeds one panel the header group's live bottom edge, relative to the
     * panel's offset parent, so the panel's top edge meets the header's bottom.
     *
     * @param {HTMLElement} panel The mega menu panel to position.
     * @returns {void}
     */
    const position = panel => {

        // Skip a panel that isn't laid out, and skip Ollie's mobile overlay: it
        // is `position: fixed` with its own top, which this must not fight.
        if (!panel.offsetParent || getComputedStyle(panel).position === 'fixed') {
            return;
        }

        const offset = headerGroup.getBoundingClientRect().bottom - panel.offsetParent.getBoundingClientRect().top;

        panel.style.setProperty(TOP_PROPERTY, `${offset}px`);

    };

    // Collect the panels once, and position each the moment its menu opens.
    const panels = [];

    menus.forEach(menu => {

        const toggle = menu.querySelector('.wp-block-ollie-mega-menu__toggle');
        const panel = menu.querySelector('.wp-block-ollie-mega-menu__menu-container');

        if (!toggle || !panel) {
            return;
        }

        panels.push(panel);

        // Position on the aria-expanded flip, synchronously, so the property is
        // set before the panel paints – no fallback flash, and the companion
        // height-capping plugin reads the corrected top.
        new MutationObserver(() => {
            if (toggle.getAttribute('aria-expanded') === 'true') {
                position(panel);
            }
        }).observe(toggle, { attributes: true, attributeFilter: ['aria-expanded'] });

    });

    let isTicking = false;

    // Re-measure on resize, coalesced to one update per frame: the header's
    // bottom edge moves with its padding across breakpoints. position() guards
    // the closed mobile overlay, so re-measuring every panel is safe.
    window.addEventListener('resize', () => {

        if (isTicking) {
            return;
        }

        isTicking = true;
        requestAnimationFrame(() => {
            panels.forEach(position);
            isTicking = false;
        });

    }, { passive: true });

})();
