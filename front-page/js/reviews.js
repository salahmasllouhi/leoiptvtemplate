// Reviews carousel — arrow paging, no autoplay.
//
// The row scrolls natively (scroll-snap on the viewport), so the arrows only
// have to nudge scrollLeft by one page and keep their own disabled state in
// step with where the row ended up. Dragging or flicking the row directly
// therefore stays in sync for free.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-review-carousel]').forEach(function (root) {
        var viewport = root.querySelector('[data-review-viewport]');
        var prev = root.querySelector('[data-review-prev]');
        var next = root.querySelector('[data-review-next]');

        if (!viewport || !prev || !next) {
            return;
        }

        // Advance by whole cards rather than by viewport width, so a page never
        // leaves a card straddling the edge.
        function step() {
            var card = viewport.querySelector('.dv2-review-card');
            if (!card) {
                return viewport.clientWidth;
            }
            var styles = window.getComputedStyle(card);
            var width = card.offsetWidth + parseFloat(styles.marginRight || 0);
            // Whole cards per view, but always at least one.
            var perView = Math.max(1, Math.floor(viewport.clientWidth / width));
            return width * perView;
        }

        function sync() {
            // scrollWidth - clientWidth is the maximum scrollLeft. The 1px
            // slack absorbs sub-pixel rounding, which otherwise leaves "next"
            // enabled at the end of the row on fractional-DPI displays.
            var max = viewport.scrollWidth - viewport.clientWidth;
            prev.disabled = viewport.scrollLeft <= 1;
            next.disabled = viewport.scrollLeft >= max - 1;
        }

        prev.addEventListener('click', function () {
            viewport.scrollBy({ left: -step(), behavior: 'smooth' });
        });

        next.addEventListener('click', function () {
            viewport.scrollBy({ left: step(), behavior: 'smooth' });
        });

        viewport.addEventListener('scroll', sync, { passive: true });
        window.addEventListener('resize', sync);
        sync();
    });
});
