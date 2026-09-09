<?php
/**
 * Teach Rank Math which external domains keep a followed link.
 *
 * Rank Math's General settings have "Nofollow External Links" switched on, so
 * every outbound link in post content is rewritten to rel="nofollow". That
 * breaks its own "linking to external resources with a followed link" test:
 * the analyser reads the same setting, so it reports every outbound link as
 * nofollow no matter what the content says.
 *
 * plan/inc/plan-seo.php already strips the nofollow token back out of the
 * rendered HTML for a small whitelist of trusted domains. That fixes the page
 * a visitor sees, but not the editor's score, because the content analysis
 * runs in the browser against the setting rather than against the output.
 * Rank Math's own answer is the "Nofollow Exclude Domains" list, which this
 * writes, using iptv_plan_followed_domains() so the two stay in agreement
 * instead of drifting apart.
 *
 * Merges one key into the existing option rather than rewriting it: the array
 * holds ~60 unrelated settings and reconstructing it by hand would be a good
 * way to silently lose one.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/** Bump to re-run after changing the whitelist. */
define('RANKMATH_FOLLOWED_DOMAINS_BUILD', 2);

/**
 * Merge the theme's followed-domain whitelist into Rank Math's exclude list.
 *
 * @return array Report, stored so the run can be inspected afterwards.
 */
function iptv_rankmath_sync_followed_domains()
{
    $option = 'rank-math-options-general';
    $opts   = get_option($option, array());

    if (!is_array($opts)) {
        return array('ok' => false, 'why' => 'option is not an array');
    }

    $wanted = function_exists('iptv_plan_followed_domains')
        ? iptv_plan_followed_domains()
        : array();

    if (!$wanted) {
        return array('ok' => false, 'why' => 'no whitelist available');
    }

    // Stored as a textarea: one domain per line.
    $existing = isset($opts['nofollow_exclude_domains'])
        ? preg_split('/\R/', (string) $opts['nofollow_exclude_domains'], -1, PREG_SPLIT_NO_EMPTY)
        : array();

    $existing = array_map('trim', $existing);
    $merged   = array_values(array_unique(array_filter(array_merge($existing, $wanted))));

    sort($merged);

    if ($merged === $existing) {
        return array('ok' => true, 'changed' => false, 'domains' => $merged);
    }

    $opts['nofollow_exclude_domains'] = implode("\n", $merged);
    update_option($option, $opts);

    return array(
        'ok'       => true,
        'changed'  => true,
        'added'    => array_values(array_diff($merged, $existing)),
        'domains'  => $merged,
        'nofollow' => isset($opts['nofollow_external_links']) ? $opts['nofollow_external_links'] : 'unset',
    );
}

/**
 * Run once per RANKMATH_FOLLOWED_DOMAINS_BUILD, on the first request after
 * deploy. Flag written before the work so two requests cannot both run it.
 */
add_action('init', function () {
    if ((int) get_option('rmfollowed_built') === RANKMATH_FOLLOWED_DOMAINS_BUILD) {
        return;
    }

    // The whitelist lives in plan-seo.php, which loads later in functions.php.
    if (!function_exists('iptv_plan_followed_domains')) {
        return;
    }

    update_option('rmfollowed_built', RANKMATH_FOLLOWED_DOMAINS_BUILD, false);

    // Prefixed so wp_get_plugin_settings can read it back over the connector.
    update_option('rmfollowed_report', iptv_rankmath_sync_followed_domains(), false);
}, 30);
