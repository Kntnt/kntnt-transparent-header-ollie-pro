/**
 * Marks the sticky header as scrolled once the page leaves the top.
 *
 * Ollie has no transparent-header mode – the word appears nowhere in the theme
 * or in Ollie Pro – so this class is the only hook the stylesheet has.
 *
 * It marks the SCROLLED state, not the transparent one, and that direction is
 * the whole point: a class added by script cannot exist in the first paint, so
 * if transparency depended on one, every page load would render the solid
 * header and then animate it away. The top state is the absence of this class,
 * which costs nothing and is right before this file has even run.
 */
(() => {

    'use strict';

    // The header group Ollie Pro made sticky, inside a header that opted into
    // transparent mode. Every page carries this script, but only such a page has
    // anything to toggle.
    const HEADER_SELECTOR = 'header.has-transparent-header > .wp-block-group.is-position-sticky';
    const SOLID_THRESHOLD = 20; // Scroll depth in px past which the header turns solid.
    const header = document.querySelector(HEADER_SELECTOR);

    if (!header) {
        return;
    }

    let isTicking = false;

    /**
     * Syncs the scrolled class with the current scroll position.
     *
     * @returns {void}
     */
    const update = () => {
        header.classList.toggle('is-scrolled', window.scrollY > SOLID_THRESHOLD);
    };

    // Coalesce a burst of scroll events into one class update per frame. The
    // listener is passive: it never calls preventDefault, and declaring that keeps
    // it off the scrolling critical path.
    window.addEventListener('scroll', () => {

        if (isTicking) {
            return;
        }

        isTicking = true;
        requestAnimationFrame(() => {
            update();
            isTicking = false;
        });

    }, { passive: true });

    // Settle the initial state: a reload can restore a scrolled position, which
    // fires no event of its own.
    update();

})();
