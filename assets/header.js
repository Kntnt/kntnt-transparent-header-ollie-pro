/**
 * Marks the sticky header as scrolled once the page leaves the top.
 *
 * Ollie has no transparent-header mode — the word appears nowhere in the theme or
 * in Ollie Pro — so this class is the only hook the stylesheet has.
 *
 * It marks the SCROLLED state, not the transparent one, and that direction is the
 * whole point: a class added by script cannot exist in the first paint, so if
 * transparency depended on one, every page load would render the solid header and
 * then animate it away. The top state is the absence of this class, which costs
 * nothing and is right before this file has even run.
 */
(() => {
    const header = document.querySelector('header.has-transparent-header > .wp-block-group.is-position-sticky');

    if (!header) {
        return;
    }

    const SOLID_THRESHOLD = 20; // Scroll depth past which the header turns solid.

    let isTicking = false;

    const update = () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        header.classList.toggle('is-scrolled', scrollTop > SOLID_THRESHOLD);
    };

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

    update();
})();
