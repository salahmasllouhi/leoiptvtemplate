<?php
/**
 * Keeps .htaccess pointed at the real document root when Site Address carries
 * a path.
 *
 * Site Address (URL) is https://nordictv.io/nordiciptv while WordPress Address
 * (URL) stays at https://nordictv.io — core files never moved, so wp-admin,
 * wp-content and wp-json stay reachable unprefixed. WordPress needs nothing
 * else to serve the prefixed URLs: WP::parse_request() strips the home path
 * from REQUEST_URI itself, so /nordiciptv/pricing/ resolves exactly like
 * /pricing/ always did.
 *
 * What it does not survive is a permalink flush. WP_Rewrite::mod_rewrite_rules()
 * builds its rules from home_url(), so any flush — Settings > Permalinks, or a
 * plugin calling flush_rewrite_rules() — rewrites the managed block as
 *
 *     RewriteBase /nordiciptv/
 *     RewriteRule . /nordiciptv/index.php [L]
 *
 * and /nordiciptv/index.php does not exist on disk. Every front-end URL dies at
 * the web server, before PHP, and nothing in wp-admin explains why. This pins
 * both lines back to the root the files actually live at.
 *
 * Do not "fix" this by mutating $_SERVER['REQUEST_URI'] instead: redirect_canonical()
 * reads that value to decide whether the address bar already matches the
 * canonical URL, so stripping the prefix there makes every request look
 * un-prefixed and 301 to itself forever.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @param string $rules Generated mod_rewrite block.
 * @return string
 */
function nordictv_pin_rewrite_rules_to_root($rules)
{
    global $wp_rewrite;

    $home_path = wp_parse_url(home_url(), PHP_URL_PATH);

    if (!is_string($home_path) || trim($home_path, '/') === '') {
        return $rules;
    }

    $home_root = trailingslashit($home_path);
    $index     = isset($wp_rewrite->index) ? $wp_rewrite->index : 'index.php';

    return str_replace(
        array(
            "RewriteBase {$home_root}\n",
            "RewriteRule . {$home_root}{$index} [L]\n",
        ),
        array(
            "RewriteBase /\n",
            "RewriteRule . /{$index} [L]\n",
        ),
        $rules
    );
}
add_filter('mod_rewrite_rules', 'nordictv_pin_rewrite_rules_to_root');

/**
 * 301s the un-prefixed URL space onto the prefixed one.
 *
 * Core only canonicalises the front page and Polylang only the language roots,
 * so without this every page answers at both /about-us and
 * /nordiciptv/about-us — duplicate content, and none of the old URL's
 * authority moves to the new one, which was the entire point of the move.
 *
 * It also catches the absolute URLs saved inside ACF link fields and post
 * content, which name the bare domain and cannot follow home_url().
 *
 * 404s are left to inc/legacy-redirects.php, which runs at priority 20 and
 * resolves them to a real destination. Redirecting them here first would only
 * add a hop in front of its answer.
 */
function nordictv_prefix_redirect()
{
    if (is_admin() || is_robots() || is_favicon() || is_404() || headers_sent()) {
        return;
    }

    if (wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }

    $prefix = wp_parse_url(home_url(), PHP_URL_PATH);
    $prefix = is_string($prefix) ? trim($prefix, '/') : '';

    // Inert while Site Address carries no path. That is what makes reverting
    // WP_HOME a complete rollback: this stops redirecting on its own, instead
    // of sending every URL to itself forever.
    if ($prefix === '') {
        return;
    }

    $request = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path    = (string) wp_parse_url($request, PHP_URL_PATH);

    if ($path === '') {
        return;
    }

    $trimmed = trim($path, '/');

    if ($trimmed === $prefix || strpos($trimmed, $prefix . '/') === 0) {
        return;
    }

    $target = home_url($path);
    $query  = (string) wp_parse_url($request, PHP_URL_QUERY);

    if ($query !== '') {
        $target .= '?' . $query;
    }

    // Same reasoning as inc/legacy-redirects.php: a browser caches a 301
    // indefinitely, so a rule that ships wrong would outlive its own fix for
    // everyone who hit it. The SEO value is in the status code, not the cache.
    do_action('litespeed_control_set_nocache', 'nordictv prefix redirect');
    header('X-LiteSpeed-Cache-Control: no-cache');
    header('Cache-Control: no-store, max-age=0');

    wp_redirect($target, 301);
    exit;
}
add_action('template_redirect', 'nordictv_prefix_redirect', 10);
