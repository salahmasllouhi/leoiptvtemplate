<?php
/**
 * Rank Math and the front page (every language).
 *
 * Two things live here: the canonical URL each language's home page sends, and
 * the content Rank Math analyses when scoring it.
 *
 * The front page stores no post content — every heading, paragraph and image
 * is rendered by the templates in front-page/sections/. Rank Math analyses the
 * editor's content, so on these pages it was scoring an empty string.
 *
 * Two different consumers need feeding, because Rank Math analyses the page
 * twice with two unrelated code paths:
 *
 *   PHP — `rank_math/researches/post_content`, used server-side (bulk
 *   analysis, Analytics). It gets the whole digest, since nothing else adds to
 *   the empty post body there. Same approach as plan/inc/plan-seo.php.
 *
 *   JS — the score in the editor sidebar is computed in the browser from
 *   `wp.data`, so the PHP filter never reaches it. What does reach it is Rank
 *   Math's own ACF module (includes/modules/acf/assets/js/acf.js), which
 *   appends every ACF field to the analysed text through the `rank_math_content`
 *   JS filter. That is why keyword density and content length already looked
 *   right in the sidebar while "focus keyword in a subheading" and "image with
 *   the keyword as alt text" kept failing: the ACF module wraps every text
 *   field in <p>, never in <h2>, and the two content images are hardcoded in
 *   the templates rather than being ACF image fields, so no <img> ever reached
 *   the analyser. iptv_front_page_seo_editor_data() adds that missing
 *   structure — headings, images and the outbound support links — on the same
 *   JS filter.
 *
 * Note this is an analysis fix, not a content fix. Every language's page
 * already renders its focus keyword in an <h2> (reviews_title, faq_title) and
 * in both image alts — the templates read alt text from each translated page's
 * own `vod_image_alt` / `sports_image_alt` field rather than from the media
 * library, so the images do not need duplicating per language in Polylang.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Canonicalise each language's home page to itself.
 *
 * Rank Math builds the front page's canonical from home_url(), which is not
 * language-aware here: /dk/, /is/, /no/, /fi/ and /sv/ were all sending
 * `rel=canonical https://nordictv.io`, telling Google to fold every translated
 * home page into the English one — while the hreflang set right above it said
 * they were separate. Subpages were never affected; they canonicalise off the
 * permalink already.
 */
add_filter('rank_math/frontend/canonical', function ($canonical) {
    // is_page() as well as is_front_page(): a front page showing the blog has
    // no page to take a URL from.
    if (!is_front_page() || !is_page()) {
        return $canonical;
    }

    $post_id = (int) get_queried_object_id();
    if (!$post_id) {
        return $canonical;
    }

    // get_permalink() is not enough on its own. For a page WordPress considers
    // the front page it returns home_url('/'), and Polylang only filters
    // home_url() for a whitelist of callers — so /no/, /fi/ and /is/ came back
    // as the English home, while /dk/ and /sv/, which it does not treat as
    // front pages, came back right. pll_home_url() asks the question directly.
    if (function_exists('pll_home_url') && function_exists('pll_get_post_language')) {
        // 'slug' explicitly: that is what pll_home_url() keys off, and a
        // language name would quietly hand back the default language's home.
        $language = pll_get_post_language($post_id, 'slug');
        if ($language) {
            $home = pll_home_url($language);
            if ($home) {
                return $home;
            }
        }
    }

    $permalink = get_permalink($post_id);

    return $permalink ? $permalink : $canonical;
});

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

