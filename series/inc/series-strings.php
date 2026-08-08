<?php
/**
 * Series Template – Polylang String Registration
 *
 * All simple (non-repeater, non-URL) strings for the series template pages
 * are registered here so they appear in Languages → Translations and can be
 * translated per language without needing an extra plugin.
 *
 * Usage in templates: srs_str('Default English text')
 */

// ── Helper function ──────────────────────────────────────────────────────────

if (!function_exists('srs_str')) {
    /**
     * Return the Polylang translation of a registered series template string.
     * Falls back gracefully if Polylang is not active.
     *
     * @param string $default  The English default (used as the lookup key).
     * @return string
     */
    function srs_str(string $default): string
    {
        return function_exists('pll__') ? pll__($default) : $default;
    }
}

// ── Register all strings on init ─────────────────────────────────────────────

add_action('init', function () {
    if (!function_exists('pll_register_string')) {
        return;
    }

    $group = 'Series Template';

    // ── Hero ─────────────────────────────────────────────────────────────────
    pll_register_string('series_hero_prefix', 'Watch', $group);
    pll_register_string('series_hero_suffix', 'Plus Thousands of Series with NordicTV', $group);
    pll_register_string('series_hero_streaming_badge', '· Streaming Now', $group);
    pll_register_string('series_hero_savings_badge', 'Save Over $1,500 Annually!', $group);
    pll_register_string('series_hero_fallback_subtitle', 'Stream <strong>%s</strong> with NordicTV. All seasons, all episodes. <strong>No restrictions.</strong>', $group, true);
    pll_register_string('series_hero_feature_1', '200K+ Movies & Shows', $group);
    pll_register_string('series_hero_feature_2', 'All Seasons Available', $group);
    pll_register_string('series_hero_feature_3', '4K Ultra HD', $group);
    pll_register_string('series_hero_cta_text', 'Start Watching Now', $group);
    pll_register_string('series_hero_disclaimer', 'No contract • Cancel anytime • 24h trial', $group);

    // ── Hero: Viewer Counter ──────────────────────────────────────────────────
    pll_register_string('series_hero_counter_label', 'STREAMING NOW', $group);
    pll_register_string('series_hero_counter_desc', 'viewers streaming right now', $group);

    // ── Features (6 cards) ────────────────────────────────────────────────────
    pll_register_string('series_feat_tag', 'Why NordicTV', $group);
    pll_register_string('series_feat_title', 'Watch %s with <span class="gradient-text">NordicTV</span>', $group, true);
    pll_register_string('series_feat_subtitle', 'Everything you need for the ultimate %s binge experience', $group);
    // Feature 1
    pll_register_string('series_feat_1_title', '200K+ Movies & Shows', $group);
    pll_register_string('series_feat_1_desc', 'Stream %s alongside 200,000+ movies and series. Every genre, every mood — all included.', $group);
    // Feature 2
    pll_register_string('series_feat_2_title', 'All Seasons & Episodes', $group);
    pll_register_string('series_feat_2_desc', 'Watch every season of %s from pilot to finale. No missing episodes, no gaps, no waiting.', $group);
    // Feature 3
    pll_register_string('series_feat_3_title', '40,000+ Live Channels', $group);
    pll_register_string('series_feat_3_desc', 'Beyond %s, enjoy 40,000+ live channels from 198 countries. Sports, news, entertainment — all included.', $group);
    // Feature 4
    pll_register_string('series_feat_4_title', '$0 PPV Events', $group);
    pll_register_string('series_feat_4_desc', 'All combat-sports and Pay-Per-View events at zero extra cost alongside your favorite series like %s.', $group);
    // Feature 5
    pll_register_string('series_feat_5_title', 'Crystal Clear 4K', $group);
    pll_register_string('series_feat_5_desc', 'Watch %s in crystal-clear 4K resolution with Dolby audio. No buffering, no lag, no compromise.', $group);
    // Feature 6
    pll_register_string('series_feat_6_title', '24/7 Support', $group);
    pll_register_string('series_feat_6_desc', 'Real humans, real help via live chat or WhatsApp anytime. We make sure %s is always available for you.', $group);

    // ── Pricing ──────────────────────────────────────────────────────────────
    pll_register_string('series_price_badge', 'Stream Smarter, Pay Less – Start Today!', $group);
    pll_register_string('series_price_title', 'Start Watching <span class="gradient-text">%s</span> Today', $group, true);
    pll_register_string('series_price_subtitle', 'Choose your plan and start streaming %s in minutes', $group);
    pll_register_string('series_price_step_1', 'Select Devices', $group);
    pll_register_string('series_price_step_2', 'Choose Plan', $group);
    pll_register_string('series_price_step_3', 'Complete Order', $group);
    pll_register_string('series_price_device_singular', 'Device', $group);
    pll_register_string('series_price_device_plural', 'Devices', $group);
    pll_register_string('series_price_device_question', 'How many devices will you use?', $group);
    pll_register_string('series_price_duration_title', 'Select your plan duration', $group);
    pll_register_string('series_price_1mo_label', '1 Month', $group);
    pll_register_string('series_price_3mo_label', '3 Months', $group);
    pll_register_string('series_price_6mo_label', '6 Months', $group);
    pll_register_string('series_price_12mo_label', '12 Months', $group);
    pll_register_string('series_price_save_40', 'Save 40%', $group);
    pll_register_string('series_price_save_58', 'Save 58%', $group);
    pll_register_string('series_price_best_value', 'Best Value', $group);
    pll_register_string('series_price_save_more', 'Save more', $group);
    pll_register_string('series_price_best_deal', 'Best deal!', $group);
    pll_register_string('series_price_per_month', 'per month', $group);
    pll_register_string('series_price_cta_text', 'Complete Your Order', $group);
    pll_register_string('series_price_guarantee', '14-day money-back guarantee. No questions asked.', $group);
    pll_register_string('series_price_trust_1_title', 'Transparent pricing', $group);
    pll_register_string('series_price_trust_1_desc', 'No contracts. Cancel anytime.', $group);
    pll_register_string('series_price_trust_2_title', 'Instant activation', $group);
    pll_register_string('series_price_trust_2_desc', 'Start watching in minutes.', $group);
    pll_register_string('series_price_trust_3_title', 'Risk-free', $group);
    pll_register_string('series_price_trust_3_desc', '14-day money-back guarantee.', $group);

    // ── FAQ title & subtitle ─────────────────────────────────────────────────
    pll_register_string('series_faq_tag', 'FAQ', $group);
    pll_register_string('series_faq_title', 'Frequently Asked <span class="gradient-text">Questions</span>', $group, true);
    pll_register_string('series_faq_subtitle', 'Common questions about watching %s with NordicTV', $group);

    // ── FAQ default Q&A ───────────────────────────────────────────────────────
    pll_register_string('series_faq_q_1', 'How do I watch %s with NordicTV?', $group);
    pll_register_string('series_faq_a_1', 'Simply subscribe to any NordicTV plan, download our app on your device, and search for %s. You\'ll be streaming in minutes.', $group);
    pll_register_string('series_faq_q_2', 'Are all seasons of %s available?', $group);
    pll_register_string('series_faq_a_2', 'Yes! NordicTV gives you access to all seasons and every episode of %s, from the pilot to the latest release.', $group);
    pll_register_string('series_faq_q_3', 'Can I watch %s in 4K?', $group);
    pll_register_string('series_faq_a_3', 'Yes! When available, %s streams in full 4K Ultra HD quality with Dolby audio support for the best viewing experience.', $group);
    pll_register_string('series_faq_q_4', 'What devices can I watch %s on?', $group);
    pll_register_string('series_faq_a_4', 'You can watch %s on Smart TV, Android, iOS, Amazon Firestick, Roku, Apple TV, MAG boxes, Windows, Mac, and more.', $group);
    pll_register_string('series_faq_q_5', 'Is there a trial?', $group);
    pll_register_string('series_faq_a_5', 'Yes! All NordicTV plans come with a 24h trial. You can cancel anytime during the trial period.', $group);
    pll_register_string('series_faq_q_6', 'Do I need a cable subscription?', $group);
    pll_register_string('series_faq_a_6', 'No! NordicTV is a standalone streaming service. No cable, no satellite dish, no contracts. Just an internet connection.', $group);

    // ── CTA ──────────────────────────────────────────────────────────────────
    pll_register_string('series_cta_title', 'Ready to Watch %s?', $group);
    pll_register_string('series_cta_subtitle', 'Join thousands binge-watching %s and 200,000+ movies & shows with NordicTV.', $group);
    pll_register_string('series_cta_button_text', 'Get Access Now', $group);
    pll_register_string('series_cta_badge_1', '256-bit SSL Encryption', $group);
    pll_register_string('series_cta_badge_2', 'Instant Activation', $group);
    pll_register_string('series_cta_badge_3', '24/7 Customer Support', $group);
});
