<?php
/**
 * Channel Template – Polylang String Registration
 *
 * All simple (non-repeater, non-URL) strings for the channel template pages
 * are registered here so they appear in Languages → Translations and can be
 * translated per language without needing an extra plugin.
 *
 * Usage in templates: tpl_str('Default English text')
 */

// ── Helper function ──────────────────────────────────────────────────────────

if (!function_exists('tpl_str')) {
    /**
     * Return the Polylang translation of a registered channel template string.
     * Falls back gracefully if Polylang is not active.
     *
     * @param string $default  The English default (used as the lookup key).
     * @return string
     */
    function tpl_str(string $default): string
    {
        return function_exists('pll__') ? pll__($default) : $default;
    }
}

// ── Register all strings on admin init ──────────────────────────────────────

add_action('init', function () {
    if (!function_exists('pll_register_string')) {
        return;
    }

    $group = 'Channel Template';

    // ── Hero ─────────────────────────────────────────────────────────────────
    pll_register_string('hero_prefix', 'Watch', $group);
    pll_register_string('hero_suffix', 'Plus Thousands of Channels with NordicTV', $group);
    pll_register_string('hero_live_badge', '· Live Now', $group);
    pll_register_string('hero_savings_badge', 'Save Over $1,500 Annually!', $group);
    pll_register_string('hero_fallback_subtitle', 'Stream <strong>%s</strong> live with NordicTV. No cable required. <strong>No blackouts. No restrictions.</strong>', $group, true);
    pll_register_string('hero_feature_1', '40,000+ Channels', $group);
    pll_register_string('hero_feature_2', 'PPV $0 Extra', $group);
    pll_register_string('hero_feature_3', '4K Ultra HD', $group);
    pll_register_string('hero_cta_text', 'Start Watching Now', $group);
    pll_register_string('hero_disclaimer', 'No contract • Cancel anytime • 24h trial', $group);

    // ── Hero: Live Counter ────────────────────────────────────────────────────
    pll_register_string('hero_live_counter_label', 'LIVE NOW', $group);
    pll_register_string('hero_live_counter_desc', 'viewers streaming right now', $group);

    // ── Features (6 cards) ────────────────────────────────────────────────────
    pll_register_string('feat_tag', 'Why NordicTV', $group);
    pll_register_string('feat_title', 'Watch %s with <span class="gradient-text">NordicTV</span>', $group, true);
    pll_register_string('feat_subtitle', 'Everything you need for the ultimate %s streaming experience', $group);
    // Feature 1
    pll_register_string('feat_1_title', '40,000+ Live Channels', $group);
    pll_register_string('feat_1_desc', 'Stream %s alongside 40,000+ channels from 198 countries. Sports, news, entertainment — all included.', $group);
    // Feature 2
    pll_register_string('feat_2_title', '$0 PPV Events', $group);
    pll_register_string('feat_2_desc', 'Watch %s and every combat-sports Pay-Per-View event at zero extra cost. Save $70+ per event.', $group);
    // Feature 3
    pll_register_string('feat_3_title', 'All Sports Live', $group);
    pll_register_string('feat_3_desc', 'Top-flight football, European club nights, basketball, motorsport — every game live. Perfect for %s fans.', $group);
    // Feature 4
    pll_register_string('feat_4_title', '200K+ Movies & Shows', $group);
    pll_register_string('feat_4_desc', 'Massive VOD library with latest releases alongside your live %s stream. New content added daily.', $group);
    // Feature 5
    pll_register_string('feat_5_title', 'Crystal Clear 4K', $group);
    pll_register_string('feat_5_desc', 'Watch %s in crystal-clear 4K resolution with Dolby audio. No buffering, no lag, no compromise.', $group);
    // Feature 6
    pll_register_string('feat_6_title', '24/7 Support', $group);
    pll_register_string('feat_6_desc', 'Real humans, real help via live chat or WhatsApp anytime. We make sure %s is always on for you.', $group);

    // ── Pricing ──────────────────────────────────────────────────────────────
    pll_register_string('price_badge', 'Stream Smarter, Pay Less – Start Today!', $group);
    pll_register_string('price_title', 'Start Watching <span class="gradient-text">%s</span> Today', $group, true);
    pll_register_string('price_subtitle', 'Choose your plan and start streaming %s in minutes', $group);
    pll_register_string('price_step_1', 'Select Devices', $group);
    pll_register_string('price_step_2', 'Choose Plan', $group);
    pll_register_string('price_step_3', 'Complete Order', $group);
    pll_register_string('price_device_singular', 'Device', $group);
    pll_register_string('price_device_plural', 'Devices', $group);
    pll_register_string('price_device_question', 'How many devices will you use?', $group);
    pll_register_string('price_duration_title', 'Select your plan duration', $group);
    pll_register_string('price_1mo_label', '1 Month', $group);
    pll_register_string('price_3mo_label', '3 Months', $group);
    pll_register_string('price_6mo_label', '6 Months', $group);
    pll_register_string('price_12mo_label', '12 Months', $group);
    pll_register_string('price_save_40', 'Save 40%', $group);
    pll_register_string('price_save_58', 'Save 58%', $group);
    pll_register_string('price_best_value', 'Best Value', $group);
    pll_register_string('price_save_more', 'Save more', $group);
    pll_register_string('price_best_deal', 'Best deal!', $group);
    pll_register_string('price_per_month', 'per month', $group);
    pll_register_string('price_cta_text', 'Complete Your Order', $group);
    pll_register_string('price_guarantee', '14-day money-back guarantee. No questions asked.', $group);
    pll_register_string('price_trust_1_title', 'Transparent pricing', $group);
    pll_register_string('price_trust_1_desc', 'No contracts. Cancel anytime.', $group);
    pll_register_string('price_trust_2_title', 'Instant activation', $group);
    pll_register_string('price_trust_2_desc', 'Start watching in minutes.', $group);
    pll_register_string('price_trust_3_title', 'Risk-free', $group);
    pll_register_string('price_trust_3_desc', '14-day money-back guarantee.', $group);

    // ── FAQ title & subtitle ─────────────────────────────────────────────────
    pll_register_string('faq_tag', 'FAQ', $group);
    pll_register_string('faq_title', 'Frequently Asked <span class="gradient-text">Questions</span>', $group, true);
    pll_register_string('faq_subtitle', 'Common questions about watching %s with NordicTV', $group);

    // ── FAQ default Q&A (used when no ACF repeater items are set) ────────────
    pll_register_string('faq_q_1', 'How do I watch %s with NordicTV?', $group);
    pll_register_string('faq_a_1', 'Simply subscribe to any NordicTV plan, download our app on your device, and search for %s. You\'ll be streaming in minutes.', $group);
    pll_register_string('faq_q_2', 'Is %s available in 4K?', $group);
    pll_register_string('faq_a_2', 'Yes! When available, %s streams in full 4K Ultra HD quality with Dolby audio support.', $group);
    pll_register_string('faq_q_3', 'Can I record shows from %s?', $group);
    pll_register_string('faq_a_3', 'Yes, our built-in DVR feature lets you record any program from %s and watch it later. You can also use catch-up to rewind up to 7 days.', $group);
    pll_register_string('faq_q_4', 'What devices can I watch %s on?', $group);
    pll_register_string('faq_a_4', 'You can watch %s on Smart TV, Android, iOS, Amazon Firestick, Roku, Apple TV, MAG boxes, Windows, Mac, and more.', $group);
    pll_register_string('faq_q_5', 'Is there a trial?', $group);
    pll_register_string('faq_a_5', 'Yes! All NordicTV plans come with a 24h trial. You can cancel anytime during the trial period.', $group);
    pll_register_string('faq_q_6', 'Do I need a cable subscription?', $group);
    pll_register_string('faq_a_6', 'No! NordicTV is a standalone streaming service. No cable, no satellite dish, no contracts. Just an internet connection.', $group);

    // ── CTA ──────────────────────────────────────────────────────────────────
    pll_register_string('cta_title', 'Ready to Watch %s?', $group);
    pll_register_string('cta_subtitle', 'Join thousands of satisfied customers streaming %s and 40,000+ channels with NordicTV.', $group);
    pll_register_string('cta_button_text', 'Get Access Now', $group);
    pll_register_string('cta_badge_1', '256-bit SSL Encryption', $group);
    pll_register_string('cta_badge_2', 'Instant Activation', $group);
    pll_register_string('cta_badge_3', '24/7 Customer Support', $group);
});
