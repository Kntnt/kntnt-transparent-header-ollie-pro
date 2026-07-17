/**
 * Pins the transparent header below the admin bar as the bar scrolls away.
 *
 * Loaded only for logged-in users – the offset matters only while an admin bar
 * exists, and keeping it off public pages also keeps it clear of the caching
 * and minification those pages get but logged-in ones do not.
 *
 * The stylesheet already resolves the offset above 600px, where core fixes the
 * admin bar to the viewport and its height is a constant band. At 600px and
 * below core makes the bar `absolute`: it scrolls away with the document, yet
 * `--wp-admin--admin-bar--height` stays 46px, so a static offset would leave
 * the header hanging below an empty band once the bar has scrolled out. Reading
 * the scroll position is the one thing CSS cannot do, so this script measures
 * the bar's live bottom edge into `--kntnt-admin-bar-offset`, which the
 * stylesheet reads. It runs only below 600px, the only range where the offset
 * moves. (The solid sticky header needs none of this – being `sticky` it tucks
 * under the bar in the flow, so css/header.css resets it with a static `top: 0`;
 * only the `fixed` transparent header needs the live measurement.)
 */
(() => {

    'use strict';

    // The header that opted into transparent mode carries the `top` the
    // stylesheet pins to the admin bar; the bar is the thing whose edge moves.
    const header = document.querySelector('header.has-transparent-header');
    const adminBar = document.getElementById('wpadminbar');

    if (!header || !adminBar) {
        return;
    }

    // The offset only moves at 600px and below, where core makes the admin bar
    // scroll away. Above it the stylesheet's static fallback is already correct,
    // so the script attaches nothing there.
    const mobile = window.matchMedia('(max-width: 600px)');
    const OFFSET_PROPERTY = '--kntnt-admin-bar-offset';

    let isTicking = false;

    /**
     * Pins the header's top offset to the admin bar's visible bottom edge.
     *
     * Clamping the edge at zero lets one measure cover the whole range: the edge
     * falls to zero as the absolute bar scrolls out, and the header sits flush.
     *
     * @returns {void}
     */
    const update = () => {

        const offset = Math.max(0, adminBar.getBoundingClientRect().bottom);
        const value = `${offset}px`;

        // Skip the write when nothing changed: once the bar has scrolled out the
        // value stays 0px and every further frame would be a no-op.
        if (header.style.getPropertyValue(OFFSET_PROPERTY) !== value) {
            header.style.setProperty(OFFSET_PROPERTY, value);
        }

    };

    /**
     * Coalesces a burst of scroll events into one update per frame. Passive: it
     * never calls preventDefault, which keeps it off the scrolling critical path.
     *
     * @returns {void}
     */
    const onScroll = () => {

        if (isTicking) {
            return;
        }

        isTicking = true;
        requestAnimationFrame(() => {
            update();
            isTicking = false;
        });

    };

    /**
     * Tracks the bar below 600px and stands down above it, handing `top` back to
     * the stylesheet's static fallback by clearing the property on the way up.
     *
     * @returns {void}
     */
    const sync = () => {

        if (mobile.matches) {
            window.addEventListener('scroll', onScroll, { passive: true });
            update();
        } else {
            window.removeEventListener('scroll', onScroll);
            header.style.removeProperty(OFFSET_PROPERTY);
        }

    };

    // Re-evaluate whenever the viewport crosses 600px, then settle the initial
    // state – a reload can restore a scrolled position, which fires no event.
    mobile.addEventListener('change', sync);
    sync();

})();
