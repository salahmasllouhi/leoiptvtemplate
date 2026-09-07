// Currency Selector JavaScript v2
function toggleCountryDropdown() {
    const dropdown = document.getElementById('countryDropdown');
    dropdown.classList.toggle('active');
}

// Footer dropdown toggle
function toggleFooterDropdown() {
    const dropdown = document.getElementById('footerCountryDropdown');
    if (dropdown) dropdown.classList.toggle('active');
}

// A switcher key resolved to a Polylang slug.
//
// The options name a language now, because currency codes cannot: Finnish and
// German are both `eur`, so keying the switcher on currency made them the same
// option. A currency code still resolves, for anything still passing one.
function languageSlugFor(key) {
    if (window.nordictvLangSlug) {
        const resolved = window.nordictvLangSlug(key);
        if (resolved) return resolved;
    }

    const cfg = window.nordictvLang;
    if (!cfg) return null;
    if (cfg.byLang && cfg.byLang[key]) return key;
    return (cfg.byCurrency && cfg.byCurrency[key]) || null;
}

// Remember the language the visitor just picked, so the next visit opens in it.
// inc/language-preference.php reads this cookie and prints window.nordictvLang.
function rememberLanguageChoice(key) {
    const cfg = window.nordictvLang;
    if (!cfg) return;

    const slug = languageSlugFor(key);
    if (!slug) return;

    document.cookie = cfg.cookie + '=' + encodeURIComponent(slug) +
        ';path=/;max-age=' + (cfg.days * 24 * 60 * 60) + ';samesite=lax';
}

// Where switching to `currency` should land.
//
// Prefer this page's counterpart in the chosen language — window.nordictvLangUrl
// is printed by inc/language-preference.php from Polylang, which knows each
// translation's URL and falls back to that language's front page by itself.
// Switching language from /sv/about-us used to drop you on /no/ rather than
// /no/about-us, because only the language roots below were ever consulted.
function languageTargetUrl(key) {
    const translated = window.nordictvLangUrl && window.nordictvLangUrl(key);
    if (translated) return translated;

    const slug = languageSlugFor(key);
    if (!slug) return null;

    // Polylang hides the default language's directory, so English is the root.
    const path = slug === 'en' ? '/' : '/' + slug + '/';
    return window.location.origin + path;
}

// The preference redirect only runs on a front page, so only guard those — no
// need to hang a query string off every inner page URL. It covers the case
// where the cookie write silently failed and a stale preference would otherwise
// bounce the visitor straight back.
function withLangRedirectGuard(url) {
    let isRoot = false;
    try {
        isRoot = /^\/([a-z]{2}\/)?$/.test(new URL(url, window.location.origin).pathname);
    } catch (e) {
        isRoot = false;
    }
    if (!isRoot) return url;
    return url + (url.indexOf('?') === -1 ? '?' : '&') + 'nolangredirect=1';
}

// Redirect to the chosen language
function redirectToRegion(key) {
    const target = languageTargetUrl(key);
    if (!target) return;

    rememberLanguageChoice(key);
    window.location.href = withLangRedirectGuard(target);
}

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    const selector = document.getElementById('countrySelector');
    const dropdown = document.getElementById('countryDropdown');
    if (selector && !selector.contains(e.target)) {
        if (dropdown) dropdown.classList.remove('active');
    }

    // Footer dropdown
    const footerSelector = document.getElementById('footerCountrySelector');
    const footerDropdown = document.getElementById('footerCountryDropdown');
    if (footerSelector && !footerSelector.contains(e.target)) {
        if (footerDropdown) footerDropdown.classList.remove('active');
    }
});

// Currency data — `symbol`, `code` and `position` format a price. The label the
// switcher shows lives in langData below: a currency cannot name a language,
// because Finnish and German share the euro.
const currencyData = {
    usd: { symbol: '$', code: 'USD', position: 'before' },
    eur: { symbol: '€', code: 'EUR', position: 'before' },
    sek: { symbol: 'kr', code: 'SEK', position: 'after' },
    nok: { symbol: 'kr', code: 'NOK', position: 'after' },
    dkk: { symbol: 'kr', code: 'DKK', position: 'after' },
    isk: { symbol: 'kr', code: 'ISK', position: 'after' }
};

// One entry per Polylang language: the switcher label, and the currency that
// language prices in. Mirrors nordictv_currency_by_lang() in PHP.
const langData = {
    en: { flag: '🇺🇸', name: 'English', currency: 'usd' },
    sv: { flag: '🇸🇪', name: 'Svenska', currency: 'sek' },
    no: { flag: '🇳🇴', name: 'Norsk', currency: 'nok' },
    dk: { flag: '🇩🇰', name: 'Dansk', currency: 'dkk' },
    fi: { flag: '🇫🇮', name: 'Suomi', currency: 'eur' },
    is: { flag: '🇮🇸', name: 'Íslenska', currency: 'isk' },
    de: { flag: '🇩🇪', name: 'Deutsch', currency: 'eur' }
};

// Detect the language being served from the URL. Polylang hides the default
// language's directory, so a path with no language segment is English.
function getCurrentLangFromUrl() {
    const first = window.location.pathname.split('/')[1];
    return langData[first] ? first : 'en';
}

