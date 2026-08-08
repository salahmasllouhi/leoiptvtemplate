<?php
/**
 * One-shot scrub of third-party broadcaster and competition names.
 *
 * Most of the affected copy was rewritten directly through the editor. What is
 * left are the places where the branded phrase is a few words inside 20KB of
 * inlined CSS, an escaped code sample, or one of ~200 ACF meta rows per home
 * page. Rewriting those by hand risks corrupting the surrounding markup for the
 * sake of one sentence; an exact str_replace on the server does the same job
 * without touching anything else.
 *
 * Runs once, guarded by an option, then can be deleted along with its require
 * in functions.php. See inc/sport-retire.php for the same pattern.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * post_content replacements, applied to every page, post and FAQ entry.
 *
 * Every key is a distinctive multi-word token. Bare league acronyms are
 * deliberately absent here: a substring replace on "NFL" or "F1" across all
 * post bodies would fire inside unrelated words. The pages that needed those
 * were rewritten by hand instead.
 */
function iptv_trademark_content_map()
{
    return array(
        // FAQ page — the one branded sentence in an otherwise static document.
        'major leagues including NFL, NBA, Premier League, La Liga, Champions League, UFC, boxing, cricket, and many more'
            => 'major competitions across football, basketball, American football, combat sports, boxing, cricket, and many more',

        // M3U converter pages — sample playlist rows, English and Swedish.
        'ESPN HD'  => 'Sports Channel HD',
        'HBO Max'  => 'Movie Channel HD',
        'SVT1 HD'  => 'Nyhetskanalen HD',
        'TV4 Film' => 'Filmkanalen HD',

        // Blog post on Android TV setup.
        'SVT Play-liknande erbjudanden' => 'playtjänst-liknande erbjudanden',
    );
}

/**
 * Per-home-page meta replacements.
 *
 * Keyed by post ID because the copy is translated: the same field holds a
 * different sentence on each of the six home pages, so a single global map
 * cannot express it. Every meta row on the page is swept, which picks up
 * repeater rows (reviews_list_1_review_text and friends) without this file
 * needing to know ACF's flattened key naming.
 *
 * 6 = en, 419 = sv, 3179 = no, 3180 = dk, 3181 = fi, 3182 = is.
 */
