<?php
/**
 * Page links
 *
 * Resolves a page by slug and returns the current language's translation of it.
 *
 * The footer used to hardcode `href="#"` for every legal link, so About Us,
 * Privacy Policy, Terms of Service and the refund policy all pointed nowhere on
 * every language of the site — despite all four pages existing. The other footer
 * column built its URLs with home_url(), which is not language-aware, so the
 * Swedish footer linked to the English blog and setup guide.
 *
 * iptv_page_url() fixes both: it finds the page whatever language it was created
 * in, then hands the ID to Polylang to get this language's version. When a
 * translation does not exist yet it returns the page it did find, because a link
 * to the English page is more use than a dead one.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('iptv_page_url')) {
    /**
     * Permalink of a page in the current language, looked up by slug.
     *
     * $lang exists for inc/legacy-redirects.php. On a 404 under a prefix
     * Polylang does not know — /de/…, /da/… — the current language is always
     * the site default, so a redirect that needs to land on a specific
     * language has to name it rather than rely on the request.
     *
     * $strict refuses to cross languages. The default is to return whatever
     * translation was found — a link to the English page beats a dead one in a
     * footer. A 301 is not a footer link: sending /no/pages/checkout to an
     * English page contradicts hreflang and reads to Google as a soft 404, so
     * inc/legacy-redirects.php asks for a miss it can turn into a better guess.
     *
     * @param string $slug     Page slug in any language — Polylang maps it across.
     * @param string $fallback Returned when no such page exists.
     * @param string $lang     Target language slug. Empty means the current one.
     * @param bool   $strict   Return $fallback rather than another language's page.
     * @return string Permalink, or $fallback, or '' if there is neither.
     */
    function iptv_page_url($slug, $fallback = '', $lang = '', $strict = false)
    {
        static $cache = array();

        if ($lang === '') {
            $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';
        }

        $key = $slug . '|' . $lang . '|' . (int) $strict;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $url = '';

        $args = array(
            'post_type'              => 'page',
            'name'                   => $slug,
            'post_status'            => 'publish',
            'posts_per_page'         => 1,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
        );

        // Search every language explicitly. 'lang' => '' does NOT mean "all" —
        // Polylang reads it as the current language, which silently breaks any
        // page whose translation was given its own slug: looking up the English
        // m3u-playlist-convert-your-m3u-url from /sv/ found nothing, because the
        // Swedish page is m3u-lank-konverterare. Pages that happen to share a
        // slug across languages hid the bug.
        //
        // Which translation the query lands on does not matter — every member of
        // a translation group maps to the same set, so pll_get_post() below
        // resolves the right one either way.
        if (function_exists('nordictv_lang_slugs')) {
            $languages = nordictv_lang_slugs();
            if (!empty($languages)) {
                $args['lang'] = implode(',', $languages);
            }
        }

        $query = new WP_Query($args);

        if (!empty($query->posts)) {
            $id = $query->posts[0]->ID;

            if (function_exists('pll_get_post')) {
                $translated = pll_get_post($id, $lang);
                if ($translated) {
                    $id = $translated;
                } elseif ($strict) {
                    $id = 0;
                }
            }

            $url = $id ? get_permalink($id) : '';
        }

        if (!$url) {
            $url = $fallback;
        }

        $cache[$key] = $url;

        return $url;
    }
}

if (!function_exists('iptv_footer_links')) {
    /**
     * Render a list of footer links, skipping any whose page is missing.
     *
     * Printing a link to '' would point at the current page, which is worse than
     * the dead '#' it replaces, so unresolved entries are dropped entirely.
     *
     * @param array $links Each entry: ['slug' => …, 'key' => …, 'label' => …]
     *                     or ['url' => …, 'key' => …, 'label' => …] for an
     *                     external destination.
     */
    function iptv_footer_links(array $links)
    {
        echo '<div>';

        foreach ($links as $link) {
            $url = isset($link['url'])
                ? $link['url']
                : iptv_page_url($link['slug'], isset($link['fallback']) ? $link['fallback'] : '');

            if (!$url) {
                continue;
            }

            printf(
                '<a href="%s">%s</a>',
                esc_url($url),
                esc_html(iptv_text($link['key'], $link['label']))
            );
        }

        echo '</div>';
    }
}