if (!function_exists('iptv_front_page_analysis_elements')) {
    /**
     * The rendered front page, element by element.
     *
     * Each entry is array(kind, html), where kind is 'copy' for a paragraph and
     * 'structure' for anything whose markup carries meaning Rank Math tests for
     * — headings, images, outbound links. Heading levels match what the
     * sections actually output, so the split titles are H3s, not H2s.
     *
     * Only things the templates genuinely render are included. The hero
     * backdrop is a CSS background rather than an <img>, so it is deliberately
     * absent: listing it would score an image Google cannot see.
     *
     * @param int $post_id
     * @return array<int,array{0:string,1:string}>
     */
    function iptv_front_page_analysis_elements($post_id)
    {
        $f = function ($key, $default = '') use ($post_id) {
            return iptv_front_page_field($post_id, $key, $default);
        };

        $out = array();

        // Hero (H1).
        $out[] = array('structure', '<h1>' . $f('hero_title', 'Nordic IPTV Without Limits. Every Match. Every Channel.')
            . ' ' . $f('hero_title_span', 'One Subscription.')
            . ' ' . $f('hero_title_3', '$1,000 Saved. Zero Compromises.') . '</h1>');
        $out[] = array('copy', '<p>' . $f('hero_subtitle', 'Looking for Nordic IPTV? NordicTV offers 40,000+ live channels, 200,000+ movies & series, and every sport in 4K/8K — on any device, instantly.') . '</p>');

        // Live channels.
        $out[] = array('structure', '<h3>' . $f('showcase_title', 'Explore')
            . ' ' . $f('showcase_title_span', '40,000+')
            . ' ' . $f('showcase_title_3', 'live TV channels') . '</h3>');
        $out[] = array('copy', '<p>' . $f('showcase_subtitle', 'From local Nordic news to global sports, entertainment, kids, and international channels — 198 countries covered.') . '</p>');

        // Movies & series, with the image the section renders.
        $out[] = array('structure', '<h3>' . $f('vod_title', 'Indulge in')
            . ' ' . $f('vod_title_span', '200,000+')
            . ' ' . $f('vod_title_3', 'movies and series') . '</h3>');
        $out[] = array('copy', '<p>' . $f('vod_subtitle', 'All genres and languages, on demand whenever it suits you.') . '</p>');
        $out[] = array('structure', '<img src="https://nordictv.io/wp-content/uploads/2026/08/vodnordic.webp" alt="'
            . esc_attr($f('vod_image_alt', 'A selection of movies and series available on NordicTV')) . '" />');

        // Sport, with its image.
        $out[] = array('structure', '<h3>' . $f('sports_title', 'Never Miss a')
            . ' ' . $f('sports_title_span', 'Game') . '</h3>');
        $out[] = array('copy', '<p>' . $f('sports_desc', 'Never miss a game again. Every major league, every tournament, every PPV event.') . '</p>');
        $out[] = array('structure', '<img src="https://nordictv.io/wp-content/uploads/2026/08/nordicsport.webp" alt="'
            . esc_attr($f('sports_image_alt', 'Live sport available on NordicTV')) . '" />');

        // Features.
        $out[] = array('structure', '<h2>' . $f('features_title', 'Built for global viewers') . '</h2>');
        $out[] = array('copy', '<p>' . $f('features_subtitle', 'From Nordic public TV to Premier League, Bollywood to Hollywood.') . '</p>');
        for ($i = 1; $i <= 8; $i++) {
            $title = $f("feature_{$i}_title");
            $desc  = $f("feature_{$i}_desc");
            if ($title) {
                $out[] = array('structure', '<h3>' . $title . '</h3>');
            }
            if ($desc) {
                $out[] = array('copy', '<p>' . $desc . '</p>');
            }
        }

        // Devices, reviews, contact.
        $out[] = array('structure', '<h2>' . $f('devices_section_title', 'Works on every screen') . '</h2>');
        $out[] = array('copy', '<p>' . $f('devices_subtitle', 'Works flawlessly on Smart TV, Android, iOS, Firestick, MAG, and more.') . '</p>');
        $out[] = array('structure', '<h2>' . $f('reviews_title', 'What our customers actually say') . '</h2>');
        $out[] = array('copy', '<p>' . $f('reviews_subtitle', '') . '</p>');
        $out[] = array('structure', '<h2>' . $f('contact_title', 'We\'re here to help') . '</h2>');
        $out[] = array('copy', '<p>' . $f('contact_subtitle', '') . '</p>');

        // Support cards. Their labels are H3s on the page and the WhatsApp and
        // Telegram cards are the page's only outbound links, which is a test of
        // its own ("link out to external resources").
        foreach (iptv_front_page_support_cards($post_id) as $card) {
            $out[] = array('structure', '<h3>' . $card['label'] . '</h3>');
            $out[] = array('structure', '<p><a href="' . esc_url($card['link']) . '">'
                . ($card['value'] !== '' ? $card['value'] : $card['label']) . '</a></p>');
        }

        // FAQ — questions are H3s on the page, so they count as subheadings.
        $out[] = array('structure', '<h2>' . $f('faq_title', 'Frequently asked questions') . '</h2>');
        if (function_exists('get_field')) {
            $faq = get_field('faq_list', $post_id);
            if (is_array($faq)) {
                foreach ($faq as $row) {
                    if (!empty($row['question'])) {
                        $out[] = array('structure', '<h3>' . $row['question'] . '</h3>');
                        if (!empty($row['answer'])) {
                            $out[] = array('copy', '<p>' . $row['answer'] . '</p>');
                        }
                    }
                }
            }
        }

        return $out;
    }
}