function iptv_trademark_meta_map()
{
    // Shared across every language: the comparison table's competitor rows.
    $competitors = array(
        'Netflix'  => 'Streaming service 1',
        'Disney+'  => 'Streaming service 2',
        'HBO Max'  => 'Streaming service 3',
    );

    $map = array(
        6 => array(
            'NBA, UFC, Boxing... - All Included' => 'Basketball, MMA, boxing... - All Included',
            'UFC, boxing and wrestling PPV events included at no extra cost.' => 'Combat sports PPV events included at no extra cost.',
            'EPL, La Liga, Champions League, NFL, NBA and F1 — every major league, live.' => 'Top-flight football, European club nights, American football, basketball and motorsport — every major league, live.',
            'Premier League, Champions League, World Cup' => 'League, cup and international football',
            'Every Premier League game, Champions League, NBA — all in HD.' => 'Every big match, European club nights, basketball — all in HD.',
        ),
        419 => array(
            'UFC, NBA, Boxning... - Allt ingår' => 'Basket, MMA, boxning... - Allt ingår',
            'UFC, boxning och wrestling – alla PPV-evenemang ingår utan extra kostnad.' => 'Kampsport – alla PPV-evenemang ingår utan extra kostnad.',
            'Premier League, La Liga, Champions League, NFL, NBA och F1 – alla stora ligor, live.' => 'Europeisk toppfotboll, klubbturneringar, amerikansk fotboll, basket och motorsport – alla stora ligor, live.',
            'Premier League, Champions League, World Cup' => 'Liga-, cup- och landslagsfotboll',
            'Varje match i Premier League, Champions League, NBA — allt i HD.' => 'Varje stormatch, europeiska klubbkvällar, basket — allt i HD.',
        ),
        3179 => array(
            'NBA, UFC, boksing ... - alt inkludert' => 'Basketball, MMA, boksing ... - alt inkludert',
            'UFC, boksing og wrestling PPV-arrangementer inkludert uten ekstra kostnad.' => 'Kampsport-PPV-arrangementer inkludert uten ekstra kostnad.',
            'EPL, La Liga, Champions League, NFL, NBA og F1 — hver eneste store liga, direkte.' => 'Europeisk toppfotball, klubbturneringer, amerikansk fotball, basketball og motorsport — hver eneste store liga, direkte.',
            'Premier League, Champions League, World Cup' => 'Liga-, cup- og landslagsfotball',
            'Hver Premier League-kamp, Champions League, NBA – alt i HD.' => 'Hver storkamp, europeiske klubbkvelder, basketball – alt i HD.',
        ),
        3180 => array(
            'NBA, UFC, boksning... – alt inkluderet' => 'Basketball, MMA, boksning... – alt inkluderet',
            'UFC, boksning og wrestling PPV-begivenheder inkluderet uden ekstra omkostninger.' => 'Kampsport-PPV-begivenheder inkluderet uden ekstra omkostninger.',
            'EPL, La Liga, Champions League, NFL, NBA og F1 — alle store ligaer, live.' => 'Europæisk topfodbold, klubturneringer, amerikansk fodbold, basketball og motorsport — alle store ligaer, live.',
            'Premier League, Champions League, World Cup' => 'Liga-, pokal- og landsholdsfodbold',
            'Hver Premier League-kamp, Champions League, NBA – alt i HD.' => 'Hver storkamp, europæiske klubaftener, basketball – alt i HD.',
        ),
        3181 => array(
            'NBA, UFC, Nyrkkeily... – Kaikki sisältyy' => 'Koripallo, MMA, nyrkkeily... – Kaikki sisältyy',
            'UFC:n, nyrkkeilyn ja painin PPV-tapahtumat ilman lisäkustannuksia.' => 'Kamppailulajien PPV-tapahtumat ilman lisäkustannuksia.',
            'Valioliiga, La Liga, Mestarien liiga, NFL, NBA ja F1 — kaikki suuret sarjat livenä.' => 'Euroopan huippujalkapallo, seurajoukkueturnaukset, amerikkalainen jalkapallo, koripallo ja moottoriurheilu — kaikki suuret sarjat livenä.',
            'Kaikki F1-osakilpailut' => 'Kaikki kauden osakilpailut',
            'Jokainen Valioliigan ottelu, Mestarien liiga, NBA – kaikki HD-laadulla.' => 'Jokainen huippuottelu, eurooppalaiset seuraillat, koripallo – kaikki HD-laadulla.',
        ),
        3182 => array(
            'NBA, UFC, hnefaleikar... – allt innifalið' => 'Körfubolti, MMA, hnefaleikar... – allt innifalið',
            'UFC, hnefaleikar og glímu PPV viðburðir innifaldir án aukakostnaðar.' => 'Bardagaíþrótta-PPV-viðburðir innifaldir án aukakostnaðar.',
            'EPL, La Liga, Champions League, NFL, NBA og F1 — allar helstu deildir, í beinni.' => 'Evrópskur toppfótbolti, félagsliðakeppnir, ruðningur, körfubolti og akstursíþróttir — allar helstu deildir, í beinni.',
            'Premier League, Champions League, HM' => 'Deildar-, bikar- og landsliðsfótbolti',
            'Allir Premier League leikir, Meistaradeildin, NBA — allt í HD.' => 'Allir stórleikir, evrópsk félagsliðakvöld, körfubolti — allt í HD.',
        ),
    );

    // The sport tile names are the same acronyms on every page; only the
    // replacement is localised.
    $tiles = array(
        6    => array('NBA' => 'Basketball',  'MLB' => 'Baseball',    'NFL' => 'American football',        'F1' => 'Motorsport',        'UFC/Boxing' => 'Combat sports'),
        419  => array('NBA' => 'Basket',      'MLB' => 'Baseboll',    'NFL' => 'Amerikansk fotboll',       'F1' => 'Motorsport',        'UFC/Boxing' => 'Kampsport'),
        3179 => array('NBA' => 'Basketball',  'MLB' => 'Baseball',    'NFL' => 'Amerikansk fotball',       'F1' => 'Motorsport',        'UFC/Boxing' => 'Kampsport'),
        3180 => array('NBA' => 'Basketball',  'MLB' => 'Baseball',    'NFL' => 'Amerikansk fodbold',       'F1' => 'Motorsport',        'UFC/Boxing' => 'Kampsport'),
        3181 => array('NBA' => 'Koripallo',   'MLB' => 'Baseball',    'NFL' => 'Amerikkalainen jalkapallo', 'F1' => 'Moottoriurheilu',  'UFC/Boxing' => 'Kamppailulajit'),
        3182 => array('NBA' => 'Körfubolti',  'MLB' => 'Hafnabolti',  'NFL' => 'Ruðningur',                'F1' => 'Akstursíþróttir',   'UFC/Boxing' => 'Bardagaíþróttir'),
    );

    foreach ($map as $id => $_) {
        $map[$id] = array_merge($map[$id], $competitors);
    }

    return array($map, $tiles);
}

