<?php
/**
 * Legacy URL redirects
 *
 * Search Console reported 330 "Not found (404)" URLs. None of them are typos:
 * they are the URL space of the site that stood here before this theme, still
 * being crawled and still earning clicks. Four things produced them.
 *
 *   1. Languages that no longer exist — /de/, /es/, /fr/, /pt/, /nl/, and /da/,
 *      which was Danish before it became /dk/. /de alone still takes 32 clicks
 *      a quarter into a 404.
 *   2. WooCommerce, now uninstalled, taking /product/, /produkt/ and
 *      /product-category/ with it. The four plan pages replaced those products.
 *   3. A /setup-guides/ section whose posts moved to the blog under the same
 *      slugs. /sv/setup-guides is the highest-impression URL on the site after
 *      the home page, and it 404s.
 *   4. A /sports/ archive, retired alongside the sport post type.
 *
 * Everything here runs *after* WordPress has already decided the request is a
 * 404 — see the is_404() guard in nordictv_legacy_redirect(). That is the whole
 * safety property of this file: it is structurally incapable of shadowing a URL
 * that works, so publishing a page can never be undone by a rule written here,
 * and no rule needs revisiting when content moves.
 *
 * A rule only fires when the path is recognisably one of the shapes above. An
 * ordinary typo under a live language still 404s, which is what it should do.
 *
 * Known-broken destination, deliberately avoided: the Swedish blog index.
 * Polylang Pro translates the `blog` permalink base to `blogg` for Swedish and
 * canonicalises /sv/blog to /sv/blogg (x-redirect-by: Polylang Pro), but the
 * page still carries the slug `blog`, so nothing answers at /sv/blogg. Until
 * page 421's slug is changed to `blogg`, no rule here may resolve through it.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retired language prefixes, and the live language each one should land in.
 *
 * /da/ is the old Danish prefix and has a real destination. The other five are
 * languages the site dropped entirely, so English is the only honest target.
 *
 * @return array<string,string> retired prefix => live language slug
 */
function nordictv_retired_lang_prefixes()
{
    return apply_filters('nordictv_retired_lang_prefixes', array(
        'da' => 'dk',
        'de' => 'en',
        'es' => 'en',
        'fr' => 'en',
        'nl' => 'en',
        'pt' => 'en',
    ));
}

/**
 * Home page of a language.
 *
 * pll_home_url(), never home_url() — the latter ignores the language and would
 * drop every non-English visitor onto the English front page.
 *
 * @param string $lang Language slug.
 * @return string
 */
function nordictv_lang_home($lang)
{
    if (function_exists('pll_home_url')) {
        $url = pll_home_url($lang);
        if ($url) {
            return $url;
        }
    }

    return home_url('/');
}

/**
 * Pricing section of a language's home page.
 *
 * Used where the legacy URL was about buying but names nothing we can resolve
 * to a specific plan — a /product archive, a checkout, a retired free-trial
 * product. The fragment is ignored by Google but puts a human in front of the
 * prices instead of the top of the page.
 *
 * @param string $lang Language slug.
 * @return string
 */
function nordictv_pricing_url($lang)
{
    return trailingslashit(nordictv_lang_home($lang)) . '#pricing';
}

/**
 * Subscription length encoded in a legacy slug, in any of the eight languages
 * the old store sold in.
 *
 * The old product slugs are the only record of which plan they were, so the
 * length has to be read back out of them:
 *
 *   iptv-abonnement-6-maaneder-nordic-tv      → 6
 *   iptv-tilaus-3-kuukautta-nordic-tv         → 3
 *   iptv-askrift-12-manadur-nordic-tv         → 12
 *   iptv-norden-6-manadersplan                → 6
 *   meilleur-abonnement-iptv-12-mois-nordictv → 12
 *   nordic-stream-svensk-iptv-12-man          → 12
 *
 * Only 1, 3, 6 and 12 are accepted, which is also what keeps the free-trial
 * slugs out: gratis-iptv-prove-24-timer-nordic-tv carries a 24, and 24 is not
 * a plan length. Longest unit first, so "manadersplan" is not eaten by "man".
 *
 * @param string $slug
 * @return int 1, 3, 6, 12, or 0 when the slug names no plan.
 */
function nordictv_months_from_slug($slug)
{
    $units = 'manadersplan|manader|manadur|manudur|manudir|maaneder|maanden'
           . '|maneder|maaned|manad|maned|maand|months|month|monate|monat'
           . '|kuukautta|kuukausi|meses|mois|mes|man';

    if (preg_match('/(?:^|-)(1|3|6|12)-(?:' . $units . ')(?:-|$)/', $slug, $m)) {
        return (int) $m[1];
    }

    return 0;
}

