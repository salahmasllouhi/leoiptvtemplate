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
