<?php
/**
 * FAQ Archive — the hub for the `faq` post type
 *
 * Search Console had 81 of the 100 FAQ posts as "Discovered - currently not
 * indexed": Google knew the URLs from faq-sitemap.xml and had never spent a
 * crawl on one of them. The cause was that nothing linked to them. The post
 * type had no archive and `show_in_nav_menus => false`, and the only internal
 * link to a FAQ post came from the "Related questions" block on another FAQ
 * post — a closed loop with no way in. A sitemap entry is an invitation, not a
 * reason; the internal link is the reason.
 *
 * This page is that reason. It lists every question, so each one sits two
 * clicks from the home page once the footer links here.
 *
 * Structure mirrors single-faq.php: shared front-page CSS inlined, universal
 * header, content, shared footer.
 *
 * @package Nordic_IPTV
 */

get_header();

$shared_css = [
    'variables.css',
    'redesign-theme.css',
    'base.css',
    'header.css',
    'footer.css',
    'cta.css',
    'design-v2.css',
    'design-v2-sections.css',
];

echo '<style>';
foreach ($shared_css as $file) {
    $path = get_template_directory() . '/front-page/css/' . $file;
    if (file_exists($path)) {
        echo file_get_contents($path);
    }
}
echo '</style>';
?>

<style>
/* ── FAQ Archive ─────────────────────────────────────────────────── */
.faq-archive {
    padding: calc(80px + 3rem) 0 var(--space-lg, 3rem);
    background: var(--bg-page, #F2F8FE);
}

.faq-archive__container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 var(--space-md, 1.5rem);
}

