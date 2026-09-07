<?php
/**
 * German pages — one-time provisioning
 *
 * Creates the German counterpart of every page the other languages already
 * have, assigns it the `de` language and joins it to the existing translation
 * group so Polylang, the language switcher, hreflang and iptv_page_url() all
 * see one page in seven languages rather than a seventh orphan.
 *
 * Why this lives in the theme rather than being done over the REST API: a
 * Polylang translation group is a term in the `post_translations` taxonomy
 * whose *description* is a serialized lang => post_id map. Writing that by hand
 * over an API is fragile — anything that sanitises the description corrupts the
 * group silently. pll_set_post_language() and pll_save_post_translations() are
 * Polylang's own API for it, and they can only be called from inside WordPress.
 * Same reasoning, and the same shape, as plan/inc/plan-pages-setup.php, which
 * provisions the four plan pages and is deliberately not duplicated here.
 *
 * Content is translated by substitution into the *English page's stored
 * content* rather than by shipping a German copy of it. These pages carry
 * hand-written markup — a wp:html block with its own CSS on the FAQ, Gutenberg
 * block comments everywhere — and a re-typed copy drifts from the original the
 * first time anyone edits one. A substitution table can only change the words
 * it names; every tag, class and shortcode is carried across byte for byte, and
 * a key that stops matching leaves that passage in English instead of breaking
 * the block.
 *
 * Pages with no table are created with the English content, which is what the
 * Norwegian, Danish, Finnish and Icelandic copies of the legal pages hold
 * today. Translating those is a decision about legal text, not a migration's.
 *
 * Idempotent. A page is matched through the source page's translation group
 * before anything is created, so re-running repairs rather than duplicates. To
 * re-run after editing the table below, bump GERMAN_PAGES_BUILD.
 *
 * The pages and their translation groups live in the database once this has
 * run, so the file is inert after its build number and can be deleted along
 * with its require in functions.php. Keeping it keeps the German copy in
 * version control and makes a repair a one-line build bump.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

// Bump to re-run after changing the table below. Re-running is safe: existing
// pages are matched and reused, and only an *empty* German page ever has its
// content rewritten, so nothing edited in wp-admin is overwritten.
define('GERMAN_PAGES_BUILD', 2);

if (!function_exists('iptv_de_page_definitions')) {
    /**
     * The German page set: one entry per English page to translate.
     *
     * `source` is the English page. `slug` is the German URL — keyword-bearing
     * where the page has a keyword to bear, and only applied at creation, so
     * editing this table never moves a live URL. `copy` names a substitution
     * table in inc/german-content/; without one the English content is used.
     * `fields` names an ACF table in the same directory, for a page whose copy
     * is fields rather than content.
     *
     * @return array<int,array{source:int,title:string,slug:string,copy:string,fields?:string}>
     */
    function iptv_de_page_definitions()
    {
        return array(
            // The home page. Its copy is ACF fields on the page itself, not
            // post content, so there is nothing to substitute — see
            // inc/iptv-text.php. Joining page 6's group is what makes Polylang
            // serve this one at /de/.
            array('source' => 6,   'title' => 'Startseite',                        'slug' => 'home-de',                      'copy' => '', 'fields' => 'home-fields'),

            array('source' => 16,  'title' => 'Über uns',                          'slug' => 'ueber-uns',                    'copy' => 'about-us'),
            array('source' => 18,  'title' => 'Kontakt',                           'slug' => 'kontakt',                      'copy' => ''),
            array('source' => 83,  'title' => 'IPTV Anleitung: Einrichtung, Apps und Geräte', 'slug' => 'iptv-anleitung-einrichtung', 'copy' => 'user-guide'),
            array('source' => 134, 'title' => 'Blog',                              'slug' => 'blog',                         'copy' => ''),
            array('source' => 150, 'title' => 'IPTV FAQ: häufige Fragen zu IPTV-Diensten', 'slug' => 'iptv-haeufige-fragen', 'copy' => 'faq'),
            array('source' => 326, 'title' => 'M3U Playlist Konverter',            'slug' => 'm3u-playlist-konverter',       'copy' => ''),

            // Legal. Created so the German footer links somewhere real; the
            // text is still the English original, as it is in no/dk/fi/is.
            array('source' => 3,   'title' => 'Datenschutzerklärung',              'slug' => 'datenschutzerklaerung',        'copy' => ''),
            array('source' => 12,  'title' => 'Nutzungsbedingungen',               'slug' => 'nutzungsbedingungen',          'copy' => ''),
            array('source' => 14,  'title' => 'Rückgabe- und Erstattungsrichtlinie', 'slug' => 'rueckgabe-erstattungsrichtlinie', 'copy' => ''),
        );
    }
}