/**
 * Permalink of a post looked up by slug, in a given language.
 *
 * The /setup-guides/ posts kept their slugs when they moved to the blog, so the
 * old URL names its own replacement exactly. The same lookup resolves the FAQ
 * that a pruned duplicate was a copy of.
 *
 * @param string $slug
 * @param string $lang      Language slug.
 * @param string $post_type
 * @return string Permalink, or '' when nothing matches.
 */
function nordictv_post_url_by_slug($slug, $lang, $post_type = 'post')
{
    $args = array(
        'post_type'              => $post_type,
        'name'                   => $slug,
        'post_status'            => 'publish',
        'posts_per_page'         => 1,
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
    );

    // 'lang' => '' reads as the current language, not as all of them. Same trap
    // iptv_page_url() documents; same workaround.
    if (function_exists('nordictv_lang_slugs')) {
        $languages = nordictv_lang_slugs();
        if (!empty($languages)) {
            $args['lang'] = implode(',', $languages);
        }
    }

    $query = new WP_Query($args);

    if (empty($query->posts)) {
        return '';
    }

    $id = $query->posts[0]->ID;

    // Strict: without this, a guide that was never translated would 301 a
    // Norwegian URL onto the English post, contradicting hreflang. The caller
    // falls back to that language's blog index instead.
    if (function_exists('pll_get_post')) {
        $translated = pll_get_post($id, $lang);
        if (!$translated) {
            return '';
        }
        $id = $translated;
    }

    return (string) get_permalink($id);
}

/**
 * FAQ duplicates that were pruned, and the question each was a copy of.
 *
 * The FAQ post type had grown seven sets of the same question — "Kan IPTV
 * spåras?" existed four times, word for word, under kan-iptv-sparas,
 * -2, -3 and -4. Duplicates split whatever authority the question earns and
 * gave Google four near-identical pages to choose between, which is a large
 * part of why none of them were being crawled.
 *
 * The copies are in the trash; these rules point their URLs at the survivor.
 * The survivor is the clean slug except for kan-polisen-spara-iptv, where the
 * -2 copy is the one with actual clicks in Search Console and the base had
 * none — traffic outranks a tidy slug.
 *
 * @return array<string,string> retired slug => surviving slug
 */
function nordictv_retired_faq_slugs()
{
    return apply_filters('nordictv_retired_faq_slugs', array(
        'ar-det-olagligt-att-betala-for-iptv-2'      => 'ar-det-olagligt-att-betala-for-iptv',
        'har-xtream-iptv-stangts-ner-2'              => 'har-xtream-iptv-stangts-ner',
        'hur-installerar-jag-iptv-pa-min-smart-tv-2' => 'hur-installerar-jag-iptv-pa-min-smart-tv',
        'kan-iptv-sparas-2'                          => 'kan-iptv-sparas',
        'kan-iptv-sparas-3'                          => 'kan-iptv-sparas',
        'kan-iptv-sparas-4'                          => 'kan-iptv-sparas',
        'kan-polisen-spara-iptv'                     => 'kan-polisen-spara-iptv-2',
        'vad-ar-iptv-och-hur-fungerar-det-2'         => 'vad-ar-iptv-och-hur-fungerar-det',
        'vad-ar-straffet-for-att-ha-iptv-2'          => 'vad-ar-straffet-for-att-ha-iptv',
    ));
}

/**
 * Where a retired URL should go, if anywhere.
 *
 * @param string $path Request path, no host, no query string.
 * @return string Absolute URL, or '' to leave the 404 alone.
 */
