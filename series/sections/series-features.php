<?php
/**
 * Series Features Section — 6 Cards
 * Simple strings: Polylang String Translations via srs_str().
 * Dynamic: $series_name
 */

$feat_tag = srs_str('Why NordicTV');
$feat_title = srs_str('Watch %s with <span class="gradient-text">NordicTV</span>');
$feat_subtitle = srs_str('Everything you need for the ultimate %s binge experience');

$feat_1_title = srs_str('200K+ Movies & Shows');
$feat_1_desc = srs_str('Stream %s alongside 200,000+ movies and series. Every genre, every mood — all included.');
$feat_2_title = srs_str('All Seasons & Episodes');
$feat_2_desc = srs_str('Watch every season of %s from pilot to finale. No missing episodes, no gaps, no waiting.');
$feat_3_title = srs_str('40,000+ Live Channels');
$feat_3_desc = srs_str('Beyond %s, enjoy 40,000+ live channels from 198 countries. Sports, news, entertainment — all included.');
$feat_4_title = srs_str('$0 PPV Events');
$feat_4_desc = srs_str('All combat-sports and Pay-Per-View events at zero extra cost alongside your favorite series like %s.');
$feat_5_title = srs_str('Crystal Clear 4K');
$feat_5_desc = srs_str('Watch %s in crystal-clear 4K resolution with Dolby audio. No buffering, no lag, no compromise.');
$feat_6_title = srs_str('24/7 Support');
$feat_6_desc = srs_str('Real humans, real help via live chat or WhatsApp anytime. We make sure %s is always available for you.');

$sn = esc_html($series_name);
?>

<section class="features" id="features">
    <div class="section-header">
        <div class="section-tag">
            <?php echo esc_html($feat_tag); ?>
        </div>
        <h2 class="section-title">
            <?php echo wp_kses_post(sprintf($feat_title, $sn)); ?>
        </h2>
        <p class="section-subtitle">
            <?php echo wp_kses_post(sprintf($feat_subtitle, $sn)); ?>
        </p>
    </div>

    <div class="features-grid">

        <!-- Feature 1: VOD Library -->
        <div class="feature-card animate-on-scroll">
            <div class="feature-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="2" width="20" height="20" rx="2.18" />
                    <line x1="7" y1="2" x2="7" y2="22" />
                    <line x1="17" y1="2" x2="17" y2="22" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                </svg>
            </div>
            <h3 class="feature-title">
                <?php echo esc_html($feat_1_title); ?>
            </h3>
            <p class="feature-desc">
                <?php echo wp_kses_post(sprintf($feat_1_desc, $sn)); ?>
            </p>
        </div>

        <!-- Feature 2: All Seasons -->
        <div class="feature-card animate-on-scroll">
            <div class="feature-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
            </div>
            <h3 class="feature-title">
                <?php echo esc_html($feat_2_title); ?>
            </h3>
            <p class="feature-desc">
                <?php echo wp_kses_post(sprintf($feat_2_desc, $sn)); ?>
            </p>
        </div>

        <!-- Feature 3: Live Channels -->
        <div class="feature-card animate-on-scroll">
            <div class="feature-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                    <line x1="8" y1="21" x2="16" y2="21" />
                    <line x1="12" y1="17" x2="12" y2="21" />
                </svg>
            </div>
            <h3 class="feature-title">
                <?php echo esc_html($feat_3_title); ?>
            </h3>
            <p class="feature-desc">
                <?php echo wp_kses_post(sprintf($feat_3_desc, $sn)); ?>
            </p>
        </div>

        <!-- Feature 4: PPV -->
        <div class="feature-card animate-on-scroll">
            <div class="feature-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path
                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
            </div>
            <h3 class="feature-title">
                <?php echo esc_html($feat_4_title); ?>
            </h3>
            <p class="feature-desc">
                <?php echo wp_kses_post(sprintf($feat_4_desc, $sn)); ?>
            </p>
        </div>

        <!-- Feature 5: 4K -->
        <div class="feature-card animate-on-scroll">
            <div class="feature-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                    <line x1="8" y1="21" x2="16" y2="21" />
                    <line x1="12" y1="17" x2="12" y2="21" />
                </svg>
            </div>
            <h3 class="feature-title">
                <?php echo esc_html($feat_5_title); ?>
            </h3>
            <p class="feature-desc">
                <?php echo wp_kses_post(sprintf($feat_5_desc, $sn)); ?>
            </p>
        </div>

        <!-- Feature 6: Support -->
        <div class="feature-card animate-on-scroll">
            <div class="feature-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
            </div>
            <h3 class="feature-title">
                <?php echo esc_html($feat_6_title); ?>
            </h3>
            <p class="feature-desc">
                <?php echo wp_kses_post(sprintf($feat_6_desc, $sn)); ?>
            </p>
        </div>

    </div>
</section>