if (!function_exists('iptv_front_page_support_cards')) {
    /**
     * The support cards of one specific translation.
     *
     * iptv_contact_cards() reads the repeater off get_option('page_on_front'),
     * which in the admin is the current language's front page rather than the
     * one being edited — same reason iptv_front_page_field() exists. Falls back
     * to the same three defaults inc/contact-cards.php uses.
     *
     * @param int $post_id
     * @return array<int,array{label:string,value:string,link:string}>
     */
    function iptv_front_page_support_cards($post_id)
    {
        $cards = array();

        $rows = function_exists('get_field') ? get_field('contact_cards', $post_id) : null;
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (empty($row['card_label']) || empty($row['card_link'])) {
                    continue;
                }
                $cards[] = array(
                    'label' => $row['card_label'],
                    'value' => isset($row['card_value']) ? $row['card_value'] : '',
                    'link'  => $row['card_link'],
                );
            }
        }

        if ($cards) {
            return $cards;
        }

        return array(
            array(
                'label' => iptv_front_page_field($post_id, 'contact_card_email_label', 'Email Support'),
                'value' => iptv_front_page_field($post_id, 'contact_card_email_value', 'support@nordictv.io'),
                'link'  => 'mailto:support@nordictv.io',
            ),
            array(
                'label' => iptv_front_page_field($post_id, 'contact_card_whatsapp_label', 'WhatsApp'),
                'value' => iptv_front_page_field($post_id, 'contact_card_whatsapp_value', 'Chat with us live'),
                'link'  => 'https://wa.me/33745476690',
            ),
            array(
                'label' => iptv_front_page_field($post_id, 'contact_card_telegram_label', 'Telegram'),
                'value' => iptv_front_page_field($post_id, 'contact_card_telegram_value', '@NordicTV'),
                'link'  => 'https://t.me/NordicTV',
            ),
        );
    }
}

if (!function_exists('iptv_front_page_analysis_digest')) {
    /**
     * The rendered front page, as markup Rank Math can analyse.
     *
     * @param int  $post_id
     * @param bool $structure_only Drop the paragraphs and keep the headings,
     *                             images and links. Used in the editor, where
     *                             Rank Math's ACF module has already handed the
     *                             analyser every field's copy.
     * @return string
     */
    function iptv_front_page_analysis_digest($post_id, $structure_only = false)
    {
        $html = array();

        foreach (iptv_front_page_analysis_elements($post_id) as $element) {
            list($kind, $markup) = $element;
            if ($structure_only && $kind !== 'structure') {
                continue;
            }
            $html[] = $markup;
        }

        return implode("\n", array_filter($html));
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

/**
 * Give the editor's own analysis the structure its content is missing.
 *
 * The sidebar score is computed in the browser, so the PHP filter above never
 * reaches it — `rank_math_content` is the JS filter Rank Math applies to the
 * text it analyses, and the one its ACF module already uses to append every
 * field as a paragraph. This adds the headings, the two content images and the
 * outbound support links on top, none of which the ACF module can express.
 *
 * The copy is deliberately not repeated: it is already in the analysed text
 * once, and sending it twice would inflate the word count. If the ACF module is
 * not running at all the analysed text arrives near-empty, and then the full
 * digest is sent instead.
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php') {
        return;
    }

    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if (!$post_id || !in_array($post_id, iptv_front_page_ids(), true)) {
        return;
    }

    $handle = 'iptv-front-page-seo';
    // No file of its own — the handle exists only to carry the inline script.
    wp_register_script($handle, false, array('wp-hooks'), null, true);
    wp_enqueue_script($handle);

    $data = wp_json_encode(array(
        'structure' => iptv_front_page_analysis_digest($post_id, true),
        'full'      => iptv_front_page_analysis_digest($post_id),
    ));

    wp_add_inline_script($handle, <<<JS
(function (wp, data) {
    if (!wp || !wp.hooks) {
        return;
    }

    wp.hooks.addFilter('rank_math_content', 'nordictv/front-page', function (content) {
        content = content || '';

        var words = content.replace(/<[^>]*>/g, ' ').split(/\s+/).filter(Boolean).length;

        return content + '\\n' + (words < 200 ? data.full : data.structure);
    }, 20);

    // Rank Math may have scored the page before this filter existed.
    window.addEventListener('load', function () {
        if (window.rankMathEditor && typeof window.rankMathEditor.refresh === 'function') {
            window.rankMathEditor.refresh('content');
        }
    });
}(window.wp, {$data}));
JS
    );
});