/**
 * Apply both maps.
 */
function iptv_run_trademark_scrub()
{
    if (get_option('iptv_trademark_scrubbed') === '1') {
        return;
    }

    $privileged = is_admin()
        || wp_doing_cron()
        || (defined('REST_REQUEST') && REST_REQUEST);

    if (!$privileged) {
        return;
    }

    $log = array('content' => array(), 'meta' => array());

    // --- post_content -----------------------------------------------------
    $content_map = iptv_trademark_content_map();

    // 'lang' => '' disables Polylang's language filter so translations in every
    // language are swept, not just those of the request's current language.
    $ids = get_posts(array(
        'post_type'        => array('page', 'post', 'faq'),
        'post_status'      => 'any',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'lang'             => '',
        'suppress_filters' => false,
    ));

    foreach ($ids as $id) {
        $before = get_post_field('post_content', $id);
        $after  = strtr($before, $content_map);

        if ($after !== $before) {
            wp_update_post(array('ID' => $id, 'post_content' => $after));
            $log['content'][$id] = get_the_title($id);
        }
    }

    // --- home page ACF meta ----------------------------------------------
    list($meta_map, $tile_map) = iptv_trademark_meta_map();

    foreach ($meta_map as $page_id => $phrases) {
        $rows = get_post_meta($page_id);

        if (empty($rows)) {
            continue;
        }

        $tiles = isset($tile_map[$page_id]) ? $tile_map[$page_id] : array();

        foreach ($rows as $key => $values) {
            // ACF's `_fieldname => field_key` reference rows carry no copy.
            if (strpos($key, '_') === 0) {
                continue;
            }

            $before = isset($values[0]) ? $values[0] : '';

            if (!is_string($before) || $before === '') {
                continue;
            }

            // Whole-value match for the bare acronyms: applying those as a
            // substring would rewrite them inside longer sentences too, where
            // the surrounding grammar has already been handled above.
            if (isset($tiles[$before])) {
                $after = $tiles[$before];
            } else {
                $after = strtr($before, $phrases);
            }

            if ($after !== $before) {
                update_post_meta($page_id, $key, $after);
                $log['meta'][$page_id][$key] = $after;
            }
        }
    }

    // Kept so the run can be inspected afterwards rather than taken on trust.
    update_option('iptv_trademark_scrub_log', $log, false);
    update_option('iptv_trademark_scrubbed', '1', true);
}
add_action('init', 'iptv_run_trademark_scrub', 51);
