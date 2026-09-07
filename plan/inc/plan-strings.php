<?php
/**
 * Plan template — Polylang string registration
 *
 * The theme ships no .mo files and never calls load_theme_textdomain(), so a
 * __() in a template renders English in every language. Every string a plan
 * page can print is therefore registered with Polylang instead, exactly as
 * sport/inc/sport-strings.php does, and shows up under
 * Languages → String translations (where AutoPoly can also reach it).
 *
 * Usage in templates: plan_str('Default English text')
 *
 * The audience and FAQ defaults are held here as data rather than in the
 * sections that print them, so the registration loop and the templates read the
 * same array. Copy edited in one place cannot fall out of registration.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('iptv_plan_translations')) {
    /**
     * Bundled Nordic copy for this template, english => translation.
     *
     * Ships with the theme so the plan pages read correctly in every language
     * the moment they are published, rather than after someone remembers to
     * work through Languages → String translations. A translation entered
     * there still wins — see plan_str() — so this is a floor, not a ceiling.
     *
     * @param string $lang Polylang language slug.
     * @return array<string,string>
     */
    function iptv_plan_translations($lang)
    {
        static $cache = array();

        if (isset($cache[$lang])) {
            return $cache[$lang];
        }

        $file = __DIR__ . '/translations/' . $lang . '.php';
        $cache[$lang] = file_exists($file) ? (array) include $file : array();

        return $cache[$lang];
    }
}

if (!function_exists('plan_str')) {
    /**
     * The current language's version of a plan string.
     *
     * Order: a translation entered in Polylang's string table, then the copy
     * bundled with the theme, then the English default. Polylang comes first so
     * an editor can always override what the theme ships — pll__() returns the
     * string unchanged when nothing has been entered, which is what makes the
     * comparison below a usable "was it translated?" test.
     *
     * @param string $default English default, which is also the lookup key.
     * @return string
     */
    function plan_str($default)
    {
        if (function_exists('pll__')) {
            $translated = pll__($default);
            if ($translated !== $default) {
                return $translated;
            }
        }

        $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';

        if ($lang && $lang !== 'en') {
            $bundled = iptv_plan_translations($lang);
            if (isset($bundled[$default]) && $bundled[$default] !== '') {
                return $bundled[$default];
            }
        }

        return $default;
    }
}

if (!function_exists('iptv_plan_audience_defaults')) {
    /**
     * "Who this plan suits" — three cards per length.
     *
     * The only genuinely per-length copy on the site, and the reason the four
     * plans are separate pages: a 1-month page argues "try it without
     * committing", a 12-month page argues "you already know you want it".
     * Overridden per page by the plan_audience_points ACF repeater.
     *
     * @return array<int,array<int,array{title:string,text:string}>>
     */
    function iptv_plan_audience_defaults()
    {
        return array(
            1 => array(
                array(
                    'title' => 'You want to try it properly',
                    'text'  => 'A full month of the complete service — every channel, every film, every match. Long enough to judge it on your own TV, your own connection and your own evenings.',
                ),
                array(
                    'title' => 'You are not ready to commit',
                    'text'  => 'Nothing renews on its own and there is no contract to leave. When the month ends, it ends — you decide whether there is a next one.',
                ),
                array(
                    'title' => 'You only need it for a while',
                    'text'  => 'A season, a tournament, a long winter, a rented flat. Take the month you need and stop.',
                ),
            ),
            3 => array(
                array(
                    'title' => 'You have already made up your mind',
                    'text'  => 'You have tried IPTV before and you know what you want. Three months costs noticeably less per month than paying monthly.',
                ),
                array(
                    'title' => 'You are covering a season',
                    'text'  => 'One league, one winter, one stretch of long evenings — a quarter is usually the shape of it.',
                ),
                array(
                    'title' => 'You want less admin',
                    'text'  => 'One payment instead of three, and no renewal to remember in between.',
                ),
            ),
            6 => array(
                array(
                    'title' => 'You watch all year',
                    'text'  => 'Half a year of everything, at a rate that makes monthly billing look expensive.',
                ),
                array(
                    'title' => 'You want the saving without the full year',
                    'text'  => 'Most of the discount of the annual plan, at half the amount up front.',
                ),
                array(
                    'title' => 'You are done comparing',
                    'text'  => 'Set it up once, forget the billing, and go back to watching television.',
                ),
            ),
            12 => array(
                array(
                    'title' => 'You want the lowest price there is',
                    'text'  => 'The annual plan is the cheapest month of television we sell. Nothing else comes close per month.',
                ),
                array(
                    'title' => 'This is your main television',
                    'text'  => 'If the household watches most nights, a year is the plan that matches how you actually use it.',
                ),
                array(
                    'title' => 'You want to pay once and forget it',
                    'text'  => 'One payment, twelve months, no renewal notice and no auto-charge at the end of it.',
                ),
            ),
        );
    }
}