if (!function_exists('iptv_de_translate_content')) {
    /**
     * The English content with the German substitutions applied.
     *
     * strtr() rather than a str_replace() loop: it scans once and prefers the
     * longest key matching at each position, so a short key ("features") cannot
     * eat a longer one that contains it ("features and benefits") and no
     * replacement can be re-replaced by a later pair.
     *
     * @param string $content English source content.
     * @param string $table   File basename in inc/german-content/.
     * @return string
     */
    function iptv_de_translate_content($content, $table)
    {
        if ($table === '' || $content === '') {
            return $content;
        }

        $file = get_template_directory() . '/inc/german-content/' . $table . '.php';

        if (!file_exists($file)) {
            return $content;
        }

        $pairs = (array) include $file;

        return $pairs ? strtr($content, $pairs) : $content;
    }
}

if (!function_exists('iptv_de_fill_fields')) {
    /**
     * Write the German front-page copy into the German home page's ACF fields.
     *
     * The front page keeps no post content — iptv_text() reads every heading and
     * paragraph from an ACF field on the page being served — so the German home
     * page is translated here rather than through the substitution tables the
     * other pages use.
     *
     * Only ever fills an *empty* field, for the same reason
     * iptv_plan_fill_acf() does: a re-run after someone has edited the page in
     * wp-admin must not overwrite their wording. That is the whole point of the
     * copy living in ACF rather than only in the theme.
     *
     * @param int    $post_id German page.
     * @param string $table   File basename in inc/german-content/.
     * @return int Number of fields written.
     */
    function iptv_de_fill_fields($post_id, $table)
    {
        if ($table === '' || !function_exists('update_field') || !function_exists('get_field')) {
            return 0;
        }

        $file = get_template_directory() . '/inc/german-content/' . $table . '.php';

        if (!file_exists($file)) {
            return 0;
        }

        $written = 0;

        foreach ((array) include $file as $name => $value) {
            if ($value === '' || $value === array()) {
                continue;
            }

            $existing = get_field($name, $post_id);

            // A link field comes back as an array whose parts can all be empty;
            // treat that as unset rather than as content someone typed.
            if (is_array($existing)) {
                $existing = implode('', array_filter(array_map(
                    function ($part) {
                        return is_scalar($part) ? (string) $part : '';
                    },
                    $existing
                )));
            }

            if ($existing !== null && $existing !== '' && $existing !== false) {
                continue; // edited in wp-admin — leave it alone
            }

            update_field($name, $value, $post_id);
            $written++;
        }

        return $written;
    }
}

