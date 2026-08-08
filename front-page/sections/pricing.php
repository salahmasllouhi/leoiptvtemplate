<?php
/**
 * Section: Pricing / checkout configurator (Design v2)
 *
 * Structure follows the annotated spec: a single centred panel with a screen
 * picker (radiogroup), a duration picker, the checkout CTA and payment badges.
 * The WooCommerce/currency wiring underneath is unchanged - pricing.js still
 * drives everything off #devices [data-devices] and #durations [data-duration].
 */

// Read the stored price table, never recompute it on a visitor's request. It is
// rebuilt by the weekly rate fetch (inc/currency-rates-api.php) and whenever a
// rate or product price is saved in wp-admin.
$all_prices     = IPTV_Currency_Settings::get_price_table();
$default_device = '1_device';

// Which screen count is pre-selected. Clamped to the 1-4 range we sell.
$default_screens = (int) iptv_config('pricing_default_screens', 1);
if ($default_screens < 1 || $default_screens > 4) {
    $default_screens = 1;
}

// Which duration is pre-selected. The panel expects a duration on load, so the
// default landing URL is ?connections=1&duration=12.
$default_months = (int) iptv_config('pricing_default_months', 12);
if (!in_array($default_months, array(1, 3, 6, 12), true)) {
    $default_months = 12;
}

// Panel checkout endpoints. The panel derives the price from the two params.
$checkout_base = iptv_config('checkout_base_url', 'https://panel.nordictv.io/checkout');
$trial_url     = iptv_config('trial_url', 'https://panel.nordictv.io/checkout/trial');

/**
 * Savings badge percentages are derived from the real prices rather than
 * hard-coded, so the claim stays true if prices change. pricing.js recomputes
 * them whenever the screen count changes.
 */
$base_monthly = isset($all_prices['1_month'][$default_device]['usd'])
    ? (float) $all_prices['1_month'][$default_device]['usd']
    : 0;

$iptv_savings_pct = function ($duration_key, $months) use ($all_prices, $default_device, $base_monthly) {
    if (!$base_monthly || empty($all_prices[$duration_key][$default_device]['usd'])) {
        return 0;
    }
    $per_month = (float) $all_prices[$duration_key][$default_device]['usd'] / $months;
    return max(0, (int) round((1 - ($per_month / $base_monthly)) * 100));
};

$durations = array(
    1  => array('key' => '1_month',   'label' => iptv_text('month_1_label', '1 Month'),   'save' => 0),
    3  => array('key' => '3_months',  'label' => iptv_text('month_3_label', '3 Months'),  'save' => $iptv_savings_pct('3_months', 3)),
    6  => array('key' => '6_months',  'label' => iptv_text('month_6_label', '6 Months'),  'save' => $iptv_savings_pct('6_months', 6)),
    12 => array('key' => '12_months', 'label' => iptv_text('month_12_label', '12 Months'), 'save' => $iptv_savings_pct('12_months', 12)),
);

// Screen count that carries the "POPULAR" flag.
$popular_screens = (int) iptv_config('pricing_popular_screens', 2);

// Variation ID map for checkout URLs. WooCommerce may be inactive (e.g. fresh
// install): degrade to an empty map.
$variation_map = array();
$duration_skus = array(1 => '1_month', 3 => '3_months', 6 => '6_months', 12 => '12_months');

if (function_exists('wc_get_product_id_by_sku') && function_exists('wc_get_product')) {
    foreach ($duration_skus as $months => $sku) {
        $product_id = wc_get_product_id_by_sku($sku);
        if (!$product_id) {
            continue;
        }
        $product = wc_get_product($product_id);
        if (!$product || !$product->is_type('variable')) {
            continue;
        }
        foreach ($product->get_children() as $child_id) {
            $variation = wc_get_product($child_id);
            if (!$variation) {
                continue;
            }
            $attributes = $variation->get_attributes();
            // Try global attribute (pa_devices) first, then local (devices)
            $device_attr = isset($attributes['pa_devices']) ? $attributes['pa_devices'] : '';
            if (!$device_attr) {
                $device_attr = isset($attributes['devices']) ? $attributes['devices'] : '';
            }
            if ($device_attr) {
                $variation_map[$device_attr . '-' . $months] = $child_id; // e.g. "1-12", "2-3"
            }
        }
    }
}

