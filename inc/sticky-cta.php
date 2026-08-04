<?php
/**
 * Sticky CTA bar
 *
 * A bottom-pinned bar on every screen size: a countdown next to two
 * CTAs — pricing and the 24-hour trial, stacked on phones and on one centered
 * row from 769px up. Rendered on every template via
 * wp_footer, since every template in this theme ends in wp_footer() either
 * directly or through get_footer().
 *
 * Editable from WordPress:
 *   Settings → Site Config   — on/off switch, pricing link override
 *                              (the trial link reuses the existing Trial URL)
 *   Front page editor        — the three labels, per language, under the
 *                              "📌 Sticky Bar" tab
 *
 * The countdown shares its deadline with the pricing panel (localStorage key
 * `iptvOfferDeadline`, window from Site Config → Discount Lock), so the two
 * timers always show the same number.
 *
 * @package Nordic_IPTV
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('iptv_sticky_cta_is_visible')) {
    /**
     * Whether the sticky bar should render on the current request.
     *
     * Suppressed anywhere the visitor is already in a buying flow — a "See
     * pricing" button on the checkout page only leaks the sale.
     *
     * @return bool
     */
    function iptv_sticky_cta_is_visible()
    {
        if (is_admin() || is_feed() || is_embed()) {
            return false;
        }

        // Site Config toggle. Defaults to on so the bar works before the field
        // group has been synced into the database.
        if (!iptv_config('sticky_cta_enabled', true)) {
            return false;
        }

        if (function_exists('is_checkout') && (is_checkout() || is_cart() || is_account_page())) {
            return false;
        }

        if (is_page_template(array('template-store-checkout.php', 'template-store-cart.php'))) {
            return false;
        }

        /**
         * Filter the sticky bar's visibility for a single request.
         *
         * @param bool $visible
         */
        return (bool) apply_filters('iptv_sticky_cta_visible', true);
    }
}

if (!function_exists('iptv_sticky_cta_pricing_url')) {
    /**
     * Destination for the pricing CTA.
     *
     * The pricing panel is a section of the front page, so off the front page the
     * link has to carry the language's home URL as well as the anchor.
     *
     * @return string
     */
    function iptv_sticky_cta_pricing_url()
    {
        $configured = iptv_config('sticky_pricing_url', '');
        if ($configured) {
            return $configured;
        }

        if (is_front_page()) {
            return '#pricing';
        }

        $home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

        return trailingslashit($home) . '#pricing';
    }
}

/**
 * Load the bar's styles everywhere.
 *
 * Enqueued rather than <link>-ed because the templates disagree about how they
 * pull in CSS — front-page.php inlines it, page.php and single.php link it — but
 * all of them call wp_head().
 */
add_action('wp_enqueue_scripts', function () {
    if (!iptv_sticky_cta_is_visible()) {
        return;
    }

    wp_enqueue_style(
        'iptv-sticky-cta',
        get_template_directory_uri() . '/front-page/css/sticky-cta.css',
        array(),
        iptv_asset_version('front-page/css/sticky-cta.css')
    );

    wp_enqueue_script(
        'iptv-sticky-cta',
        get_template_directory_uri() . '/front-page/js/sticky-cta.js',
        array(),
        iptv_asset_version('front-page/js/sticky-cta.js'),
        true
    );
}, 20);

/**
 * Render the bar.
 *
 * Priority 5 so the markup is in the document before wp_print_footer_scripts
 * (priority 20) runs sticky-cta.js, which looks the element up on execution.
 */
add_action('wp_footer', function () {
    if (!iptv_sticky_cta_is_visible()) {
        return;
    }

    $pricing_url = iptv_sticky_cta_pricing_url();
    $trial_url   = iptv_config('trial_url', 'https://panel.nordictv.io/checkout/trial');

    // Same window the pricing panel's lock line quotes, so both countdowns agree.
    $offer_days = max(1, (int) iptv_config('offer_lock_days', 5));

    $timer_label   = iptv_text('sticky_timer_label', 'Offer ends in');
    $pricing_label = iptv_text('sticky_pricing_label', 'See Pricing');
    $trial_label   = iptv_text('sticky_trial_label', '24h Trial');

    // The day marker in "4d 07:12:39". Handed to JS rather than hardcoded there,
    // so the countdown is localised along with the rest of the bar — "d" suits
    // sv/no/dk (dag), but Finnish wants "pv" and Icelandic "d." or similar.
    $days_suffix = iptv_text('sticky_days_suffix', 'd');
    ?>
    <div class="dv2-sticky-cta" id="sticky-cta" data-offer-days="<?php echo (int) $offer_days; ?>"
        data-days-suffix="<?php echo esc_attr($days_suffix); ?>">
        <?php // The inner wrapper is what caps the row width on desktop; the bar
              // itself has to stay full-bleed to carry the background and border. ?>
        <div class="dv2-sticky-inner">
            <div class="dv2-sticky-timer">
                <span class="dv2-sticky-dot" aria-hidden="true"></span>
                <span class="dv2-sticky-timer-label"><?php echo esc_html($timer_label); ?></span>
                <?php // Seeded with the full window; sticky-cta.js replaces it on first tick. ?>
                <strong class="dv2-sticky-timer-value" id="sticky-cta-countdown"><?php echo (int) $offer_days . esc_html($days_suffix); ?> 00:00:00</strong>
            </div>

            <div class="dv2-sticky-actions">
                <a href="<?php echo esc_url($pricing_url); ?>" class="dv2-sticky-btn dv2-sticky-btn--primary">
                    <?php echo esc_html($pricing_label); ?>
                </a>
                <a href="<?php echo esc_url($trial_url); ?>" class="dv2-sticky-btn dv2-sticky-btn--ghost">
                    <?php echo esc_html($trial_label); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}, 5);
