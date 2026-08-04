<?php
/**
 * Section: Hero (Design v2 — NordicTV Blue & White)
 */

// Hero backdrop. This is deliberately a *new* key rather than the old
// `hero_image_url`: that one holds each language's foreground artwork, which
// carried baked-in text and was never built to sit behind copy. Reusing it
// would have pulled those per-language images in as backgrounds.
$hero_bg_url = iptv_text(
    'hero_background_url',
    'https://nordictv.io/wp-content/uploads/2026/08/hero-background-scaled.webp'
);

$primary_label   = iptv_text('hero_primary_cta_label', 'Get Access Now');
$primary_url     = iptv_text('hero_primary_cta_url', '#pricing');
$secondary_label = iptv_text('hero_secondary_cta_label', 'Pricing & Plans ↓');
$secondary_url   = iptv_text('hero_secondary_cta_url', '#pricing');
?>
<section class="dv2-hero dv2-hero--backdrop">
    <?php
    // The backdrop is its own layer rather than a background on the section so
    // the scrim (.dv2-hero-bg::after) can sit between the photo and the copy.
    // The section is full-bleed, so the shell moves to .dv2-hero-inner.
    ?>
    <div class="dv2-hero-bg" aria-hidden="true"
         style="background-image:url('<?php echo esc_url($hero_bg_url); ?>');"></div>

    <div class="dv2-hero-inner">
        <div class="dv2-hero-copy">
            <div class="dv2-trustpilot">
                <span class="dv2-trustpilot-name">★ Trustpilot</span>
                <span><?php echo esc_html(iptv_text('hero_trust_label', 'Excellent')); ?></span>
                <span class="dv2-trustpilot-stars" aria-hidden="true">
                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                </span>
                <span>
                    <strong><?php echo esc_html(iptv_text('hero_trust_score', '4.9')); ?></strong>
                    <?php echo esc_html(iptv_text('hero_trust_suffix', 'out of 5')); ?>
                </span>
            </div>

            <?php
            // Two lines, each a block so the break is deliberate rather than left to
            // wrapping. The second line is split so only its opening phrase
            // (hero_title_span) takes the blue accent.
            ?>
            <h1 class="dv2-hero-title">
                <span class="dv2-hero-line">
                    <?php echo esc_html(iptv_text('hero_title', 'Nordic IPTV Without Limits. Every Match. Every Channel.')); ?>
                </span>
                <span class="dv2-hero-line">
                    <span class="dv2-hero-accent"><?php echo esc_html(iptv_text('hero_title_span', 'One Subscription.')); ?></span>
                    <?php echo esc_html(iptv_text('hero_title_3', '$1,000 Saved. Zero Compromises.')); ?>
                </span>
            </h1>

            <p class="dv2-hero-sub">
                <?php echo wp_kses_post(iptv_text('hero_subtitle', 'Looking for Nordic IPTV? NordicTV offers 40,000+ live channels, 200,000+ movies & series, and every sport in 4K/8K — on any device, instantly.')); ?>
            </p>

            <div class="dv2-hero-actions">
                <a href="<?php echo esc_url($primary_url); ?>" class="dv2-btn dv2-btn-primary dv2-btn-lg">
                    ▶ <?php echo esc_html($primary_label); ?>
                </a>
                <a href="<?php echo esc_url($secondary_url); ?>" class="dv2-btn dv2-btn-lg dv2-hero-link">
                    <?php echo esc_html($secondary_label); ?>
                </a>
            </div>
        </div>
    </div>
</section>
