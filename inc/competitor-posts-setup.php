<?php
/**
 * One-shot language assignment for the competitor-brand posts.
 *
 * The six posts were written through the REST connector, which cannot reach
 * Polylang: the `language` taxonomy is not exposed over REST, so every post
 * arrived with no language and no translation group. Everything else about
 * them is already correct, so this file does the one thing the connector
 * could not, and nothing else.
 *
 * Same shape as inc/german-pages-setup.php: guarded by an option, runs on the
 * first request after deploy, writes a report, then can be deleted along with
 * its require in functions.php. See also inc/sport-retire.php.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/** Bump to re-run after a partial failure. */
define('COMPETITOR_POSTS_BUILD', 1);

/**
 * The posts, by ID, with the language each one was written in.
 *
 * Grouped by article. Every group is one article translated across languages,
 * so the IDs inside a group get linked to each other; the groups themselves
 * stay separate because they target different keywords.
 *
 * There is no English original for the Nordic One article: the searches come
 * from Sweden, Finland and Norway, so those are the languages that were
 * written. Polylang is content with a group that has no default-language
 * member.
 */
function iptv_competitor_post_groups()
{
    return array(
        // "nordic one iptv"
        'nordic_one' => array(
            'sv' => 3432,
            'no' => 3434,
            'fi' => 3436,
        ),

        // "streaming nordic iptv" — German only, so a group of one. Still
        // worth setting the language, which is the whole point of the file.
        'streaming_nordic' => array(
            'de' => 3438,
        ),

        // "nordic iptv king"
        'nordic_king' => array(
            'en' => 3428,
            'fi' => 3430,
        ),
    );
}

/**
 * Set each post's language, then link the group.
 *
 * @return array Report, stored so the run can be inspected after the fact.
 */
function iptv_competitor_assign_languages()
{
    $summary = array(
        'assigned' => 0,
        'linked'   => 0,
        'skipped'  => array(),
    );

    $known = (array) pll_languages_list(array('fields' => 'slug'));

    foreach (iptv_competitor_post_groups() as $slug => $group) {
        $payload = array();

        foreach ($group as $lang => $post_id) {
            // A post that was deleted, or a language that was never registered,
            // is reported rather than fixed: guessing at either would put the
            // wrong copy under the wrong URL.
            if (!in_array($lang, $known, true)) {
                $summary['skipped'][] = "$slug/$lang: language not registered";
                continue;
            }

            if (get_post_status($post_id) === false) {
                $summary['skipped'][] = "$slug/$lang: post $post_id not found";
                continue;
            }

            pll_set_post_language($post_id, $lang);
            $payload[$lang] = (int) $post_id;
            $summary['assigned']++;
        }

        // A single-language group needs no linking, and passing one member to
        // pll_save_post_translations() would be a no-op anyway.
        if (count($payload) > 1) {
            pll_save_post_translations($payload);
            $summary['linked']++;
        }
    }

    return $summary;
}

/**
 * Run once per COMPETITOR_POSTS_BUILD, on the first request after deploy.
 *
 * The flag is written before the work, so two requests arriving together
 * cannot both start. Re-running is safe: setting a language a post already has
 * is a no-op, and the translation group is rewritten from the same IDs.
 */
add_action('init', function () {
    if ((int) get_option('competitorposts_built') === COMPETITOR_POSTS_BUILD) {
        return;
    }

    if (!function_exists('pll_set_post_language')
        || !function_exists('pll_save_post_translations')
        || !function_exists('pll_languages_list')) {
        return;
    }

    update_option('competitorposts_built', COMPETITOR_POSTS_BUILD, false);

    // Prefixed so wp_get_plugin_settings can read it back over the connector.
    update_option('competitorposts_report', iptv_competitor_assign_languages(), false);
}, 25);