.faq-archive__title {
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 700;
    color: var(--text-primary, #0F2847);
    letter-spacing: -0.02em;
    line-height: 1.25;
    text-align: center;
    margin-bottom: var(--space-sm, 1rem);
}

.faq-archive__intro {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-secondary, #4A6282);
    text-align: center;
    max-width: 640px;
    margin: 0 auto var(--space-xl, 3rem);
}

.faq-archive__group {
    margin-bottom: var(--space-xl, 3rem);
}

.faq-archive__group-heading {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text-primary, #0F2847);
    margin: 0 0 1.25rem;
    padding-bottom: 0.6rem;
    border-bottom: 2px solid var(--color-teal, #1089F2);
}

.faq-archive__list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

@media (max-width: 700px) {
    .faq-archive__list {
        grid-template-columns: 1fr;
    }
}

.faq-archive__item {
    margin: 0;
}

.faq-archive__link {
    display: block;
    height: 100%;
    background: var(--bg-card, #FFFFFF);
    border: 1px solid transparent;
    border-radius: var(--radius-lg, 20px);
    padding: 1rem 1.25rem;
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.45;
    color: var(--text-primary, #0F2847);
    text-decoration: none;
    box-shadow: var(--shadow-sm, 0 2px 8px rgba(15, 40, 71, 0.06));
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
}

.faq-archive__link:hover,
.faq-archive__link:focus-visible {
    border-color: var(--color-teal, #1089F2);
    box-shadow: var(--shadow-glow, 0 8px 40px rgba(16, 137, 242, 0.25));
    transform: translateY(-2px);
}
</style>

<?php include get_template_directory() . '/front-page/sections/header.php'; ?>

<section class="faq-archive">
    <div class="faq-archive__container">

        <h1 class="faq-archive__title">
            <?php echo esc_html(iptv_text('faq_hub_title', 'IPTV questions and answers')); ?>
        </h1>

        <p class="faq-archive__intro">
            <?php echo esc_html(iptv_text('faq_hub_intro', 'Everything people ask us about IPTV — what it costs, which devices it runs on, how to set it up, and where the law stands.')); ?>
        </p>

        <?php
        // Every question on one page. The archive is paginated by default,
        // which would leave later pages as poorly-linked as the posts were,
        // so the query asks for all of them. The set is ~100 titles and the
        // page is cached, so the cost is one query on a cache miss.
        $faq_query = new WP_Query([
            'post_type'              => 'faq',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
        ]);

        // Which section a question belongs in, matched against its slug. The
        // slug rather than the title because it is already accent-folded —
        // "Är det lagligt…" is ar-det-lagligt-… — so the needles stay plain
        // ASCII and no mb_* handling is needed.
        //
        // This is a heuristic tuned to the Swedish questions that exist today,
        // not a taxonomy. It is deliberately cheap: tagging 100 posts by hand
        // would order them no better, and anything it fails to place lands in
        // the last group rather than disappearing. Order matters — the first
        // group whose needle appears wins.
        $faq_groups = apply_filters('nordictv_faq_hub_groups', [
            'legal' => [
                'label'   => iptv_text('faq_hub_group_legal', 'Legality and safety'),
                'needles' => ['laglig', 'olaglig', 'straff', 'domd', 'polis', 'sparas', 'spara-iptv',
                              'blockerar', 'blockerat', 'stangts', 'stangs', 'vpn', 'sakerhet'],
            ],
            'price' => [
                'label'   => iptv_text('faq_hub_group_price', 'Price and payment'),
                'needles' => ['kostar', 'kostnad', 'pris', 'betal', 'rabatt', 'gratis', 'provperiod',
                              'erbjudande', 'paket', 'abonnemang', 'prenumeration', 'vart-att-skaffa'],
            ],
            'devices' => [
                'label'   => iptv_text('faq_hub_group_devices', 'Devices and apps'),
                'needles' => ['enhet', 'smart-tv', 'android', 'apple', 'fire-tv', 'box', 'mottagare',
                              'chromecast', 'mobil', 'appar', 'app-', '-app'],
            ],
            'setup' => [
                'label'   => iptv_text('faq_hub_group_setup', 'Setup and troubleshooting'),
                'needles' => ['installer', 'installation', 'installningar', 'konfigurera', 'felsok',
                              'buffring', 'internethastighet', 'uppkoppling', 'internetabonnemang',
                              'spela-in', 'spelar-in', 'kanallistan', 'anvandarprofiler', 'barnlas',
                              'optimera', 'borjar', 'igang', 'avbryt', 'fornya', 'byta', 'kundsupport'],
            ],
            'choosing' => [
                'label'   => iptv_text('faq_hub_group_choosing', 'Choosing a provider'),
                'needles' => ['leverantor', 'tjanst', 'kanaler', 'recension', 'jamfor', 'jamforelse',
                              'tillgangliga', 'kvalitet', 'undertext', 'valjer', 'valja', 'val-av',
                              'palitlig', 'nordic'],
            ],
            'other' => [
                'label'   => iptv_text('faq_hub_group_other', 'More questions'),
                'needles' => [],
            ],
        ]);

        $sorted = array_fill_keys(array_keys($faq_groups), []);

        while ($faq_query->have_posts()) {
            $faq_query->the_post();

            $slug  = get_post_field('post_name', get_the_ID());
            $bin   = 'other';

            foreach ($faq_groups as $key => $group) {
                foreach ($group['needles'] as $needle) {
                    if (strpos($slug, $needle) !== false) {
                        $bin = $key;
                        break 2;
                    }
                }
            }

            $sorted[$bin][] = [
                'url'   => get_permalink(),
                'title' => get_the_title(),
            ];
        }

        wp_reset_postdata();
        ?>

        <?php foreach ($faq_groups as $key => $group) : ?>
            <?php if (empty($sorted[$key])) { continue; } ?>
            <div class="faq-archive__group">
                <h2 class="faq-archive__group-heading"><?php echo esc_html($group['label']); ?></h2>
                <ul class="faq-archive__list">
                    <?php foreach ($sorted[$key] as $faq_item) : ?>
                        <li class="faq-archive__item">
                            <a class="faq-archive__link" href="<?php echo esc_url($faq_item['url']); ?>">
                                <?php echo esc_html($faq_item['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

    </div>
</section>

<?php include get_template_directory() . '/front-page/sections/footer.php'; ?>

<?php
$js_files = ['header.js', 'currency.js'];
echo '<script>';
foreach ($js_files as $file) {
    $path = get_template_directory() . '/front-page/js/' . $file;
    if (file_exists($path)) {
        echo file_get_contents($path);
    }
}
echo '</script>';
