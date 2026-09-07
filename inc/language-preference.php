<?php
/**
 * Language preference
 *
 * Replaces inc/geo-redirect.php, which picked a visitor's language for them from
 * their IP (WooCommerce geolocation) and their Accept-Language header. Language
 * is now the visitor's own choice and nothing else.
 *
 * The rules:
 *   - Whatever URL you ask for is the language you get. Landing on English keeps
 *     you on English, however your browser is configured and wherever you are.
 *   - Choosing a language in the switcher stores it in the `nordictv_lang`
 *     cookie for a year.
 *   - On a later visit, arriving at the *front page* in a language other than
 *     the stored one sends you to the stored one's front page. That is the only
 *     redirect left in the theme.
 *   - Deep links are never redirected, so a shared or bookmarked URL always
 *     opens the page it names.
 *   - Bots are never redirected, so every language stays crawlable under its own
 *     URL and hreflang keeps working.
 *
 * The cookie is deliberately not HttpOnly: the language switcher writes it from
 * JavaScript (see front-page/js/currency.js) as well as through ?set_lang=,
 * which is the no-JavaScript path.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('NORDICTV_LANG_COOKIE')) {
    define('NORDICTV_LANG_COOKIE', 'nordictv_lang');
}

if (!defined('NORDICTV_LANG_COOKIE_DAYS')) {
    define('NORDICTV_LANG_COOKIE_DAYS', 365);
}

/**
 * Active language slugs, straight from Polylang.
 *
 * @return string[]
 */
function nordictv_lang_slugs()
{
    static $slugs = null;

    if ($slugs !== null) {
        return $slugs;
    }

    $slugs = array();

    if (function_exists('pll_languages_list')) {
        $list = pll_languages_list(array('fields' => 'slug'));
        if (is_array($list)) {
            $slugs = $list;
        }
    }

    return $slugs;
}

/**
 * The currency each language prices in.
 *
 * This is the authoritative pairing, and it is keyed by language rather than by
 * currency because the relationship is many-to-one: Finnish and German both
 * price in euros. Keying it the other way — as nordictv_lang_by_currency() did
 * on its own — cannot express that, and array_flip()ing it silently dropped
 * whichever of the two came first.
 *
 * @return array<string,string> language slug => currency code
 */
function nordictv_currency_by_lang()
{
    return apply_filters('nordictv_currency_by_lang', array(
        'en' => 'usd',
        'sv' => 'sek',
        'no' => 'nok',
        'dk' => 'dkk',
        'fi' => 'eur',
        'is' => 'isk',
        'de' => 'eur',
    ));
}

/**
 * Which language each currency in the switcher corresponds to.
 *
 * The switcher used to be built around currencies and the redirect around
 * Polylang slugs, so the two needed reconciling somewhere. The switcher now
 * names languages directly (see nordictv_lang_for_key()), and this survives as
 * the fallback for a currency key: it answers "if all I know is `eur`, which
 * language did the visitor mean?" with the language that had it first.
 *
 * Entries whose language is not active in Polylang are dropped, so disabling a
 * language in wp-admin is enough to take it out of circulation.
 *
 * @return array<string,string> currency code => language slug
 */
function nordictv_lang_by_currency()
{
    $map = array();

    // First language claiming a currency keeps it, so German does not take the
    // euro away from Finnish for visitors arriving with only a currency code.
    foreach (nordictv_currency_by_lang() as $slug => $currency) {
        if (!isset($map[$currency])) {
            $map[$currency] = $slug;
        }
    }

    $map = apply_filters('nordictv_lang_by_currency', $map);

    $active = nordictv_lang_slugs();
    if (empty($active)) {
        return $map;
    }

    return array_filter($map, function ($slug) use ($active) {
        return in_array($slug, $active, true);
    });
}

/**
 * Resolve a switcher key to a language slug.
 *
 * The switcher's options carry a language slug now, but the currency codes they
 * used to carry are still in circulation — in the footer switcher, in anything
 * cached, and in a bookmarked ?set_lang. Both are accepted: a key that names an
 * active language is that language, anything else is looked up as a currency.
 *
 * @param string $key Language slug or currency code.
 * @return string Language slug, or '' if the key names neither.
 */