// Kept because the pricing code asks in currency, not in language.
function getCurrentCurrencyFromUrl() {
    return langData[getCurrentLangFromUrl()].currency;
}

// Update the switcher label and the prices for one language.
function setLanguage(slug) {
    const lang = langData[slug];
    if (!lang) return;

    const headerFlag = document.getElementById('selectedFlag');
    const headerCode = document.getElementById('selectedCode');
    if (headerFlag) headerFlag.textContent = lang.flag;
    if (headerCode) headerCode.textContent = lang.name;

    const footerFlag = document.getElementById('footerSelectedFlag');
    const footerCode = document.getElementById('footerSelectedCode');
    if (footerFlag) footerFlag.textContent = lang.flag;
    if (footerCode) footerCode.textContent = lang.name;

    document.querySelectorAll('.country-option').forEach(opt => {
        opt.classList.remove('selected');
        if ((opt.dataset.lang || opt.dataset.currency) === slug) {
            opt.classList.add('selected');
        }
    });

    setCurrency(lang.currency);
}

// Update prices for a currency. The switcher label is set by setLanguage().
function setCurrency(currency) {
    const data = currencyData[currency];
    if (!data) return;

    window.currentCurrency = currency;
    updateAllPrices();
    localStorage.setItem('iptv_currency', currency);

    const headerDropdown = document.getElementById('countryDropdown');
    const footerDropdown = document.getElementById('footerCountryDropdown');
    if (headerDropdown) headerDropdown.classList.remove('active');
    if (footerDropdown) footerDropdown.classList.remove('active');
}

// Footer switcher (syncs with header). Takes a language slug; a currency code
// still resolves, for any markup not yet carrying data-lang.
function setFooterCurrency(key) {
    const slug = languageSlugFor(key);
    if (!slug) return;

    // Same as the header switcher: this is a deliberate choice, so record it.
    rememberLanguageChoice(slug);

    if (slug !== getCurrentLangFromUrl()) {
        const target = languageTargetUrl(slug);
        if (target) {
            window.location.href = withLangRedirectGuard(target);
            return;
        }
    }
    setLanguage(slug);
}

// Update all prices based on selected device count and currency
function updateAllPrices() {
    if (!window.iptvPrices) return;

    const currency = window.currentCurrency || 'usd';
    const data = currencyData[currency];

    // pricing.js marks the chosen card with .active; .selected kept for safety.
    const selectedDevice = document.querySelector('.select-card.active[data-devices], .select-card.selected[data-devices]');
    let deviceKey = '1_device';
    if (selectedDevice) {
        const deviceNum = parseInt(selectedDevice.dataset.devices);
        deviceKey = deviceNum === 1 ? '1_device' : deviceNum + '_devices';
    }

    const durationMap = { '1': '1_month', '3': '3_months', '6': '6_months', '12': '12_months' };

    document.querySelectorAll('.duration-card').forEach(card => {
        const duration = card.dataset.duration;
        const durationKey = durationMap[duration];
        const priceEl = card.querySelector('.duration-price');

        if (priceEl && durationKey && window.iptvPrices[durationKey] && window.iptvPrices[durationKey][deviceKey]) {
            const price = window.iptvPrices[durationKey][deviceKey][currency];
            if (price) {
                priceEl.textContent = data.position === 'before'
                    ? data.symbol + price
                    : price + ' ' + data.symbol;
            }
        }
    });

    // Update Comparison Table "Annual Price"
    const compPriceEl = document.getElementById('comp-annual-price');
    if (compPriceEl && window.iptvPrices && window.iptvPrices['12_months'] && window.iptvPrices['12_months']['1_device']) {
        const price = window.iptvPrices['12_months']['1_device'][currency];
        if (price) {
            compPriceEl.textContent = data.position === 'before'
                ? data.symbol + price
                : price + ' ' + data.symbol;
        }
    }

    // Let the configurator re-render per-month lines, savings badges and CTA.
    if (typeof window.iptvRefreshPricing === 'function') {
        window.iptvRefreshPricing();
    }
}

// There is deliberately no browser-language auto-redirect here any more. A
// Swedish-configured browser asking for the English page gets the English page;
// only a language the visitor picked themselves ever moves them, and that is
// handled server-side in inc/language-preference.php.

// Initialize currency on page load
document.addEventListener('DOMContentLoaded', function () {

    // Set up country option click handlers with redirect
    document.querySelectorAll('.country-option').forEach(option => {
        option.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default link behavior to ensure storage save

            // data-lang names the language; data-currency is the price
            // currency and is only a fallback for markup without data-lang.
            const slug = languageSlugFor(this.dataset.lang || this.dataset.currency);
            if (!slug) return;

            // This is the visitor choosing — remember it for next time.
            rememberLanguageChoice(slug);

            const target = languageTargetUrl(slug);

            if (slug !== getCurrentLangFromUrl() && target) {
                window.location.href = withLangRedirectGuard(target);
            } else {
                setLanguage(slug);
            }
        });
    });

    // Label and prices follow the language this URL is serving.
    setLanguage(getCurrentLangFromUrl());
});

function htmlCurrentCurrency() {
    return getCurrentCurrencyFromUrl();
}
