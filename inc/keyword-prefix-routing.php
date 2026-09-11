<?php
/**
 * URL prefix routing for /nordiciptv/
 *
 * Site Address (URL) in Settings > General points at
 * https://nordictv.io/nordiciptv while WordPress Address (URL) stays at
 * https://nordictv.io — core files never moved. That split keeps wp-admin,
 * wp-content and wp-json reachable at the real root, but it means every
 * front-end request WordPress has to resolve still arrives as
 * /nordiciptv/whatever: .htaccess's catch-all gets the request to index.php,
 * but $_SERVER['REQUEST_URI'] still holds the client's original path, and
 * that is what WP_Rewrite matches permalinks against. Without this filter,
 * every URL 404s because nothing is registered at a "nordiciptv" slug.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

define('NORDICTV_URL_PREFIX', 'nordiciptv');

/**
 * Strips the /nordiciptv prefix from the request path before WordPress's own
 * rewrite rules try to match it, so a request for /nordiciptv/some-post/
 * resolves exactly like /some-post/ always did.
 *
 * @param bool $do_parse
 * @return bool
 */
function nordictv_strip_url_prefix($do_parse)
{
    $prefix = '/' . NORDICTV_URL_PREFIX;
    $uri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

    if ($uri === $prefix) {
        $_SERVER['REQUEST_URI'] = '/';
    } elseif (strpos($uri, $prefix . '/') === 0) {
        $_SERVER['REQUEST_URI'] = substr($uri, strlen($prefix));
    }

    return $do_parse;
}
add_filter('do_parse_request', 'nordictv_strip_url_prefix');
