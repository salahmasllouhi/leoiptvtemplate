<?php
/**
 * Section: Features band (Design v2)
 * Eight capability cards: icon + title + one-line description.
 * Each card carries the animated conic-gradient border (see .dv2-feature-card
 * in design-v2-sections.css).
 */

$s = 'width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"';

$feature_cards = [
    1 => [
        'title' => '40,000+ Live Channels',
        'desc'  => 'Sports, movies, news and kids channels from every corner of the globe — all in one place.',
        'icon'  => '<svg ' . $s . '><rect x="2" y="4" width="20" height="13" rx="2"></rect><path d="M8 21h8"></path></svg>',
    ],
    2 => [
        'title' => '200,000+ Movies & Series',
        'desc'  => 'A huge on-demand library of blockbusters and box sets, refreshed every single day.',
        'icon'  => '<svg ' . $s . '><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 9h18M8 5v14M16 5v14" stroke-width="1.3"></path></svg>',
    ],
    3 => [
        'title' => 'Multi-User Access',
        'desc'  => 'Watch on several screens at once and share one subscription with the whole household.',
        'icon'  => '<svg ' . $s . '><circle cx="9" cy="8" r="3.2"></circle><path d="M2.5 20a6.5 6.5 0 0 1 13 0"></path><path d="M17 6.5a3 3 0 0 1 0 5.5M18 20a6 6 0 0 0-3-5"></path></svg>',
    ],
    4 => [
        'title' => 'HD, UHD & 4K Quality',
        'desc'  => 'Razor-sharp picture on every channel, from crisp HD all the way up to true 4K Ultra HD.',
        'icon'  => '<span class="dv2-chip-badge">4K</span>',
    ],
    5 => [
        'title' => 'Full TV Guide (EPG)',
        'desc'  => 'See what is on now and what is coming next with a built-in guide for every channel.',
        'icon'  => '<svg ' . $s . '><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 9h18M9 9v11" stroke-width="1.3"></path><path d="M12 13h6M12 16h4" stroke-width="1.3"></path></svg>',
    ],
    6 => [
        'title' => 'Daily Auto-Update',
        'desc'  => 'Channels and VOD refresh on their own every day — nothing to install, nothing to set up.',
        'icon'  => '<svg ' . $s . '><path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path><path d="M3 21v-5h5"></path></svg>',
    ],
    7 => [
        'title' => '7-Day Catch-Up TV',
        'desc'  => 'Missed the match? Rewind up to a week and watch it whenever it suits you.',
        'icon'  => '<svg ' . $s . '><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path><path d="M12 8v4.5l3 1.8" stroke-width="1.3"></path></svg>',
    ],
    8 => [
        'title' => 'Anti-Buffer™ 3.0',
        'desc'  => 'Smart stream stabilisation keeps playback silky smooth, even on average connections.',
        'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2 4 14h6l-1 8 9-12h-6z"></path></svg>',
    ],
];

$allowed_svg = [
    'svg'    => ['width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true],
    'rect'   => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true],
    'circle' => ['cx' => true, 'cy' => true, 'r' => true],
    'path'   => ['d' => true, 'stroke-width' => true],
    'span'   => ['class' => true],
];
?>
<section class="features dv2-section" id="features">
    <div class="features-inner">
        <div class="dv2-section-head">
            <h2><?php echo esc_html(iptv_text('features_title', 'Built for global viewers')); ?></h2>
            <p>
                <?php echo esc_html(iptv_text('features_subtitle', 'From Nordic public TV to Premier League, Bollywood to Hollywood — all sports, all genres, all countries in one affordable package')); ?>
            </p>
        </div>

        <div class="dv2-feature-grid">
            <?php foreach ($feature_cards as $n => $card) : ?>
                <div class="dv2-feature-card">
                    <div class="dv2-feature-card-head">
                        <span class="dv2-feature-card-icon" aria-hidden="true">
                            <?php echo wp_kses($card['icon'], $allowed_svg); ?>
                        </span>
                        <h3 class="dv2-feature-card-title">
                            <?php echo esc_html(iptv_text("feature_{$n}_title", $card['title'])); ?>
                        </h3>
                    </div>
                    <p class="dv2-feature-card-desc">
                        <?php echo esc_html(iptv_text("feature_{$n}_desc", $card['desc'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <?php // Hands off to the pricing configurator that follows this section. ?>
        <div class="dv2-features-cta">
            <a href="#pricing" class="dv2-btn dv2-btn-primary dv2-btn-lg">
                <?php echo esc_html(iptv_text('features_cta_label', 'See plans & pricing')); ?>
                <span class="dv2-btn-arrow" aria-hidden="true">→</span>
            </a>
            <p class="dv2-features-cta-note">
                <?php echo esc_html(iptv_text('features_cta_note', '14-day money-back guarantee · Instant activation · No auto-renew')); ?>
            </p>
        </div>
    </div>
</section>
