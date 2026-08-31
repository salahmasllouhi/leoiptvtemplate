// Preserve the marketing campaign from a landing URL across every path to
// pricing or panel checkout. This runs site-wide: not just on the homepage.
(function () {
    var campaignId = new URLSearchParams(window.location.search).get('campaign_id');
    if (!campaignId) return;

    document.querySelectorAll('a[href]').forEach(function (anchor) {
        var target;
        try {
            target = new URL(anchor.href, window.location.href);
        } catch (_) {
            return;
        }

        var isPricing = target.origin === window.location.origin && target.hash.toLowerCase() === '#pricing';
        var isPanelCheckout = target.hostname === 'panel.nordictv.io' &&
            (target.pathname === '/checkout' || target.pathname.indexOf('/checkout/') === 0);

        if (isPricing || isPanelCheckout) {
            target.searchParams.set('campaign_id', campaignId);
            anchor.href = target.toString();
        }
    });
})();
