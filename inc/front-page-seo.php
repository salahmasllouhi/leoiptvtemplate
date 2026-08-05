<?php
/**
 * Rank Math analysis for the front page (every language).
 *
 * The front page stores no post content — every heading, paragraph and image
 * is rendered by the templates in front-page/sections/. Rank Math analyses the
 * editor's content, so on these pages it was scoring an empty string and
 * reporting "no images", "no rich media" and "focus keyword not in subheadings"
 * on a page that has all three.
 *
 * This is the same approach plan/inc/plan-seo.php already takes for the plan
 * pages: hand Rank Math a digest of what the page actually renders.
 *
 * Note this is an analysis fix, not a content fix. The alt text was already
 * correct and per-language — the templates read it from each translated page's
 * own `vod_image_alt` / `sports_image_alt` field rather than from the media
 * library, so the images do not need duplicating per language in Polylang.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('iptv_front_page_ids')) {
    /**
     * Every translation of the front page.
     *
     * @return int[]
     */
    function iptv_front_page_ids()
    {
        $front = (int) get_option('page_on_front');
        if (!$front) {
            return array();
        }

        // Polylang filters page_on_front to the current language, which in an
        // admin request is not necessarily the page being edited — so expand
        // whichever one we got back into the full translation set.
        if (function_exists('pll_get_post_translations')) {
            $translations = pll_get_post_translations($front);
            if (is_array($translations) && $translations) {
                return array_map('intval', array_values($translations));
            }
        }

        return array($front);
    }
}

if (!function_exists('iptv_front_page_field')) {
    /**
     * A front-page field, read from one specific translation.
     *
     * iptv_text() cannot be used here: it resolves against
     * get_option('page_on_front'), which in the admin is the current
     * language's front page rather than the one being edited. Same precedence
     * as iptv_text (ACF, then raw meta, then the template default), just bound
     * to an explicit post.
     *
     * @param int    $post_id
     * @param string $key
     * @param string $default
     * @return string
     */
    function iptv_front_page_field($post_id, $key, $default = '')
    {
        if (function_exists('get_field')) {
            $value = get_field($key, $post_id);
            if ($value !== null && $value !== '' && $value !== false && !is_array($value)) {
                return (string) $value;
            }
        }

        $meta = get_post_meta($post_id, $key, true);

        return (is_string($meta) && $meta !== '') ? $meta : $default;
    }
}

if (!function_exists('iptv_front_page_analysis_digest')) {
    /**
     * The rendered front page, as markup Rank Math can analyse.
     *
     * Only things the templates genuinely output are included — in particular
     * the two content images, with the same alt text the page really serves.
     * The hero backdrop is a CSS background rather than an <img>, so it is
     * deliberately absent: listing it would score an image Google cannot see.
     *
     * @param int $post_id
     * @return string
     */
    function iptv_front_page_analysis_digest($post_id)
    {
        $f = function ($key, $default = '') use ($post_id) {
            return iptv_front_page_field($post_id, $key, $default);
        };

        $out = array();

        // Hero (H1).
        $out[] = '<h1>' . $f('hero_title', 'Nordic IPTV Without Limits. Every Match. Every Channel.')
            . ' ' . $f('hero_title_span', 'One Subscription.')
            . ' ' . $f('hero_title_3', '$1,000 Saved. Zero Compromises.') . '</h1>';
        $out[] = '<p>' . $f('hero_subtitle', 'Looking for Nordic IPTV? NordicTV offers 40,000+ live channels, 200,000+ movies & series, and every sport in 4K/8K — on any device, instantly.') . '</p>';

        // Live channels.
        $out[] = '<h2>' . $f('showcase_title', 'Explore')
            . ' ' . $f('showcase_title_span', '40,000+')
            . ' ' . $f('showcase_title_3', 'live TV channels') . '</h2>';
        $out[] = '<p>' . $f('showcase_subtitle', 'From local Nordic news to global sports, entertainment, kids, and international channels — 198 countries covered.') . '</p>';

        // Movies & series, with the image the section renders.
        $out[] = '<h2>' . $f('vod_title', 'Indulge in')
            . ' ' . $f('vod_title_span', '200,000+')
            . ' ' . $f('vod_title_3', 'movies and series') . '</h2>';
        $out[] = '<p>' . $f('vod_subtitle', 'All genres and languages, on demand whenever it suits you.') . '</p>';
        $out[] = '<img src="https://nordictv.io/wp-content/uploads/2026/08/vodnordic.webp" alt="'
            . esc_attr($f('vod_image_alt', 'A selection of movies and series available on NordicTV')) . '" />';

        // Sport, with its image.
        $out[] = '<h2>' . $f('sports_title', 'Never Miss a')
            . ' ' . $f('sports_title_span', 'Game') . '</h2>';
        $out[] = '<p>' . $f('sports_desc', 'Never miss a game again. Every major league, every tournament, every PPV event.') . '</p>';
        $out[] = '<img src="https://nordictv.io/wp-content/uploads/2026/08/nordicsport.webp" alt="'
            . esc_attr($f('sports_image_alt', 'Live sport available on NordicTV')) . '" />';

        // Features.
        $out[] = '<h2>' . $f('features_title', 'Built for global viewers') . '</h2>';
        $out[] = '<p>' . $f('features_subtitle', 'From Nordic public TV to Premier League, Bollywood to Hollywood.') . '</p>';
        $cards = '';
        for ($i = 1; $i <= 8; $i++) {
            $title = $f("feature_{$i}_title");
            $desc  = $f("feature_{$i}_desc");
            if ($title || $desc) {
                $cards .= '<h3>' . $title . '</h3><p>' . $desc . '</p>';
            }
        }
        $out[] = $cards;

        // Devices, reviews, contact.
        $out[] = '<h2>' . $f('devices_section_title', 'Works on every screen') . '</h2>';
        $out[] = '<p>' . $f('devices_subtitle', 'Works flawlessly on Smart TV, Android, iOS, Firestick, MAG, and more.') . '</p>';
        $out[] = '<h2>' . $f('reviews_title', 'What our customers actually say') . '</h2>';
        $out[] = '<p>' . $f('reviews_subtitle', '') . '</p>';
        $out[] = '<h2>' . $f('contact_title', 'We\'re here to help') . '</h2>';
        $out[] = '<p>' . $f('contact_subtitle', '') . '</p>';

        // FAQ — questions are H3s on the page, so they count as subheadings.
        $out[] = '<h2>' . $f('faq_title', 'Frequently asked questions') . '</h2>';
        if (function_exists('get_field')) {
            $faq = get_field('faq_list', $post_id);
            if (is_array($faq)) {
                foreach ($faq as $row) {
                    if (!empty($row['question'])) {
                        $out[] = '<h3>' . $row['question'] . '</h3>'
                            . '<p>' . (isset($row['answer']) ? $row['answer'] : '') . '</p>';
                    }
                }
            }
        }

        return implode("\n", array_filter($out));
    }
}

/**
 * Give Rank Math the rendered page instead of the empty post body.
 *
 * Appended rather than substituted so that if anyone ever does type into the
 * editor, their copy still leads — that is what the "keyword at the beginning
 * of the content" test measures.
 */
add_filter('rank_math/researches/post_content', function ($content, $post = null) {
    $post = get_post($post);

    if (!$post || !in_array((int) $post->ID, iptv_front_page_ids(), true)) {
        return $content;
    }

    return $content . "\n" . iptv_front_page_analysis_digest($post->ID);
}, 10, 2);
