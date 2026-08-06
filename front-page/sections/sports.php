<?php
/**
 * Section: Sports (Design v2)
 * Split panel with a single artwork alongside the copy.
 */

$sports_cta_field  = function_exists('get_field') ? get_field('sports_cta', get_option('page_on_front')) : null;
$sports_cta_url    = (!empty($sports_cta_field['url'])) ? $sports_cta_field['url'] : '#pricing';
$sports_cta_label  = (!empty($sports_cta_field['title'])) ? $sports_cta_field['title'] : iptv_text('sports_cta', 'Watch live sport now');
$sports_cta_target = (!empty($sports_cta_field['target'])) ? ' target="' . esc_attr($sports_cta_field['target']) . '"' : '';

// The six-tile mosaic was replaced by a single artwork. The sport_N_name fields
// it used are still in the ACF group and still drive the sport landing pages.
?>
<section class="dv2-split">
    <div class="dv2-split-copy">
        <h3 class="dv2-split-title">
            <?php echo esc_html(iptv_text('sports_title', 'Every sport.')); ?>
            <em><?php echo esc_html(iptv_text('sports_title_span', 'Every match.')); ?></em>
        </h3>
        <p>
            <?php echo esc_html(iptv_text('sports_desc', 'Never miss a game again. Every major league, every tournament, every PPV event — NFL, NBA, Formula 1, football, boxing and more, in HD and 4K.')); ?>
        </p>
        <a href="<?php echo esc_url($sports_cta_url); ?>" class="dv2-btn dv2-btn-white"<?php echo $sports_cta_target; ?>>
            <?php echo esc_html($sports_cta_label); ?>
            <span class="dv2-btn-arrow" aria-hidden="true">→</span>
        </a>
    </div>

    <div class="dv2-sport-mosaic dv2-sport-mosaic--image">
        <?php // 528px in the split at full width, full-bleed once it stacks at 1024px. ?>
        <img src="https://nordictv.io/wp-content/uploads/2026/08/nordicsport.webp"
             srcset="https://nordictv.io/wp-content/uploads/2026/08/nordicsport-300x224.webp 300w,
                     https://nordictv.io/wp-content/uploads/2026/08/nordicsport-768x573.webp 768w,
                     https://nordictv.io/wp-content/uploads/2026/08/nordicsport-1024x765.webp 1024w,
                     https://nordictv.io/wp-content/uploads/2026/08/nordicsport.webp 1200w"
             sizes="(max-width: 1024px) calc(100vw - 152px), (max-width: 1280px) calc((100vw - 224px) / 2), 528px"
             width="1200" height="896"
             alt="<?php echo esc_attr(iptv_text('sports_image_alt', 'Live sport available on NordicTV')); ?>"
             loading="lazy" decoding="async">
    </div>
</section>
