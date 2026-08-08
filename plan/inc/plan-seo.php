<?php
/**
 * Plan pages — Rank Math content analysis
 *
 * Rank Math's content tests read post_content. A plan page renders about 1,800
 * words, but almost none of it lives in post_content — it comes from ACF and
 * from the template — so Rank Math saw an all-but-empty page and scored the
 * content length, keyword-in-content and keyword-in-subheading tests against
 * nothing.
 *
 * This hands Rank Math the copy the visitor actually reads. It is built from
 * the same data the sections render from rather than by running the template,
 * which would mean prices, queries and a loop inside an admin request.
 *
 * The digest is assembled in the *page's* language, not the request's: the
 * analysis runs from wp-admin, which is English, while the page being analysed
 * may be Finnish.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('iptv_plan_is_plan_page')) {
    /**
     * @param int|WP_Post|null $post
     * @return bool
     */
    function iptv_plan_is_plan_page($post)
    {
        $post = get_post($post);

        return $post
            && $post->post_type === 'page'
            && get_post_meta($post->ID, '_wp_page_template', true) === 'template-plan.php';
    }
}

if (!function_exists('iptv_plan_analysis_digest')) {
    /**
     * The template-driven copy of one plan page, as HTML, in its own language.
     *
     * Headings are emitted as real <h2>/<h3> so the "focus keyword in
     * subheading" test sees the headings the page actually has.
     *
     * @param int $post_id
     * @return string
     */
    function iptv_plan_analysis_digest($post_id)
    {
        $lang = function_exists('pll_get_post_language')
            ? pll_get_post_language($post_id, 'slug')
            : '';

        $bundled = ($lang && $lang !== 'en' && function_exists('iptv_plan_translations'))
            ? iptv_plan_translations($lang)
            : array();

        // Same order plan_str() uses, minus the Polylang lookup: this runs in an
        // admin request, where the current language is not the page's.
        $t = function ($english) use ($bundled) {
            return isset($bundled[$english]) && $bundled[$english] !== ''
                ? $bundled[$english]
                : $english;
        };

        $months = (int) get_post_meta($post_id, 'plan_months', true);
        $months = in_array($months, array(1, 3, 6, 12), true) ? $months : 1;

        // Prefer whatever the editor has actually put in the fields; fall back
        // to the same defaults the sections would have rendered.
        $field = function ($key, $fallback = '') use ($post_id) {
            $value = function_exists('get_field') ? get_field($key, $post_id) : '';
            return ($value === null || $value === '' || $value === false) ? $fallback : $value;
        };

        $out = array();

        $out[] = '<p>' . $field('plan_subline', $months === 1
            ? $t('The whole service, one month at a time. No contract, no auto-renew — stop whenever you like.')
            : $t('The whole service for %s. One payment, no contract, no auto-renew.')) . '</p>';

        $out[] = '<h2>' . $field('plan_pricing_title', $t('%s — choose your screens')) . '</h2>';
        $out[] = '<p>' . $field('plan_pricing_subtitle', $t('One screen streams on one device at a time. Everything else is identical on every plan.')) . '</p>';

        // What every plan includes — shared front-page copy.
        $out[] = '<h2>' . iptv_text('plan_includes_title', 'Every plan is fully loaded') . '</h2>';
        $includes = array(
            '40,000+ Live TV Channels',
            '200,000+ Movies & Series (VOD)',
            '4K, Ultra HD & HD quality',
            'Stable, fast servers',
            'Full TV guide (EPG)',
            'Anti-Buffer™ 9.8',
            'Live hockey, football & handball',
            'Pay-Per-View (PPV) events',
            'Auto-updating channels & VOD',
            '24/7 support',
        );
        $items = '';
        foreach ($includes as $i => $item) {
            $items .= '<li>' . iptv_text('plan_includes_' . ($i + 1), $item) . '</li>';
        }
        $out[] = '<ul>' . $items . '</ul>';

        // Who this plan suits.
        $out[] = '<h2>' . $field('plan_audience_title', $t('Who the %s plan suits')) . '</h2>';

        $cards = $field('plan_audience_points', array());
        if (!is_array($cards) || empty($cards)) {
            $defaults = function_exists('iptv_plan_audience_defaults') ? iptv_plan_audience_defaults() : array();
            $cards    = array();
            foreach (isset($defaults[$months]) ? $defaults[$months] : array() as $card) {
                $cards[] = array('title' => $t($card['title']), 'text' => $t($card['text']));
            }
        }
        foreach ($cards as $card) {
            if (!empty($card['title'])) {
                $out[] = '<h3>' . $card['title'] . '</h3>';
                $out[] = '<p>' . (isset($card['text']) ? $card['text'] : '') . '</p>';
            }
        }

        // Compare table.
        $out[] = '<h2>' . $field('plan_compare_title', $t('How the four plans compare')) . '</h2>';
        $out[] = '<p>' . $field('plan_compare_subtitle', $t('Prices shown for %s. Longer plans cost less per month — the service is the same on all of them.')) . '</p>';

        // FAQ.
        $out[] = '<h2>' . iptv_text('faq_title', 'Frequently asked questions') . '</h2>';

        $faq = $field('plan_faq', array());
        if (!is_array($faq) || empty($faq)) {
            $faq = array();
            $rows = function_exists('iptv_plan_faq_defaults') ? iptv_plan_faq_defaults($months) : array();
            foreach ($rows as $row) {
                $faq[] = array('question' => $t($row['q']), 'answer' => $t($row['a']));
            }
        }
        foreach ($faq as $row) {
            if (!empty($row['question'])) {
                $out[] = '<h3>' . $row['question'] . '</h3>';
                $out[] = '<p>' . (isset($row['answer']) ? $row['answer'] : '') . '</p>';
            }
        }

        // Closing band.
        $out[] = '<h2>' . $field('plan_final_title', $t('Start your %s plan today')) . '</h2>';
        $out[] = '<p>' . $field('plan_final_text', $t('Activated in about a minute, watchable on the TV you already own.')) . '</p>';

        return implode("\n", $out);
    }
}

