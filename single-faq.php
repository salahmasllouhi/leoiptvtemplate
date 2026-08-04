<?php
/**
 * Single FAQ Template
 * Displays individual FAQ custom post type entries.
 *
 * Structure:
 *   1. h1 — post title
 *   2. First <p> from content as highlighted answer capsule
 *   3. Remaining content blocks (h2 + p Q&A pairs)
 *   4. CTA section — "Watch Nordic TV from anywhere"
 */

get_header();
?>

<?php
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
echo '<\/style>';
?>

<style>
/* ── FAQ Single Page ─────────────────────────────────────────────── */
.faq-single {
    padding: 8rem 0 var(--space-lg, 3rem);
    background: var(--bg-page, #F2F8FE);
}

/* Push content below the absolute-positioned floating header */
body.single-faq .faq-single {
    padding-top: calc(80px + 3rem);
}

.faq-single__container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 var(--space-md, 1.5rem);
}

.faq-single__title {
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 700;
    color: var(--text-primary, #0F2847);
    letter-spacing: -0.02em;
    line-height: 1.25;
    margin-bottom: var(--space-lg, 2.5rem);
    text-align: center;
}

/* First paragraph — highlighted answer capsule */
.faq-single__answer-capsule {
    background: var(--bg-card, #FFFFFF);
    border-left: 4px solid var(--color-teal, #1089F2);
    border-radius: var(--radius-lg, 20px);
    padding: var(--space-md, 1.5rem) var(--space-lg, 2rem);
    margin-bottom: var(--space-xl, 3rem);
    box-shadow: var(--shadow-glow, 0 8px 40px rgba(16, 137, 242, 0.25));
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-primary, #0F2847);
}

.faq-single__answer-capsule p {
    margin: 0;
    font-size: inherit;
    line-height: inherit;
    color: inherit;
}

/* Remaining Q&A blocks */
.faq-single__body h2 {
    font-size: clamp(1.05rem, 2.5vw, 1.25rem);
    font-weight: 700;
    color: #0F2847;
    margin: 2.5rem 0 0.6rem;
    padding: 0.85rem 1.25rem 0.85rem 1.25rem;
    background: #ffffff;
    border: 1px solid #e8e8f0;
    border-left: 4px solid #1089F2;
    border-radius: 0 12px 12px 0;
    box-shadow: 0 2px 8px rgba(15,40,71,0.06);
    display: block;
}

.faq-single__body h2:first-child {
    margin-top: 0;
}

.faq-single__body p {
    color: var(--text-secondary, #4A6282);
    line-height: 1.75;
    margin-bottom: var(--space-sm, 1rem);
    padding: 0 0.25rem;
}

.faq-single__body a {
    color: var(--color-teal, #1089F2);
    text-decoration: underline;
}

/* ── Related FAQs ────────────────────────────────────────────────── */
.faq-related {
    padding: 4rem 0 5rem;
    background: var(--bg-section, #E2EFFD);
}

.faq-related__container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 var(--space-md, 1.5rem);
}

.faq-related__heading {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text-primary, #0F2847);
    margin-bottom: 1.5rem;
}

.faq-related__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 600px) {
    .faq-related__grid {
        grid-template-columns: 1fr;
    }
}

.faq-related__card {
    display: block;
    background: var(--bg-card, #FFFFFF);
    border-radius: var(--radius-lg, 20px);
    padding: 1.25rem 1.5rem;
    text-decoration: none;
    box-shadow: var(--shadow-sm, 0 2px 8px rgba(15,40,71,0.06));
    border: 1px solid transparent;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
}

.faq-related__card:hover {
    border-color: var(--color-teal, #1089F2);
    box-shadow: var(--shadow-glow, 0 8px 40px rgba(16,137,242,0.25));
    transform: translateY(-2px);
}

.faq-related__card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary, #0F2847);
    line-height: 1.4;
    margin-bottom: 0.4rem;
}

.faq-related__card-arrow {
    font-size: 0.8rem;
    color: var(--color-teal, #1089F2);
    font-weight: 600;
}
</style>

<!-- Universal Header -->
<?php include get_template_directory() . '/front-page/sections/header.php'; ?>

<?php while (have_posts()) : the_post(); ?>

    <section class="faq-single">
        <div class="faq-single__container">

            <!-- 1. Post title -->
            <h1 class="faq-single__title"><?php the_title(); ?></h1>

            <?php
            // Split the post content: extract the first <p> for the capsule,
            // leave the rest (all h2+p Q&A blocks) for the body div.
            $raw    = get_the_content();
            $blocks = parse_blocks($raw);

            $first_p_html   = '';
            $remaining_html = '';
            $found_first    = false;

            foreach ($blocks as $block) {
                // Skip null/whitespace-only blocks Gutenberg inserts between blocks
                if (empty($block['blockName']) && empty(trim($block['innerHTML'] ?? ''))) {
                    continue;
                }

                $rendered = render_block($block);

                if (!$found_first && $block['blockName'] === 'core/paragraph') {
                    $first_p_html = $rendered;
                    $found_first  = true;
                    continue;
                }

                $remaining_html .= $rendered;
            }

            // Fallback: classic editor or if block parsing missed — split on first <p>
            if (empty($first_p_html)) {
                $content = apply_filters('the_content', $raw);
                if (preg_match('/(<p[^>]*>.*?<\/p>)/is', $content, $m)) {
                    $first_p_html   = $m[1];
                    $remaining_html = substr($content, strpos($content, $m[0]) + strlen($m[0]));
                } else {
                    $remaining_html = $content;
                }
            }
            ?>

            <?php if ($first_p_html) : ?>
                <!-- 2. First paragraph — answer capsule -->
                <div class="faq-single__answer-capsule">
                    <?php echo wp_kses_post($first_p_html); ?>
                </div>
            <?php endif; ?>

            <?php if ($remaining_html) : ?>
                <!-- 3. Remaining h2 + p Q&A blocks -->
                <div class="faq-single__body">
                    <?php echo wp_kses_post($remaining_html); ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- 4. CTA Section -->
    <?php
    $lang    = function_exists('pll_current_language') ? pll_current_language() : 'en';
    $cta_url = ($lang === 'en')
        ? 'https://nordictv.io/#pricing'
        : 'https://nordictv.io/' . $lang . '/home/#pricing';
    ?>
    <section class="cta">
        <div class="cta-box">
            <div class="cta-content">
                <h2><?php echo esc_html(iptv_text('faq_cta_title', 'Watch Nordic TV from anywhere')); ?></h2>
                <p><?php echo esc_html(iptv_text('faq_cta_subtitle', 'Get instant access to live channels, movies, and series — on any device, any time.')); ?></p>
                <a href="<?php echo esc_url($cta_url); ?>" class="btn btn-primary">
                    <?php echo esc_html(iptv_text('cta_btn_text', 'Start Streaming Now')); ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
                <div class="cta-features">
                    <div class="cta-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <?php echo esc_html(iptv_text('cta_f1', '256-bit SSL Encryption')); ?>
                    </div>
                    <div class="cta-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <?php echo esc_html(iptv_text('cta_f2', 'Instant Activation')); ?>
                    </div>
                    <div class="cta-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        <?php echo esc_html(iptv_text('cta_f3', '24/7 Customer Support')); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Related FAQs -->
    <?php
    $related = new WP_Query([
        'post_type'      => 'faq',
        'posts_per_page' => 6,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'rand',
    ]);
    if ($related->have_posts()) :
    ?>
    <section class="faq-related">
        <div class="faq-related__container">
            <h2 class="faq-related__heading">Related questions</h2>
            <div class="faq-related__grid">
                <?php while ($related->have_posts()) : $related->the_post(); ?>
                    <a href="<?php the_permalink(); ?>" class="faq-related__card">
                        <div class="faq-related__card-title"><?php the_title(); ?></div>
                        <div class="faq-related__card-arrow">Read answer →</div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php endwhile; ?>

<!-- Footer -->
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
echo '<\/script>';
?>

<?php get_footer(); ?>
