<?php
/**
 * Plan pages — one-time provisioning
 *
 * Creates the four plan pages in each of the seven languages (28 in total),
 * assigns each its Polylang language and links the seven versions of each plan
 * as translations of one another.
 *
 * Why this lives in the theme rather than being done over the REST API: a
 * Polylang translation group is a term in the `post_translations` taxonomy
 * whose *description* is a serialized lang => post_id map. Writing that by hand
 * over an API is fragile — anything that sanitises the description corrupts the
 * group silently. pll_set_post_language() and pll_save_post_translations() are
 * Polylang's own API for it, and they can only be called from inside WordPress.
 *
 * Idempotent. It matches on template + plan_months + language before creating
 * anything, so re-running repairs rather than duplicates — including adopting
 * the English 1-month page that already existed before this ran. To re-run
 * after editing the table below, bump PLAN_PAGES_BUILD.
 *
 * Pages are created as drafts. Publishing 24 pages to a live site is the site
 * owner's call, not a migration's. Note that the compare table only links plans
 * that are published (iptv_plan_url() queries post_status=publish), so the
 * cross-links light up as you publish them.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

// Bump to re-run after changing the titles or slugs below. Re-running is safe:
// existing pages are matched and reused, so only the language, translation
// group and plan length are rewritten — never the status or the content.
define('PLAN_PAGES_BUILD', 4);

if (!function_exists('iptv_plan_page_definitions')) {
    /**
     * Title and slug for every plan in every language.
     *
     * Slugs are the keyword-bearing ones set for Rank Math, not descriptions
     * of the plan length — that is deliberate: the primary focus keyword has
     * to appear in the URL. They are also distinct across languages even where
     * the titles collide (Norwegian and Danish share a phrase), so WordPress
     * never has to disambiguate one with a -2 suffix.
     *
     * Only applied when a page is created. Existing pages keep whatever slug
     * they have, so editing this table never moves a live URL.
     *
     * @return array<string,array<int,array{title:string,slug:string}>>
     */
    function iptv_plan_page_definitions()
    {
        return array(
            'en' => array(
                1  => array('title' => '1 Month IPTV Subscription', 'slug' => 'iptv-service-provider', 'label' => '1 Month'),
                3  => array('title' => '3 Months IPTV Subscription', 'slug' => 'ip-tv-subscription', 'label' => '3 Months'),
                6  => array('title' => '6 Months IPTV Subscription', 'slug' => 'iptv-service-usa', 'label' => '6 Months'),
                12 => array('title' => '12 Months IPTV Subscription', 'slug' => 'best-iptv-providers-reddit', 'label' => '12 Months'),
            ),
            'sv' => array(
                1  => array('title' => 'IPTV-abonnemang 1 månad', 'slug' => 'iptv-abonnemang', 'label' => '1 månad'),
                3  => array('title' => 'IPTV-abonnemang 3 månader', 'slug' => 'kopa-iptv', 'label' => '3 månader'),
                6  => array('title' => 'IPTV-abonnemang 6 månader', 'slug' => 'basta-iptv-leverantor', 'label' => '6 månader'),
                12 => array('title' => 'IPTV-abonnemang 12 månader', 'slug' => 'basta-iptv-sverige', 'label' => '12 månader'),
            ),
            'no' => array(
                1  => array('title' => 'IPTV-abonnement 1 måned', 'slug' => 'iptv-abonnement', 'label' => '1 måned'),
                3  => array('title' => 'IPTV-abonnement 3 måneder', 'slug' => 'norge-iptv', 'label' => '3 måneder'),
                6  => array('title' => 'IPTV-abonnement 6 måneder', 'slug' => 'iptv-norway', 'label' => '6 måneder'),
                12 => array('title' => 'IPTV-abonnement 12 måneder', 'slug' => 'nordic-iptv', 'label' => '12 måneder'),
            ),
            'dk' => array(
                1  => array('title' => 'IPTV-abonnement 1 måned', 'slug' => 'iptv-abonnement-danmark', 'label' => '1 måned'),
                3  => array('title' => 'IPTV-abonnement 3 måneder', 'slug' => 'bedste-danske-iptv', 'label' => '3 måneder'),
                6  => array('title' => 'IPTV-abonnement 6 måneder', 'slug' => 'kob-iptv', 'label' => '6 måneder'),
                12 => array('title' => 'IPTV-abonnement 12 måneder', 'slug' => 'bedste-iptv-app', 'label' => '12 måneder'),
            ),
            'fi' => array(
                1  => array('title' => 'IPTV-tilaus 1 kuukausi', 'slug' => 'iptv-hinta', 'label' => '1 kuukausi'),
                3  => array('title' => 'IPTV-tilaus 3 kuukautta', 'slug' => 'paras-iptv', 'label' => '3 kuukautta'),
                6  => array('title' => 'IPTV-tilaus 6 kuukautta', 'slug' => 'suomi-iptv-kokemuksia', 'label' => '6 kuukautta'),
                12 => array('title' => 'IPTV-tilaus 12 kuukautta', 'slug' => 'nordic-iptv-suomi', 'label' => '12 kuukautta'),
            ),
            'is' => array(
                1  => array('title' => 'IPTV áskrift 1 mánuður', 'slug' => 'iptv-askrift', 'label' => '1 mánuður'),
                3  => array('title' => 'IPTV áskrift 3 mánuðir', 'slug' => 'iceland-iptv', 'label' => '3 mánuðir'),
                6  => array('title' => 'IPTV áskrift 6 mánuðir', 'slug' => 'smart-iptv', 'label' => '6 mánuðir'),
                12 => array('title' => 'IPTV áskrift 12 mánuðir', 'slug' => 'sjonvarp-simans-askrift', 'label' => '12 mánuðir'),
            ),
            // German slugs carry the plan length as well as the keyword, which
            // is the shape the live URLs settled on in the other languages.
            'de' => array(
                1  => array('title' => 'IPTV Abo 1 Monat', 'slug' => '1-monat-iptv-abo', 'label' => '1 Monat'),
                3  => array('title' => 'IPTV Abo 3 Monate', 'slug' => '3-monate-iptv-deutschland', 'label' => '3 Monate'),
                6  => array('title' => 'IPTV Abo 6 Monate', 'slug' => '6-monate-bestes-iptv', 'label' => '6 Monate'),
                12 => array('title' => 'IPTV Abo 12 Monate', 'slug' => '12-monate-nordic-iptv', 'label' => '12 Monate'),
            ),
        );
    }
}