function nordictv_lang_for_key($key)
{
    $key    = sanitize_key($key);
    $active = nordictv_lang_slugs();

    if ($key && (empty($active) || in_array($key, $active, true))) {
        if (isset(nordictv_currency_by_lang()[$key])) {
            return $key;
        }
    }

    $by_currency = nordictv_lang_by_currency();

    return isset($by_currency[$key]) ? $by_currency[$key] : '';
}

/**
 * Store a language preference, if it names a language this site actually has.
 *
 * @param string $slug Polylang language slug.
 * @return bool Whether the preference was accepted.
 */
function nordictv_remember_language($slug)
{
    $slug   = sanitize_key($slug);
    $active = nordictv_lang_slugs();

    if (!$slug || (!empty($active) && !in_array($slug, $active, true))) {
        return false;
    }

    // Reflected into $_COOKIE so the redirect below sees it on this same
    // request, not only on the next one.
    $_COOKIE[NORDICTV_LANG_COOKIE] = $slug;

    if (!headers_sent()) {
        setcookie(
            NORDICTV_LANG_COOKIE,
            $slug,
            time() + (NORDICTV_LANG_COOKIE_DAYS * DAY_IN_SECONDS),
            COOKIEPATH ? COOKIEPATH : '/',
            COOKIE_DOMAIN,
            is_ssl(),
            false // readable by the switcher's JavaScript
        );
    }

    return true;
}

/**
 * The stored preference, or '' if there is none worth acting on.
 *
 * @return string
 */
function nordictv_stored_language()
{
    if (empty($_COOKIE[NORDICTV_LANG_COOKIE])) {
        return '';
    }

    $slug   = sanitize_key(wp_unslash($_COOKIE[NORDICTV_LANG_COOKIE]));
    $active = nordictv_lang_slugs();

    if (!empty($active) && !in_array($slug, $active, true)) {
        return '';
    }

    return $slug;
}

/**
 * ?set_lang=sv — the no-JavaScript way to record a choice.
 *
 * Resolved through nordictv_lang_for_key() so a currency code works here too:
 * the switcher named currencies until German arrived, and a bookmarked or
 * shared ?set_lang=eur should keep doing what it did.
 */
add_action('init', function () {
    if (empty($_GET['set_lang'])) {
        return;
    }

    nordictv_remember_language(nordictv_lang_for_key(wp_unslash($_GET['set_lang'])));
});

/**
 * Send a returning visitor to the language they picked last time.
 *
 * Front page only, and only when the stored language differs from the one being
 * viewed.
 */
add_action('template_redirect', function () {
    if (is_admin() || wp_doing_ajax() || is_feed() || is_embed()) {
        return;
    }

    // Only the front page. A deep link keeps whatever language it names.
    if (!is_front_page()) {
        return;
    }

    // Let editors see exactly the URL they asked for.
    if (is_user_logged_in() && current_user_can('manage_options')) {
        return;
    }

    // Never bounce a crawler — each language has to stay reachable at its own
    // URL for hreflang to mean anything.
    if (nordictv_is_bot()) {
        return;
    }

    // Escape hatch, and what the switcher appends when it sends you somewhere
    // on purpose, so the redirect cannot fight the click.
    if (isset($_GET['nolangredirect'])) {
        return;
    }

    $preferred = nordictv_stored_language();
    if (!$preferred) {
        return;
    }

    $current = function_exists('pll_current_language') ? pll_current_language('slug') : '';
    if (!$current || $current === $preferred) {
        return;
    }

    $target = function_exists('pll_home_url') ? pll_home_url($preferred) : '';
    if (!$target) {
        return;
    }

    wp_safe_redirect($target, 302);
    exit;
});

/**
 * Keep the page cache from swallowing the redirect.
 *
 * LiteSpeed serves a cached page before PHP runs, so a cache hit would skip the
 * redirect above entirely. Two measures, in order of preference:
 *
 *   - Vary the front page on the preference cookie, so each language gets its
 *     own cache entry rather than one shared one.
 *   - Additionally bypass the cache when a preference is actually set, so the
 *     redirect is guaranteed to run for the visitors it applies to. Anyone
 *     without a preference — the overwhelming majority, and every first-time
 *     visitor — still gets a fully cached page.
 *
 * The old code disabled caching on the front page for everyone.
 */
add_action('template_redirect', function () {
    if (!is_front_page() || headers_sent()) {
        return;
    }

    header('X-LiteSpeed-Vary: cookie=' . NORDICTV_LANG_COOKIE);

    if (nordictv_stored_language()) {
        header('X-LiteSpeed-Cache-Control: no-cache');
    }
}, 1);

