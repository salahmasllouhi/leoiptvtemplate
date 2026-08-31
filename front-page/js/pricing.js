// Pricing JavaScript - Screen/Duration configurator
(function () {
    let selectedDevices = null;
    let selectedDuration = null;

    const btn = document.getElementById('checkout-btn');
    const btnText = document.getElementById('button-text');
    const deviceGroup = document.getElementById('devices');
    const durationGroup = document.getElementById('durations');

    if (!btn || !btnText || !deviceGroup || !durationGroup) return;

    // Some header/sticky CTAs use an absolute homepage URL such as
    // "https://nordictv.io/#pricing". That navigation would otherwise replace
    // the landing URL and drop the campaign ID before checkout is reached.
    const landingCampaignId = new URLSearchParams(window.location.search).get('campaign_id');
    if (landingCampaignId) {
        document.querySelectorAll('a[href]').forEach(function (anchor) {
            const target = new URL(anchor.href, window.location.href);
            if (target.origin === window.location.origin && target.hash === '#pricing') {
                target.searchParams.set('campaign_id', landingCampaignId);
                anchor.href = target.toString();
            }
        });
    }

    const deviceCards = Array.from(deviceGroup.querySelectorAll('[data-devices]'));
    const durationCards = Array.from(durationGroup.querySelectorAll('[data-duration]'));

    // Get price from window.iptvPrices
    function getPrice(devices, months) {
        if (!window.iptvPrices) return 0;
        const durationMap = { 1: '1_month', 3: '3_months', 6: '6_months', 12: '12_months' };
        const deviceKey = devices === 1 ? '1_device' : devices + '_devices';
        const durationKey = durationMap[months];
        const currency = window.currentCurrency || 'usd';

        if (window.iptvPrices[durationKey] && window.iptvPrices[durationKey][deviceKey]) {
            return parseFloat(window.iptvPrices[durationKey][deviceKey][currency]) || 0;
        }
        return 0;
    }

    // Get currency data for formatting
    function getCurrencyData() {
        const currencyData = {
            usd: { symbol: '$', position: 'before' },
            eur: { symbol: '€', position: 'before' },
            sek: { symbol: 'kr', position: 'after' },
            nok: { symbol: 'kr', position: 'after' },
            dkk: { symbol: 'kr', position: 'after' },
            isk: { symbol: 'kr', position: 'after' }
        };
        return currencyData[window.currentCurrency || 'usd'];
    }

    function formatPrice(price) {
        const data = getCurrencyData();
        const currency = window.currentCurrency || 'usd';
        let formatted = (currency === 'usd' || currency === 'eur') ? price.toFixed(2) : Math.round(price).toString();
        return data.position === 'before' ? data.symbol + formatted : formatted + ' ' + data.symbol;
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    // Savings badges are derived from the live prices so the claim stays true
    // when the screen count (and therefore the price ladder) changes.
    function updateSavings(deviceCount) {
        const base = getPrice(deviceCount, 1);

        [3, 6, 12].forEach(function (months) {
            const el = document.getElementById('save-' + months + 'mo');
            if (!el) return;

            const total = getPrice(deviceCount, months);
            if (!base || !total) {
                el.classList.add('is-hidden');
                return;
            }

            const pct = Math.round((1 - (total / months) / base) * 100);
            if (pct > 0) {
                el.textContent = 'Save ' + pct + '%';
                el.classList.remove('is-hidden');
            } else {
                el.classList.add('is-hidden');
            }
        });
    }

    // Update duration cards with prices based on selected screen count
    function updatePrices(deviceCount) {
        [1, 3, 6, 12].forEach(function (months) {
            const price = getPrice(deviceCount, months);
            setText('price-' + months + 'mo', formatPrice(price));
            setText(
                'per-' + months + 'mo',
                months === 1 ? 'per month' : '~' + formatPrice(price / months) + '/mo'
            );
        });

        updateSavings(deviceCount);
    }

    // "Your total" card. The struck-through price is the same plan bought month
    // by month, so the saving shown is a real comparison rather than a markup.
    const totalAside = document.getElementById('total-aside');
    const totalLock = document.getElementById('total-lock');
    const lockCopy = document.getElementById('total-lock-copy');
    const lockFormat = lockCopy ? lockCopy.textContent.trim().replace(/\d+%/, '{pct}%') : '';

    function updateTotal(devices, months) {
        if (!devices || !months) return;

        const now = getPrice(devices, months);
        const rate = getPrice(devices, 1);
        const was = rate * months;
        const off = Math.max(0, was - now);
        const pct = was > 0 ? Math.round((off / was) * 100) : 0;

        setText('total-price', formatPrice(now));
        setText('total-was', formatPrice(was));
        setText('total-save', 'Save ' + formatPrice(off) + ' (' + pct + '%)');
        setText(
            'total-meta',
            months === 1
                ? 'one-time · ' + formatPrice(now) + '/mo'
                : 'one-time · ' + formatPrice(now / months) + '/mo'
        );

        // A 1-month plan is the reference price, so there is nothing to compare
        // it against and no discount to hold.
        const discounted = months > 1 && off > 0.005;

        if (totalAside) totalAside.classList.toggle('is-hidden', !discounted);
        if (totalLock) totalLock.classList.toggle('is-hidden', !discounted);

        if (lockCopy && lockFormat) {
            lockCopy.textContent = lockFormat.replace('{pct}', pct);
        }
    }

    // Countdown for the locked discount. The deadline is stored locally so it
    // keeps ticking down across visits instead of restarting on every load.
    (function startCountdown() {
        const lock = document.querySelector('[data-offer-days]');
        const out = document.getElementById('total-countdown');
        if (!lock || !out) return;

        const days = parseInt(lock.dataset.offerDays, 10) || 5;
        const window_ms = days * 24 * 60 * 60 * 1000;
        const key = 'iptvOfferDeadline';

        let deadline = 0;
        try {
            deadline = parseInt(localStorage.getItem(key), 10) || 0;
        } catch (e) {
            deadline = 0;
        }

        // Seed on first visit, and re-seed once the window has fully elapsed.
        if (!deadline || deadline <= Date.now()) {
            deadline = Date.now() + window_ms;
            try { localStorage.setItem(key, String(deadline)); } catch (e) { /* private mode */ }
        }

        function pad(n) { return n < 10 ? '0' + n : String(n); }

        function tick() {
            let left = Math.max(0, deadline - Date.now());
            const d = Math.floor(left / 86400000);
            left -= d * 86400000;
            const h = Math.floor(left / 3600000);
            left -= h * 3600000;
            const m = Math.floor(left / 60000);
            const s = Math.floor((left - m * 60000) / 1000);
            out.textContent = d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s);
        }

        tick();
        setInterval(tick, 1000);
    })();

    // Keep a marketing campaign from the landing URL when the selected plan is
    // sent to the panel. This is intentionally separate from referrals.
    function checkoutUrl(devices, months) {
        const base = window.iptvCheckoutBase || 'https://panel.nordictv.io/checkout';
        const url = new URL(base);
        url.searchParams.set('connections', devices);
        url.searchParams.set('duration', months);
        const campaignId = new URLSearchParams(window.location.search).get('campaign_id');
        if (campaignId) url.searchParams.set('campaign_id', campaignId);
        return url.toString();
    }

    const ctaLabel = btnText.textContent.trim() || 'Start watching';

    function updateButton() {
        updateTotal(selectedDevices, selectedDuration);

        if (selectedDevices && selectedDuration) {
            const price = getPrice(selectedDevices, selectedDuration);
            btnText.textContent = price
                ? ctaLabel + ' · ' + formatPrice(price)
                : ctaLabel;
            btn.href = checkoutUrl(selectedDevices, selectedDuration);
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        } else {
            // Both pickers default to a value, so this is only reachable if the
            // markup is missing a default.
            btnText.textContent = ctaLabel;
            btn.style.opacity = '0.6';
            btn.style.pointerEvents = 'none';
        }
    }

    // Reflect selection on a radiogroup: one checked item, roving tabindex.
    function select(cards, chosen) {
        cards.forEach(function (card) {
            const isChosen = card === chosen;
            card.classList.toggle('active', isChosen);
            card.setAttribute('aria-checked', isChosen ? 'true' : 'false');
            if (card.hasAttribute('aria-pressed')) {
                card.setAttribute('aria-pressed', isChosen ? 'true' : 'false');
            }
            card.tabIndex = isChosen ? 0 : -1;
        });
    }

    function chooseDevice(card, focus) {
        select(deviceCards, card);
        selectedDevices = parseInt(card.dataset.devices, 10);
        updatePrices(selectedDevices);
        updateButton();
        if (focus) card.focus();
    }

    function chooseDuration(card, focus) {
        select(durationCards, card);
        selectedDuration = parseInt(card.dataset.duration, 10);
        updateButton();
        if (focus) card.focus();
    }

    // Arrow-key navigation, as expected of a radiogroup.
    function bindKeys(cards, choose) {
        cards.forEach(function (card, index) {
            card.addEventListener('keydown', function (e) {
                let next = null;
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    next = cards[(index + 1) % cards.length];
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    next = cards[(index - 1 + cards.length) % cards.length];
                } else if (e.key === 'Home') {
                    next = cards[0];
                } else if (e.key === 'End') {
                    next = cards[cards.length - 1];
                }
                if (next) {
                    e.preventDefault();
                    choose(next, true);
                }
            });
        });
    }

    deviceCards.forEach(function (card) {
        card.addEventListener('click', function () { chooseDevice(card, false); });
    });
    durationCards.forEach(function (card) {
        card.addEventListener('click', function () { chooseDuration(card, false); });
    });

    bindKeys(deviceCards, chooseDevice);
    bindKeys(durationCards, chooseDuration);

    // Pre-select both pickers so prices and the checkout URL are live on load
    // (default landing URL: ?connections=1&duration=12).
    const defaultScreens = parseInt(window.iptvDefaultScreens, 10) || 1;
    const defaultMonths = parseInt(window.iptvDefaultMonths, 10) || 12;

    const defaultDeviceCard = deviceCards.find(function (card) {
        return parseInt(card.dataset.devices, 10) === defaultScreens;
    }) || deviceCards[0];

    const defaultDurationCard = durationCards.find(function (card) {
        return parseInt(card.dataset.duration, 10) === defaultMonths;
    }) || durationCards[0];

    if (defaultDeviceCard) chooseDevice(defaultDeviceCard, false);
    if (defaultDurationCard) chooseDuration(defaultDurationCard, false);
    updateButton();

    // currency.js calls this after switching currency so the per-month lines,
    // savings badges and CTA price all re-render, not just the headline price.
    window.iptvRefreshPricing = function () {
        updatePrices(selectedDevices || defaultScreens);
        updateButton();
    };
})();