function nordictv_legacy_target($path)
{
    $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));

    if (empty($segments)) {
        return '';
    }

    $segments = array_map('sanitize_title', $segments);

    $live    = function_exists('nordictv_lang_slugs') ? nordictv_lang_slugs() : array();
    $retired = nordictv_retired_lang_prefixes();

    // English carries no prefix, so the first segment is a language only when
    // it names one. Anything else is already part of the path.
    $lang    = 'en';
    $matched = false;

    if (in_array($segments[0], $live, true)) {
        $lang = array_shift($segments);
    } elseif (isset($retired[$segments[0]])) {
        $lang = $retired[$segments[0]];
        array_shift($segments);
        // A retired prefix is itself enough to act on: even if nothing below
        // resolves, that language's home beats a dead end.
        $matched = true;
    }

    // The prefix was the whole URL — /de, /da.
    if (empty($segments)) {
        return $matched ? nordictv_lang_home($lang) : '';
    }

    $section = $segments[0];
    $slug    = end($segments);

    switch ($section) {
        // WooCommerce is gone. A nameable product becomes its plan page;
        // anything else — the archive, a category, the free trial — becomes the
        // prices.
        case 'product':
        case 'produkt':
        case 'product-category':
        case 'shop':
            $months = count($segments) > 1 ? nordictv_months_from_slug($slug) : 0;

            if ($months) {
                $plan = iptv_plan_url($months, $lang, true);
                if ($plan) {
                    return $plan;
                }
            }

            return nordictv_pricing_url($lang);

        case 'checkout':
            return nordictv_pricing_url($lang);

        // The guides moved to the blog keeping their slugs, so a named guide
        // resolves exactly. The bare section goes to the User guide page --
        // "iptv-guide-setup-apps-devices-tips", live in all six languages --
        // rather than the blog index: it is the closer topical match for
        // /setup-guides, and it avoids the Swedish blog index, which is
        // currently broken (see the note in the file header).
        case 'setup-guides':
            if (count($segments) > 1) {
                $post = nordictv_post_url_by_slug($slug, $lang);
                if ($post) {
                    return $post;
                }
            }

            return iptv_page_url(
                'iptv-guide-setup-apps-devices-tips',
                nordictv_lang_home($lang),
                $lang,
                true
            );

        // A pruned duplicate question points at the one it duplicated. The
        // archive itself (/faq, /sv/faq) resolves now, so it never lands here.
        case 'faq':
            if (count($segments) > 1) {
                $retired_faqs = nordictv_retired_faq_slugs();

                if (isset($retired_faqs[$slug])) {
                    return nordictv_post_url_by_slug($retired_faqs[$slug], $lang, 'faq');
                }
            }

            return '';

        // Retired with the sport post type. /sport/* is already handled in Rank
        // Math and redirects to the same place; this covers the plural archive
        // Rank Math has no rule for.
        case 'sports':
        case 'sport':
            return nordictv_lang_home($lang);

        // Shopify put every page under /pages/. This site does not.
        case 'pages':
            if (count($segments) > 1) {
                return iptv_page_url($slug, nordictv_lang_home($lang), $lang, true);
            }

            return nordictv_lang_home($lang);
    }

    // Plan pages have been renamed more than once, and the old names are still
    // indexed: /12-months-iptv-subscription, /fi/iptv-tilaus-3-kuukautta,
    // /no/iptv-abonnement-6-maneder. The length in the slug still identifies
    // the page. Safe as a catch-all because this only ever runs on a 404.
    $months = nordictv_months_from_slug($slug);
    if ($months) {
        $plan = iptv_plan_url($months, $lang, true);
        if ($plan) {
            return $plan;
        }
    }

    // Under a retired prefix, try the tail as a page slug — /de/about-us has a
    // live English counterpart — and fall back to that language's home.
    if ($matched) {
        return iptv_page_url($slug, nordictv_lang_home($lang), $lang, true);
    }

    return '';
}

/**
 * Turn a recognised legacy 404 into a 301.
 *
 * Priority 20 puts this after inc/language-preference.php's front-page handler.
 * The two cannot collide — that one only runs on is_front_page(), which is
 * never a 404 — but the ordering states the intent.
 */
function nordictv_legacy_redirect()
{
    if (!is_404() || is_admin() || is_feed() || is_robots() || headers_sent()) {
        return;
    }

    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }

    // Lets a retired URL be inspected as the 404 it really is, which is the
    // only way to check what a rule is standing in front of once it ships.
    if (isset($_GET['nolegacyredirect'])) {
        return;
    }

    $request = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path    = (string) wp_parse_url($request, PHP_URL_PATH);

    if ($path === '') {
        return;
    }

    $target = nordictv_legacy_target($path);

    if (!$target) {
        return;
    }

    // A rule that resolves back to the URL being requested would loop.
    if (untrailingslashit(wp_parse_url($target, PHP_URL_PATH)) === untrailingslashit($path)) {
        return;
    }

    // Neither LiteSpeed nor the browser should hold on to this. LiteSpeed
    // because the rules resolve live pages and a cached copy would outlive a
    // rename; the browser because a 301 is cached indefinitely by default, and
    // a rule that ships wrong would then outlive its own fix for everyone who
    // hit it. The SEO value of a 301 is in the status code, not the cache.
    do_action('litespeed_control_set_nocache', 'nordictv legacy redirect');
    header('X-LiteSpeed-Cache-Control: no-cache');
    header('Cache-Control: no-store, max-age=0');

    wp_redirect($target, 301);
    exit;
}
add_action('template_redirect', 'nordictv_legacy_redirect', 20);