if (!function_exists('iptv_plan_existing_pages')) {
    /**
     * Every page already using the plan template, indexed by language and
     * length: [lang][months] => post ID.
     *
     * Straight to postmeta rather than through WP_Query: Polylang filters on
     * parse_query, which 'suppress_filters' does not stop, so a normal query
     * would narrow to the current language — and, worse, would drop the pages
     * that have no language assigned yet, which are exactly the ones this has
     * to find and adopt.
     *
     * @return array<string,array<int,int>>
     */
    function iptv_plan_existing_pages()
    {
        global $wpdb;

        $found = array();

        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta}
             WHERE meta_key = '_wp_page_template' AND meta_value = %s",
            'template-plan.php'
        ));

        foreach ($ids as $id) {
            $post = get_post((int) $id);

            if (!$post || $post->post_type !== 'page' || $post->post_status === 'trash') {
                continue;
            }

            $months = (int) get_post_meta($post->ID, 'plan_months', true);
            $lang   = function_exists('pll_get_post_language')
                ? pll_get_post_language($post->ID, 'slug')
                : '';

            // A page with no language yet is parked under '' and claimed by
            // its definition below — that is the English 1-month page.
            $found[$lang ? $lang : ''][$months] = $post->ID;
        }

        return $found;
    }
}

