<?php
/**
 * TEMPORARY probe: find out what makes the English front page uncacheable.
 *
 * The homepage answers every request with `X-LiteSpeed-Cache-Control: no-cache`
 * while /sv/ caches for a week, and none of the obvious candidates explain it:
 * LiteSpeed's own exclude lists hold nothing but sitemap URLs, and the theme's
 * front-page rule in inc/language-preference.php only fires when a language
 * cookie is set — a cookieless request still comes back no-cache.
 *
 * So the answer has to be read from inside the request. This records who calls
 * LiteSpeed's nocache action, and brackets the point where the page stops being
 * cacheable, by sampling Control::is_cacheable() at each stage of the load.
 *
 * Everything here is gated behind ?nocache_probe=1, so a normal visitor pays
 * nothing and the report is not exposed on the live page.
 *
 * Delete this file, and its require in functions.php, once the cause is fixed.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Is this request asking for the probe?
 *
 * @return bool
 */
function nordictv_nocache_probe_on()
{
    return !empty($_GET['nocache_probe']);
}

/**
 * LiteSpeed's current verdict on whether this response may be cached.
 *
 * @return int 1 cacheable, 0 not, -1 when LiteSpeed is not loaded.
 */
function nordictv_nocache_probe_cacheable()
{
    if (!class_exists('\\LiteSpeed\\Control')) {
        return -1;
    }

    return (int) \LiteSpeed\Control::is_cacheable();
}

/**
 * Anyone calling the public nocache action names themselves here.
 */
add_action('litespeed_control_set_nocache', function ($reason = '') {
    if (!nordictv_nocache_probe_on()) {
        return;
    }

    $frames = array();
    foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15) as $frame) {
        if (!empty($frame['file'])) {
            $frames[] = str_replace(ABSPATH, '', $frame['file'])
                . ':' . (isset($frame['line']) ? $frame['line'] : '?');
        }
    }

    $GLOBALS['nordictv_nocache_calls'][] = array(
        'reason' => (string) $reason,
        'trace'  => $frames,
    );
}, 1);

/**
 * Sample the cacheable flag through the request, so the stage where it flips
 * is visible even when whatever flipped it never fired the public action.
 */
foreach (array('muplugins_loaded', 'plugins_loaded', 'init', 'wp', 'template_redirect', 'wp_head', 'wp_footer') as $nordictv_probe_hook) {
    add_action($nordictv_probe_hook, function () use ($nordictv_probe_hook) {
        if (!nordictv_nocache_probe_on()) {
            return;
        }

        $GLOBALS['nordictv_nocache_stages'][$nordictv_probe_hook] = nordictv_nocache_probe_cacheable();
    }, 99999);
}
unset($nordictv_probe_hook);

/**
 * Print the report as a comment at the very end of the response.
 */
add_action('shutdown', function () {
    if (!nordictv_nocache_probe_on()) {
        return;
    }

    if (!function_exists('get_mu_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $headers = array();
    foreach (headers_list() as $header) {
        if (stripos($header, 'litespeed') !== false || stripos($header, 'cache-control') !== false) {
            $headers[] = $header;
        }
    }

    $report = array(
        'is_front_page'   => (int) is_front_page(),
        'queried_id'      => get_queried_object_id(),
        'pll_current'     => function_exists('pll_current_language') ? pll_current_language('slug') : '',
        'cookies'         => array_keys($_COOKIE),
        'stored_language' => function_exists('nordictv_stored_language') ? nordictv_stored_language() : 'n/a',
        'cacheable_now'   => nordictv_nocache_probe_cacheable(),
        'stages'          => isset($GLOBALS['nordictv_nocache_stages']) ? $GLOBALS['nordictv_nocache_stages'] : array(),
        'nocache_calls'   => isset($GLOBALS['nordictv_nocache_calls']) ? $GLOBALS['nordictv_nocache_calls'] : array(),
        'mu_plugins'      => array_keys(get_mu_plugins()),
        'headers'         => $headers,
    );

    echo "\n<!-- NOCACHE-PROBE\n"
        . wp_json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        . "\n-->\n";
}, 99999);