$screen_singular = iptv_text('screen_singular', 'Screen');
$screen_plural   = iptv_text('screen_plural', 'Screens');
?>
<script>
    // Main site URL for cross-site cart (used by pricing.js)
    window.iptvMainSiteUrl = '<?php echo esc_js(defined("IPTV_MAIN_SITE_URL") ? IPTV_MAIN_SITE_URL : network_site_url("/")); ?>';
    window.iptvPrices = <?php echo json_encode($all_prices); ?>;
    window.iptvVariationIds = <?php echo json_encode($variation_map); ?>;
    window.iptvDefaultScreens = <?php echo (int) $default_screens; ?>;
    window.iptvDefaultMonths = <?php echo (int) $default_months; ?>;
    window.iptvCheckoutBase = '<?php echo esc_js($checkout_base); ?>';
</script>

<section id="pricing" class="pricing dv2-section">
    <div class="container">
        <div class="dv2-section-head pricing-header">
            <h2><?php echo esc_html(iptv_text('pricing_title', 'Choose your plan that fits you')); ?></h2>
            <p>
                <?php echo esc_html(iptv_text('pricing_subtitle', 'Unlock unbeatable value and embrace remarkable savings with the best-priced IPTV subscription available today! Stream smart and watch your savings soar!')); ?>
            </p>
        </div>

        <div class="configurator">
            <!-- Step 1: screens -->
            <div class="config-section">
                <div class="config-step">
                    <span class="config-step-num">1</span>
                    <span class="config-title"><?php echo esc_html(iptv_text('screens_title', 'How many screens?')); ?></span>
                </div>
                <div class="dv2-screen-row" id="devices" role="radiogroup"
                    aria-label="<?php echo esc_attr(iptv_text('screens_title', 'How many screens?')); ?>">
                    <?php for ($i = 1; $i <= 4; $i++) :
                        $is_default = ($i === $default_screens);
                        $label = $i . ' ' . ($i > 1 ? $screen_plural : $screen_singular);
                        ?>
                        <button type="button"
                            class="select-card dv2-screen-pill<?php echo $is_default ? ' active' : ''; ?>"
                            data-devices="<?php echo $i; ?>"
                            role="radio"
                            aria-checked="<?php echo $is_default ? 'true' : 'false'; ?>"
                            aria-pressed="<?php echo $is_default ? 'true' : 'false'; ?>"
                            tabindex="<?php echo $is_default ? '0' : '-1'; ?>">
                            <span class="dv2-screen-label"><?php echo esc_html($label); ?></span>
                            <?php if ($i === $popular_screens) : ?>
                                <span class="dv2-pill-badge"><?php echo esc_html(iptv_text('popular_badge', 'POPULAR')); ?></span>
                            <?php endif; ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Step 2: duration -->
            <div class="config-section">
                <div class="config-step">
                    <span class="config-step-num">2</span>
                    <span class="config-title"><?php echo esc_html(iptv_text('duration_title', 'How long?')); ?></span>
                </div>
                <div class="dv2-duration-row" id="durations" role="radiogroup"
                    aria-label="<?php echo esc_attr(iptv_text('duration_title', 'How long?')); ?>">
                    <?php foreach ($durations as $months => $d) :
                        $price   = isset($all_prices[$d['key']][$default_device]['usd']) ? $all_prices[$d['key']][$default_device]['usd'] : 0;
                        $slug    = $months . 'mo'; // price-1mo, price-3mo, ...
                        $is_default = ($months === $default_months);
                        ?>
                        <button type="button"
                            class="select-card duration-card dv2-duration-card<?php echo $is_default ? ' active' : ''; ?>"
                            data-duration="<?php echo $months; ?>" data-months="<?php echo $months; ?>"
                            role="radio" aria-checked="<?php echo $is_default ? 'true' : 'false'; ?>"
                            tabindex="<?php echo $is_default ? '0' : '-1'; ?>">
                            <span class="badge badge-green<?php echo $d['save'] ? '' : ' is-hidden'; ?>"
                                id="save-<?php echo esc_attr($slug); ?>">
                                <?php echo esc_html(sprintf(iptv_text('save_percent_format', 'Save %d%%'), $d['save'])); ?>
                            </span>
                            <span class="duration-header"><?php echo esc_html($d['label']); ?></span>
                            <span class="duration-price price-display" id="price-<?php echo esc_attr($slug); ?>">
                                $<?php echo esc_html($price); ?>
                            </span>
                            <span class="duration-per" id="per-<?php echo esc_attr($slug); ?>">
                                <?php echo $months === 1
                                    ? esc_html(iptv_text('per_month', 'per month'))
                                    : '~$' . esc_html(round($price / $months, 2)) . '/mo'; ?>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Your total. Every number here is recomputed by pricing.js on
                 every screen/duration/currency change; the PHP values below are
                 only the pre-JS paint for the default selection. -->
            <?php
            $default_device_key = $default_screens === 1 ? '1_device' : $default_screens . '_devices';
            $total_now  = isset($all_prices[$durations[$default_months]['key']][$default_device_key]['usd'])
                ? (float) $all_prices[$durations[$default_months]['key']][$default_device_key]['usd']
                : 0;
            $total_rate = isset($all_prices['1_month'][$default_device_key]['usd'])
                ? (float) $all_prices['1_month'][$default_device_key]['usd']
                : 0;
            // "Was" is the same plan bought month by month, so the saving is real.
            $total_was  = $total_rate * $default_months;
            $total_off  = max(0, $total_was - $total_now);
            $total_pct  = ($total_was > 0) ? (int) round(($total_off / $total_was) * 100) : 0;
            $has_saving = ($default_months > 1 && $total_off > 0.005);
            ?>
            <div class="dv2-total-card" id="total-card">
                <span class="dv2-total-label"><?php echo esc_html(iptv_text('total_label', 'Your total')); ?></span>

                <div class="dv2-total-main">
                    <span class="dv2-total-price" id="total-price">$<?php echo esc_html(number_format($total_now, 2)); ?></span>
                    <div class="dv2-total-aside<?php echo $has_saving ? '' : ' is-hidden'; ?>" id="total-aside">
                        <span class="dv2-total-was" id="total-was">$<?php echo esc_html(number_format($total_was, 2)); ?></span>
                        <span class="dv2-total-save" id="total-save">
                            <?php echo esc_html(sprintf(
                                iptv_text('total_save_format', 'Save %1$s (%2$d%%)'),
                                '$' . number_format($total_off, 2),
                                $total_pct
                            )); ?>
                        </span>
                    </div>
                </div>

                <p class="dv2-total-meta" id="total-meta">
                    <?php echo esc_html(sprintf(
                        iptv_text('total_meta_format', 'one-time · %s/mo'),
                        '$' . number_format($total_now / max(1, $default_months), 2)
                    )); ?>
                </p>

                <?php
                // Days the discount is held for a returning visitor. pricing.js
                // stores the deadline locally so the timer does not reset on
                // every page view.
                $offer_days = max(1, (int) iptv_config('offer_lock_days', 5));
                ?>
                <div class="dv2-total-lock<?php echo $has_saving ? '' : ' is-hidden'; ?>" id="total-lock"
                    data-offer-days="<?php echo (int) $offer_days; ?>">
                    <span class="dv2-total-dot" aria-hidden="true"></span>
                    <span id="total-lock-copy">
                        <?php echo esc_html(sprintf(
                            iptv_text('total_lock_format', 'Your %d%% discount is locked for'),
                            $total_pct
                        )); ?>
                    </span>
                    <strong id="total-countdown"><?php echo (int) $offer_days; ?>d 00:00:00</strong>
                </div>
            </div>

            <!-- Scarcity -->
            <?php $slots_line = iptv_text('checkout_slots_line', '🔥 Only 32 activation slots left this month'); ?>
            <?php if ($slots_line) : ?>
                <p class="dv2-scarcity"><?php echo esc_html($slots_line); ?></p>
            <?php endif; ?>

            <!-- Checkout. href and price are recomputed by pricing.js on every change. -->
            <a href="<?php echo esc_url($checkout_base . '?connections=' . $default_screens . '&duration=' . $default_months); ?>"
                id="checkout-btn" class="cta-btn">
                <span id="button-text"><?php echo esc_html(iptv_text('checkout_button', 'Start watching')); ?></span>
            </a>

            <ul class="dv2-checkout-trust">
                <li><?php echo esc_html(iptv_text('checkout_trust_1', '14-day money-back')); ?></li>
                <li><?php echo esc_html(iptv_text('checkout_trust_2', 'Instant activation')); ?></li>
                <li><?php echo esc_html(iptv_text('checkout_trust_3', 'No auto-renew')); ?></li>
            </ul>

            <p class="dv2-guarantee">
                <?php echo esc_html(iptv_text('guarantee_text', 'Watching in 60 seconds · Secure checkout · Pay once, no auto-renew')); ?>
            </p>

            <!-- Payment methods -->
            <div class="dv2-payments">
                <span class="dv2-payments-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <rect x="4" y="11" width="16" height="10" rx="2"></rect>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                    </svg>
                    <?php echo esc_html(iptv_text('payments_label', 'Secure checkout')); ?>
                </span>
                <ul class="dv2-payment-badges">
                    <li class="dv2-payment-badge dv2-payment-badge--visa">VISA</li>
                    <li class="dv2-payment-badge dv2-payment-badge--mc" aria-label="Mastercard">
                        <span class="dv2-mc-mark" aria-hidden="true"><i></i><i></i></span>
                        <span>Mastercard</span>
                    </li>
                    <li class="dv2-payment-badge dv2-payment-badge--amex">AMEX</li>
                    <li class="dv2-payment-badge dv2-payment-badge--paypal">PayPal</li>
                    <li class="dv2-payment-badge dv2-payment-badge--btc">₿ Bitcoin</li>
                </ul>
            </div>

            <!-- What every plan includes -->
            <?php
            $plan_includes = array(
                1  => '40,000+ Live TV Channels',
                2  => '200,000+ Movies & Series (VOD)',
                3  => '4K, Ultra HD & HD quality',
                4  => 'Stable, fast servers',
                5  => 'Full TV guide (EPG)',
                6  => 'Anti-Buffer™ 9.8',
                7  => 'Live hockey, football & handball',
                8  => 'Pay-Per-View (PPV) events',
                9  => 'Auto-updating channels & VOD',
                10 => '24/7 support',
            );
            ?>
            <div class="dv2-loaded">
                <h3 class="dv2-loaded-title">
                    <span aria-hidden="true">⚡</span>
                    <?php echo esc_html(iptv_text('plan_includes_title', 'Every plan is fully loaded')); ?>
                </h3>
                <ul class="dv2-loaded-list">
                    <?php foreach ($plan_includes as $n => $item) : ?>
                        <li><?php echo esc_html(iptv_text("plan_includes_{$n}", $item)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Trial -->
            <div class="dv2-trial">
                <span class="dv2-trial-copy"><?php echo esc_html(iptv_text('trial_prompt', 'Not ready to buy?')); ?></span>
                <a href="<?php echo esc_url($trial_url); ?>" class="dv2-btn dv2-trial-btn">
                    <?php echo esc_html(iptv_text('trial_cta', 'Start a 24-hour trial — no card')); ?>
                </a>
            </div>
        </div>
    </div>
</section>