/**
 * Hand the switcher what it needs: the cookie name, the current language, and
 * the currency/language pairs.
 */
add_action('wp_head', function () {
    // Where each language's copy of *this* page lives. Polylang works this out
    // per request and falls back to that language's front page when no
    // translation exists, which is exactly the behaviour the switcher wants.
    //
    // Without this the switcher only knew the language roots, so changing
    // language from /sv/about-us dropped you on /no/ instead of /no/about-us.
    $urls = array();
    if (function_exists('pll_the_languages')) {
        $list = pll_the_languages(array(
            'raw'                    => 1,
            'echo'                   => 0,
            'hide_if_no_translation' => 0,
        ));

        if (is_array($list)) {
            foreach ($list as $entry) {
                if (!empty($entry['slug']) && !empty($entry['url'])) {
                    $urls[$entry['slug']] = $entry['url'];
                }
            }
        }
    }

    $data = array(
        'cookie'     => NORDICTV_LANG_COOKIE,
        'days'       => NORDICTV_LANG_COOKIE_DAYS,
        'current'    => function_exists('pll_current_language') ? pll_current_language('slug') : '',
        'slugs'      => array_values(nordictv_lang_slugs()),
        'byCurrency' => (object) nordictv_lang_by_currency(),
        'byLang'     => (object) nordictv_currency_by_lang(),
        'urls'       => (object) $urls,
    );

    echo '<script>window.nordictvLang=' . wp_json_encode($data) . ';';

    // Shared by both copies of the switcher — front-page/js/currency.js and the
    // inline one in inc/universal-header.php — so they cannot drift apart again.
    //
    // The argument is a language slug now; a currency code still resolves,
    // through the same fallback nordictv_lang_for_key() applies in PHP.
    echo 'window.nordictvLangSlug=function(k){'
        . 'var g=window.nordictvLang;if(!g)return null;'
        . 'if(g.byLang&&g.byLang[k])return k;'
        . 'return (g.byCurrency&&g.byCurrency[k])||null;};';

    echo 'window.nordictvLangUrl=function(k){'
        . 'var g=window.nordictvLang;if(!g)return null;'
        . 'var s=window.nordictvLangSlug(k);if(!s)return null;'
        . 'return (g.urls&&g.urls[s])||null;};';

    // Take the guard back out of the address bar.
    //
    // The switcher appends ?nolangredirect=1 when it sends you to a language
    // root, so the preference redirect above cannot fight the click. By the
    // time this runs that request is served and the parameter has done its
    // whole job — leaving it visible only gives the visitor an odd URL to
    // bookmark or share, and gives the page cache a second key for a page it
    // already holds.
    //
    // It does mean a manual reload is no longer guarded. That is the correct
    // trade: the guard was only ever good for the one navigation it was
    // attached to, since any *other* route back to the front page carries no
    // parameter either. If the cookie write really did fail, the visitor gets
    // bounced on their next front-page visit regardless of this line.
    echo 'try{var u=new URL(window.location.href);'
        . 'if(u.searchParams.has("nolangredirect")){'
        . 'u.searchParams.delete("nolangredirect");'
        . 'var q=u.searchParams.toString();'
        . 'window.history.replaceState(null,"",u.pathname+(q?"?"+q:"")+u.hash);'
        . '}}catch(e){}';

    echo '</script>' . "\n";
}, 5);

/**
 * Is this request a crawler?
 *
 * Carried over from inc/geo-redirect.php, which is where the list was built.
 *
 * @return bool
 */
function nordictv_is_bot()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $user_agent = strtolower(wp_unslash($_SERVER['HTTP_USER_AGENT']));

    $bots = array(
        'googlebot',
        'bingbot',
        'slurp',
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'sogou',
        'exabot',
        'facebot',
        'facebookexternalhit',
        'ia_archiver',
        'mj12bot',
        'ahrefsbot',
        'semrushbot',
        'dotbot',
        'rogerbot',
        'screaming frog',
        'gtmetrix',
        'pingdom',
        'uptimerobot',
        'petalbot',
        'applebot',
        'twitterbot',
        'linkedinbot',
        'slackbot',
        'telegrambot',
        'whatsapp',
        'discordbot',
        'bot',
        'crawler',
        'spider',
    );

    foreach ($bots as $bot) {
        if (strpos($user_agent, $bot) !== false) {
            return true;
        }
    }

    return false;
}