if (!function_exists('iptv_plan_faq_defaults')) {
    /**
     * Default FAQ rows. Overridden per page by the plan_faq ACF repeater.
     *
     * The first entry is 1-month only: the upsell question ("can I switch to a
     * longer plan?") makes no sense on the annual page.
     *
     * @param int $months
     * @return array<int,array{q:string,a:string}>
     */
    function iptv_plan_faq_defaults($months)
    {
        $items = array();

        if ((int) $months === 1) {
            $items[] = array(
                'q' => 'Can I switch to a longer plan later?',
                'a' => 'Yes. Plenty of people start with one month and move to 6 or 12 once they have seen the service. Nothing is locked, and the longer plans cost far less per month.',
            );
        }

        return array_merge($items, array(
            array(
                'q' => 'What do I get with the %s plan?',
                'a' => 'Everything we offer: 40,000+ live channels, 200,000+ movies and series, 4K/HD quality, the full TV guide and 24/7 support. The only thing a plan changes is how long it runs and how many screens can watch at once.',
            ),
            array(
                'q' => 'How fast is my subscription activated?',
                'a' => 'Straight after payment. Your login details are emailed within about 60 seconds, and you can be watching before the email notification fades.',
            ),
            array(
                'q' => 'Does it renew automatically?',
                'a' => 'No. There is no auto-renew and no contract — the plan simply ends, and you renew only if you want to.',
            ),
            array(
                'q' => 'How many screens do I need?',
                'a' => 'One screen streams on one device at a time. Pick the number of people who might watch different things at the same time — most households choose two.',
            ),
            array(
                'q' => 'Which devices work?',
                'a' => 'Smart TVs, Android TV, Apple TV, Fire Stick, iPhone, iPad, Android, Windows, Mac, set-top boxes, Chromecast, Roku and Kodi. No new hardware needed.',
            ),
            array(
                'q' => 'What if it does not work for me?',
                'a' => 'You are covered by our money-back guarantee, and there is a 24-hour trial with no card if you would rather test first.',
            ),
        ));
    }
}

/**
 * Register everything a plan page can print.
 *
 * Strings reaching iptv_text() are registered too: that helper ends in
 * pll__($default) when the front page has no field of that name, which is the
 * case for every plan-only key.
 */
add_action('init', function () {
    if (!function_exists('pll_register_string')) {
        return;
    }

    $group = 'Plan Template';

    $register = function ($name, $string, $multiline = false) use ($group) {
        pll_register_string($name, $string, $group, $multiline);
    };

    // ── Hero ─────────────────────────────────────────────────────────────────
    $register('plan_eyebrow', 'IPTV Subscription');
    $register('plan_headline_format', '%s IPTV Subscription');
    $register('plan_subline_1mo', 'The whole service, one month at a time. No contract, no auto-renew — stop whenever you like.', true);
    $register('plan_subline_multi', 'The whole service for %s. One payment, no contract, no auto-renew.', true);
    $register('plan_from_label', 'From');
    $register('plan_price_for', 'for %s');
    $register('plan_cta_text', 'See prices');
    $register('plan_hero_point_1', 'Watching in 60 seconds');
    $register('plan_hero_point_2', 'No contract, no auto-renew');
    $register('plan_hero_point_3', '24/7 support');
    $register('plan_hero_image_placeholder', 'Hero image');

    // ── Price grid ───────────────────────────────────────────────────────────
    $register('plan_pricing_title', '%s — choose your screens');
    $register('plan_pricing_subtitle', 'One screen streams on one device at a time. Everything else is identical on every plan.', true);
    $register('plan_screens_note_one', 'One device watching at a time');
    $register('plan_screens_note_many', '%d devices watching at the same time');
    $register('plan_per_month_format', '%s / month');
    $register('plan_save_percent', 'Save %d%%');

    // ── Audience ─────────────────────────────────────────────────────────────
    $register('plan_audience_title', 'Who the %s plan suits');

    foreach (iptv_plan_audience_defaults() as $months => $cards) {
        foreach ($cards as $i => $card) {
            $n = $i + 1;
            $register("plan_audience_{$months}mo_{$n}_title", $card['title']);
            $register("plan_audience_{$months}mo_{$n}_text", $card['text'], true);
        }
    }

    // ── Compare table ────────────────────────────────────────────────────────
    $register('plan_compare_title', 'How the four plans compare');
    $register('plan_compare_subtitle', 'Prices shown for %s. Longer plans cost less per month — the service is the same on all of them.', true);
    $register('plan_compare_col_plan', 'Plan');
    $register('plan_compare_col_rate', 'Per month');
    $register('plan_compare_col_save', 'You save');
    $register('plan_compare_col_link', 'Link');
    $register('plan_compare_best', 'Best value');
    $register('plan_compare_here', 'You are here');
    $register('plan_compare_see', 'See %s');
    $register('plan_compare_see_pricing', 'See pricing');

    // ── FAQ ──────────────────────────────────────────────────────────────────
    // Registered from the 1-month set, which is a superset of the others.
    foreach (iptv_plan_faq_defaults(1) as $i => $row) {
        $n = $i + 1;
        $register("plan_faq_{$n}_q", $row['q']);
        $register("plan_faq_{$n}_a", $row['a'], true);
    }

    // ── Closing band ─────────────────────────────────────────────────────────
    $register('plan_final_title', 'Start your %s plan today');
    $register('plan_final_text', 'From %s. Activated in about a minute, watchable on the TV you already own.', true);
    $register('plan_final_text_nofrom', 'Activated in about a minute, watchable on the TV you already own.', true);

    // ── Schema ───────────────────────────────────────────────────────────────
    $register('plan_schema_description', '%s NordicTV IPTV subscription: 40,000+ live channels, 200,000+ movies and series in 4K and HD, on 1 to 4 screens. No contract and no auto-renew.', true);
});