if (!function_exists('iptv_plan_fill_acf')) {
    /**
     * Write this page's copy into its own ACF fields, in its own language.
     *
     * The template already renders correctly from the bundled translations, so
     * this is about editability: with the fields populated, the copy for one
     * plan in one language is editable in the page editor by someone who does
     * not touch code, instead of living only in plan/inc/translations/.
     *
     * Only ever fills an *empty* field. A re-run after someone has edited a
     * page in wp-admin must not overwrite their wording — that is the whole
     * point of putting it in ACF.
     *
     * Two things are deliberately not written:
     *   - the closing text's "From {price}" variant. Baking a price into a
     *     text field would leave it stale the next time prices move; the
     *     no-price wording is used instead.
     *   - anything on the English pages, whose defaults are already English.
     *
     * @param int    $post_id
     * @param string $lang    Polylang slug.
     * @param int    $months
     * @param string $label   The plan length in this language, e.g. "6 månader".
     * @return int Number of fields written.
     */
    function iptv_plan_fill_acf($post_id, $lang, $months, $label)
    {
        if ($lang === 'en' || !function_exists('update_field')) {
            return 0;
        }

        $t = iptv_plan_translations($lang);

        if (empty($t)) {
            return 0;
        }

        // Translation for an English default, or nothing if it is missing —
        // writing the English into a Swedish page's field would be worse than
        // leaving it empty, because an empty field still falls through to the
        // bundled copy at render time.
        $tr = function ($english) use ($t) {
            return isset($t[$english]) && $t[$english] !== '' ? $t[$english] : '';
        };

        $values = array();

        $values['plan_eyebrow']  = $tr('IPTV Subscription');
        $values['plan_cta_text'] = $tr('See prices');

        $subline = $months === 1
            ? $tr('The whole service, one month at a time. No contract, no auto-renew — stop whenever you like.')
            : $tr('The whole service for %s. One payment, no contract, no auto-renew.');
        if ($subline) {
            $values['plan_subline'] = $months === 1 ? $subline : sprintf($subline, $label);
        }

        $points = array();
        foreach (array('Watching in 60 seconds', 'No contract, no auto-renew', '24/7 support') as $p) {
            if ($tr($p)) {
                $points[] = array('text' => $tr($p));
            }
        }
        if ($points) {
            $values['plan_hero_points'] = $points;
        }

        if ($tr('%s — choose your screens')) {
            $values['plan_pricing_title'] = sprintf($tr('%s — choose your screens'), $label);
        }
        $values['plan_pricing_subtitle'] = $tr('One screen streams on one device at a time. Everything else is identical on every plan.');

        if ($tr('Who the %s plan suits')) {
            $values['plan_audience_title'] = sprintf($tr('Who the %s plan suits'), $label);
        }

        $audience = iptv_plan_audience_defaults();
        $cards    = isset($audience[$months]) ? $audience[$months] : array();
        $rows     = array();
        foreach ($cards as $card) {
            if ($tr($card['title'])) {
                $rows[] = array(
                    'title' => $tr($card['title']),
                    'text'  => $tr($card['text']),
                );
            }
        }
        if ($rows) {
            $values['plan_audience_points'] = $rows;
        }

        $values['plan_compare_title'] = $tr('How the four plans compare');

        // plan_compare_subtitle is deliberately left empty. It interpolates the
        // screen-count label, which comes from iptv_text() — the *current*
        // request's language, not this page's. Filling it from here would bake
        // "2 Screens" into the Swedish page. Left empty, the section renders it
        // live in the right language, from the same translated string.

        $faq = array();
        foreach (iptv_plan_faq_defaults($months) as $row) {
            $q = $tr($row['q']);
            $a = $tr($row['a']);
            if ($q && $a) {
                $faq[] = array(
                    // Only the "what do I get" row carries a placeholder.
                    'question' => sprintf($q, $label),
                    'answer'   => $a,
                );
            }
        }
        if ($faq) {
            $values['plan_faq'] = $faq;
        }

        if ($tr('Start your %s plan today')) {
            $values['plan_final_title'] = sprintf($tr('Start your %s plan today'), $label);
        }
        $values['plan_final_text'] = $tr('Activated in about a minute, watchable on the TV you already own.');

        $written = 0;

        foreach ($values as $name => $value) {
            if ($value === '' || $value === array()) {
                continue;
            }

            $existing = get_field($name, $post_id);
            if ($existing !== null && $existing !== '' && $existing !== false && $existing !== array()) {
                continue; // edited in wp-admin — leave it alone
            }

            update_field($name, $value, $post_id);
            $written++;
        }

        return $written;
    }
}

