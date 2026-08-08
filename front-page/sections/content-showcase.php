<?php
/**
 * Section: Content showcase (Design v2)
 * Two split panels — live channels and the VOD library.
 */

$showcase_cta_field  = function_exists('get_field') ? get_field('showcase_cta', get_option('page_on_front')) : null;
$showcase_cta_url    = (!empty($showcase_cta_field['url'])) ? $showcase_cta_field['url'] : '#pricing';
$showcase_cta_label  = (!empty($showcase_cta_field['title'])) ? $showcase_cta_field['title'] : iptv_text('showcase_cta', 'Explore the full channel lineup');
$showcase_cta_target = (!empty($showcase_cta_field['target'])) ? ' target="' . esc_attr($showcase_cta_field['target']) . '"' : '';

// Genre labels rather than broadcaster names: the row is there to show what a
// channel list looks like, and naming real networks put third-party trademarks
// on the front page for no gain. Row 2 is the highlighted one, as in the design.
$channel_rows = [
    1 => ['101', 'News & Current Affairs HD'],
    2 => ['102', 'Entertainment HD'],
    3 => ['103', 'Sports 1 HD'],
    4 => ['104', 'Movies HD'],
    5 => ['105', 'Family & Kids HD'],
    6 => ['106', 'Documentary HD'],
];
$highlight_row = 2;
?>

<!-- Explore channels -->
<section class="dv2-split dv2-split--first">
    <div class="dv2-split-copy">
        <h3 class="dv2-split-title dv2-split-title--lg">
            <?php echo esc_html(iptv_text('showcase_title', 'Explore')); ?>
            <em><?php echo esc_html(iptv_text('showcase_title_span', '40,000+')); ?></em>
            <?php echo esc_html(iptv_text('showcase_title_3', 'live TV channels')); ?>
        </h3>
        <p>
            <?php echo esc_html(iptv_text('showcase_subtitle', 'From local Nordic news to global sports, entertainment, kids, and international channels — 198 countries covered.')); ?>
        </p>
        <a href="<?php echo esc_url($showcase_cta_url); ?>" class="dv2-btn dv2-btn-white"<?php echo $showcase_cta_target; ?>>
            <?php echo esc_html($showcase_cta_label); ?>
            <span class="dv2-btn-arrow" aria-hidden="true">→</span>
        </a>
    </div>

    <div class="dv2-split-aside">
        <div class="dv2-channel-list">
            <?php foreach ($channel_rows as $n => $row) : ?>
                <div class="dv2-channel-row<?php echo $n === $highlight_row ? ' dv2-channel-row--active' : ''; ?>">
                    <span class="dv2-channel-num"><?php echo esc_html(iptv_text("showcase_channel_{$n}_num", $row[0])); ?></span>
                    <span class="dv2-channel-name"><?php echo esc_html(iptv_text("showcase_channel_{$n}_name", $row[1])); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="dv2-channel-more">
                <?php echo esc_html(iptv_text('showcase_channel_more', '+40,000 live TV channels from 198 countries')); ?>
            </div>
        </div>
    </div>
</section>

<!-- Movies & series -->
<?php
$vod_cta_field  = function_exists('get_field') ? get_field('vod_cta', get_option('page_on_front')) : null;
$vod_cta_url    = (!empty($vod_cta_field['url'])) ? $vod_cta_field['url'] : '#pricing';
$vod_cta_label  = (!empty($vod_cta_field['title'])) ? $vod_cta_field['title'] : iptv_text('vod_cta', 'Start watching today');
$vod_cta_target = (!empty($vod_cta_field['target'])) ? ' target="' . esc_attr($vod_cta_field['target']) . '"' : '';
?>
<section class="dv2-split">
    <div class="dv2-split-copy">
        <h3 class="dv2-split-title">
            <?php echo esc_html(iptv_text('vod_title', 'Indulge in')); ?>
            <em><?php echo esc_html(iptv_text('vod_title_span', '200,000+')); ?></em>
            <?php echo esc_html(iptv_text('vod_title_3', 'movies and series')); ?>
        </h3>
        <p>
            <?php echo esc_html(iptv_text('vod_subtitle', 'All genres and languages, on demand whenever it suits you. Full electronic program guide with daily content updates and multi-language subtitles.')); ?>
        </p>
        <a href="<?php echo esc_url($vod_cta_url); ?>" class="dv2-btn dv2-btn-white"<?php echo $vod_cta_target; ?>>
            <?php echo esc_html($vod_cta_label); ?>
            <span class="dv2-btn-arrow" aria-hidden="true">→</span>
        </a>
    </div>

    <div class="dv2-split-aside dv2-split-aside--image">
        <?php // 528px in the split at full width, full-bleed once it stacks at 1024px. ?>
        <img src="https://nordictv.io/wp-content/uploads/2026/08/vodnordic.webp"
             srcset="https://nordictv.io/wp-content/uploads/2026/08/vodnordic-300x224.webp 300w,
                     https://nordictv.io/wp-content/uploads/2026/08/vodnordic-768x573.webp 768w,
                     https://nordictv.io/wp-content/uploads/2026/08/vodnordic-1024x765.webp 1024w,
                     https://nordictv.io/wp-content/uploads/2026/08/vodnordic.webp 1200w"
             sizes="(max-width: 1024px) calc(100vw - 152px), (max-width: 1280px) calc((100vw - 224px) / 2), 528px"
             width="1200" height="896"
             alt="<?php echo esc_attr(iptv_text('vod_image_alt', 'A selection of movies and series available on NordicTV')); ?>"
             loading="lazy" decoding="async">
    </div>
</section>