if (!function_exists('iptv_plan_followed_domains')) {
    /**
     * External domains that must keep a followed link.
     *
     * Rank Math is configured to nofollow every external link in post content,
     * which makes its own "Linking to external content with a followed link"
     * test impossible to pass — it rewrote our references to
     * rel="nofollow noopener" before they reached the page. Its own guidance is
     * to whitelist trusted domains rather than nofollow everything; this is that
     * whitelist, kept in the theme because the plugin's options are not
     * reachable from here.
     *
     * @return string[] Host suffixes, matched against the link's host.
     */
    function iptv_plan_followed_domains()
    {
        return apply_filters('iptv_plan_followed_domains', array(
            'ayoplayer.com',
            'wikipedia.org',
        ));
    }
}

/**
 * Drop rel="nofollow" from links to whitelisted domains.
 *
 * Runs at priority 99 on the_content, i.e. after Rank Math has added its
 * attributes, so it undoes the nofollow rather than racing it. Only the
 * nofollow token is removed — noopener and target are left alone, since those
 * are about safety rather than about SEO.
 */
add_filter('the_content', function ($content) {
    if (!iptv_plan_is_plan_page(get_post()) || strpos($content, 'nofollow') === false) {
        return $content;
    }

    $domains = iptv_plan_followed_domains();

    return preg_replace_callback('/<a\b[^>]*>/i', function ($m) use ($domains) {
        $tag = $m[0];

        if (!preg_match('/href=["\']([^"\']+)["\']/i', $tag, $href)) {
            return $tag;
        }

        $host = parse_url($href[1], PHP_URL_HOST);
        if (!$host) {
            return $tag;
        }

        $allowed = false;
        foreach ($domains as $domain) {
            if ($host === $domain || substr($host, -strlen('.' . $domain)) === '.' . $domain) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return $tag;
        }

        // Strip only the nofollow token, then tidy up what it leaves behind:
        // an empty rel="" is invalid-ish and a doubled space is just untidy.
        $tag = preg_replace_callback('/\srel=["\']([^"\']*)["\']/i', function ($r) {
            $rels = array_values(array_filter(
                preg_split('/\s+/', $r[1]),
                function ($v) { return $v !== '' && strtolower($v) !== 'nofollow'; }
            ));
            return $rels ? ' rel="' . implode(' ', $rels) . '"' : '';
        }, $tag);

        return preg_replace('/\s{2,}/', ' ', $tag);
    }, $content);
}, 99);

/**
 * Give Rank Math the whole page, not just the body field.
 *
 * Appended rather than substituted, so the body copy an editor writes is still
 * the start of the content — which is what the "keyword at the beginning of the
 * content" test measures.
 */
add_filter('rank_math/researches/post_content', function ($content, $post = null) {
    $post = get_post($post);

    if (!$post || !iptv_plan_is_plan_page($post)) {
        return $content;
    }

    return $content . "\n" . iptv_plan_analysis_digest($post->ID);
}, 10, 2);