if (!function_exists('iptv_plan_build_pages')) {
    /**
     * Create anything missing, then wire the translation groups.
     *
     * @return array Summary, for the admin notice.
     */
    function iptv_plan_build_pages()
    {
        $definitions = iptv_plan_page_definitions();
        $existing    = iptv_plan_existing_pages();
        $summary     = array('created' => 0, 'reused' => 0, 'linked' => 0, 'fields' => 0);

        // months => [lang => post_id], built as we go and handed to Polylang.
        $groups = array();

        foreach ($definitions as $lang => $plans) {
            foreach ($plans as $months => $def) {

                $post_id = isset($existing[$lang][$months]) ? $existing[$lang][$months] : 0;

                // Adopt a page that has the right template and length but no
                // language assigned — that is the English 1-month page created
                // before this migration existed.
                if (!$post_id && $lang === 'en' && isset($existing[''][$months])) {
                    $post_id = $existing[''][$months];
                    unset($existing[''][$months]);
                }

                if ($post_id) {
                    $summary['reused']++;
                } else {
                    $post_id = wp_insert_post(array(
                        'post_title'   => $def['title'],
                        'post_name'    => $def['slug'],
                        'post_type'    => 'page',
                        'post_status'  => 'draft',
                        'post_content' => '',
                        'meta_input'   => array(
                            '_wp_page_template' => 'template-plan.php',
                        ),
                    ), true);

                    if (is_wp_error($post_id) || !$post_id) {
                        continue;
                    }

                    $summary['created']++;
                }

                // Length. update_field() writes the _plan_months field-key
                // reference too, which plain post meta would not.
                if (function_exists('update_field')) {
                    update_field('field_plan_months', (string) $months, $post_id);
                } else {
                    update_post_meta($post_id, 'plan_months', (string) $months);
                }

                // Language before the copy: iptv_plan_fill_acf() writes this
                // page's own language, and a page with no language yet would
                // confuse anything downstream that asks Polylang about it.
                if (function_exists('pll_set_post_language')) {
                    pll_set_post_language($post_id, $lang);
                }

                $summary['fields'] += iptv_plan_fill_acf(
                    $post_id,
                    $lang,
                    $months,
                    isset($def['label']) ? $def['label'] : ''
                );

                $groups[$months][$lang] = $post_id;
            }
        }

        // Link each plan's seven versions as translations of one another. This is
        // the step that cannot be done safely from outside WordPress.
        if (function_exists('pll_save_post_translations')) {
            foreach ($groups as $translations) {
                pll_save_post_translations($translations);
                $summary['linked']++;
            }
        }

        return $summary;
    }
}

/**
 * Run once per PLAN_PAGES_BUILD, on the first request after deploy.
 *
 * The flag is written before the work rather than after, so two requests
 * arriving together cannot both start building. If a run does fail part way,
 * bumping PLAN_PAGES_BUILD re-runs it — and because the whole thing matches on
 * existing pages first, the retry repairs instead of duplicating.
 */
add_action('init', function () {
    if ((int) get_option('iptv_plan_pages_built') === PLAN_PAGES_BUILD) {
        return;
    }

    // Polylang has to be up: without it every page would be created with no
    // language and the groups could not be linked.
    if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
        return;
    }

    update_option('iptv_plan_pages_built', PLAN_PAGES_BUILD, false);

    $summary = iptv_plan_build_pages();

    update_option('iptv_plan_pages_report', $summary, false);

    // LiteSpeed caches these pages the first time they are hit, so a template
    // or copy change that ships alongside a build bump would otherwise not be
    // visible until the cache expired on its own. Same call the price table
    // uses after a rebuild — see IPTV_Currency_Settings::rebuild_price_table().
    do_action('litespeed_purge_all');
}, 20);