if (!function_exists('iptv_de_build_pages')) {
    /**
     * Create anything missing, then wire the translation groups.
     *
     * @return array Summary, stored for inspection.
     */
    function iptv_de_build_pages()
    {
        $summary = array('created' => 0, 'reused' => 0, 'linked' => 0, 'fields' => 0, 'missing' => array());

        foreach (iptv_de_page_definitions() as $def) {
            $source = get_post($def['source']);

            if (!$source || $source->post_type !== 'page') {
                $summary['missing'][] = $def['source'];
                continue;
            }

            // The source's own group is both the existence test and the thing
            // the new page has to join, so it is read before anything else.
            $group = function_exists('pll_get_post_translations')
                ? (array) pll_get_post_translations($source->ID)
                : array();

            $post_id = isset($group['de']) ? (int) $group['de'] : 0;

            // A German page in the group that has since been deleted or
            // trashed is not a page — fall through and make a new one.
            if ($post_id) {
                $existing = get_post($post_id);
                if (!$existing || $existing->post_status === 'trash') {
                    $post_id = 0;
                    unset($group['de']);
                }
            }

            $content = iptv_de_translate_content($source->post_content, $def['copy']);

            if ($post_id) {
                $summary['reused']++;

                // Only fill a page that is still empty. A re-run after someone
                // has written German copy in wp-admin must not overwrite it.
                $current = get_post($post_id);
                if ($current && trim($current->post_content) === '' && trim($content) !== '') {
                    wp_update_post(array('ID' => $post_id, 'post_content' => $content));
                }
            } else {
                $post_id = wp_insert_post(array(
                    'post_title'   => $def['title'],
                    'post_name'    => $def['slug'],
                    'post_type'    => 'page',
                    'post_status'  => $source->post_status,
                    'post_content' => $content,
                    'post_author'  => $source->post_author,
                ), true);

                if (is_wp_error($post_id) || !$post_id) {
                    $summary['missing'][] = $def['source'];
                    continue;
                }

                $summary['created']++;

                // The template is what decides whether this renders as the
                // front page, the plan layout or an ordinary page, so it has to
                // come across with the content.
                $template = get_post_meta($source->ID, '_wp_page_template', true);
                if ($template) {
                    update_post_meta($post_id, '_wp_page_template', $template);
                }

                // Featured images are shared across languages here — the media
                // library is not duplicated per language — so the ID carries.
                $thumbnail = get_post_meta($source->ID, '_thumbnail_id', true);
                if ($thumbnail) {
                    update_post_meta($post_id, '_thumbnail_id', $thumbnail);
                }
            }

            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($post_id, 'de');
            }

            if (function_exists('pll_save_post_translations')) {
                $group['de'] = $post_id;

                // Cast the post IDs, and only the post IDs. A translation group
                // can carry a non-language key — Polylang Pro stores its sync
                // settings under 'sync', as an array — and array_map('intval')
                // over the whole group would flatten that to 0 and lose it.
                $payload = array();
                foreach ($group as $key => $value) {
                    $payload[$key] = is_scalar($value) ? (int) $value : $value;
                }

                pll_save_post_translations($payload);
                $summary['linked']++;
            }

            // The home page's copy is fields, not content. Done after the
            // language is set, so anything reading the page's language while
            // the fields are written gets the right answer.
            if (!empty($def['fields'])) {
                $summary['fields'] += iptv_de_fill_fields($post_id, $def['fields']);
            }
        }

        return $summary;
    }
}

/**
 * Run once per GERMAN_PAGES_BUILD, on the first request after deploy.
 *
 * The flag is written before the work rather than after, so two requests
 * arriving together cannot both start building. If a run does fail part way,
 * bumping GERMAN_PAGES_BUILD re-runs it — and because every page is matched
 * through its translation group first, the retry repairs instead of
 * duplicating.
 */
add_action('init', function () {
    if ((int) get_option('germanpages_built') === GERMAN_PAGES_BUILD) {
        return;
    }

    // Polylang has to be up, and German has to exist: without both, every page
    // would be created with no language and the groups could not be linked.
    if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
        return;
    }

    if (!function_exists('pll_languages_list')
        || !in_array('de', (array) pll_languages_list(array('fields' => 'slug')), true)) {
        return;
    }

    update_option('germanpages_built', GERMAN_PAGES_BUILD, false);

    // Prefixed so wp_get_plugin_settings can read it back — see the note in
    // inc/add-german-language.php about which options the connector can see.
    update_option('germanpages_report', iptv_de_build_pages(), false);

    // LiteSpeed caches these pages the first time they are hit, so the new
    // language would otherwise be invisible until the cache expired on its own.
    do_action('litespeed_purge_all');
}, 25);
