<?php
/**
 * TEMPORARY diagnostic — delete after use.
 *
 * Runs Rank Math's own subheading / image-alt / outbound-link tests against the
 * digest inc/front-page-seo.php hands the editor, for every language.
 */

require_once dirname(__FILE__) . '/../../../wp-load.php';

header('Content-Type: text/plain; charset=utf-8');

foreach (iptv_front_page_ids() as $id) {
    $keyword = (string) get_post_meta($id, 'rank_math_focus_keyword', true);
    $keyword = trim(strtolower(explode(',', $keyword)[0]));
    $digest  = iptv_front_page_analysis_digest($id, true);
    $lower   = mb_strtolower($digest);

    $joined = implode('[\s\W]+', array_map(function ($w) {
        return preg_quote($w, '/');
    }, explode(' ', $keyword)));
    $subheading = preg_match('/<h[2-6][^>]*>[\s\S]*?' . $joined . '[\s\S]*?<\/h[2-6]>/i', $lower);

    preg_match_all('/<img[^>]*\salt=(["\'])(.*?)\1/i', $lower, $images);
    $altPattern = str_replace(' ', '.*', preg_quote($keyword, '/'));
    $alt = 0;
    foreach ($images[2] as $a) {
        if (preg_match('/' . $altPattern . '/i', $a)) {
            $alt = 1;
        }
    }

    preg_match_all('/<a[^>]*href=["\'](https?:\/\/[^"\']+)/i', $digest, $links);
    $external = 0;
    foreach ($links[1] as $href) {
        if (strpos($href, 'nordictv.io') === false) {
            $external++;
        }
    }

    printf(
        "%-5d %-14s subheading:%-5s image-alt:%-5s outbound:%-5s (%d imgs, %d ext, %d words)\n",
        $id,
        $keyword,
        $subheading ? 'PASS' : 'FAIL',
        $alt ? 'PASS' : 'FAIL',
        $external ? 'PASS' : 'FAIL',
        count($images[2]),
        $external,
        str_word_count(strip_tags($digest))
    );
}
