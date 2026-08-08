<?php
/**
 * Section: Reviews (Design v2)
 * Score card plus a grid of customer reviews.
 */

$title    = iptv_text('reviews_title', 'What our customers actually say');
$subtitle = iptv_text('reviews_subtitle', 'Join thousands of cord-cutters across Scandinavia who\'ve switched to NordicTV.');

// Reviews come from the `reviews_list` repeater on the front page, so they are
// translated per language alongside the rest of the page copy. Title and date are
// optional; a row without text and author is skipped.
$reviews = [];
$review_rows = function_exists('get_field') ? get_field('reviews_list', get_option('page_on_front')) : null;

if (is_array($review_rows)) {
    foreach ($review_rows as $row) {
        $text   = $row['review_text']   ?? '';
        $author = $row['review_author'] ?? '';

        if ($text && $author) {
            $reviews[] = [
                'text'   => $text,
                'author' => $author,
                'title'  => $row['review_title'] ?? '',
                'when'   => $row['review_when'] ?? '',
            ];
        }
    }
}

if (empty($reviews)) {
    $reviews = [
        ['title' => 'Crystal clear on every device', 'when' => 'Dec 2024', 'author' => 'Marcus L. · Stockholm, SE', 'text' => 'Crystal clear picture on all my devices. No buffering, no freezing — just pure streaming. Switched from cable 6 months ago and never looked back.'],
        ['title' => 'The sports coverage is insane', 'when' => 'Jan 2025', 'author' => 'Anna K. · Oslo, NO', 'text' => 'Every big match, European club nights, basketball — all in HD. Setup took 5 minutes. Incredible service.'],
        ['title' => 'The quality blew me away', 'when' => 'Nov 2024', 'author' => 'Thomas B. · Copenhagen, DK', 'text' => 'I was skeptical at first but the quality blew me away. 40,000+ channels and they all work perfectly. Customer support replied within the hour.'],
        ['title' => 'Works on everything at once', 'when' => 'Feb 2025', 'author' => 'Erika V. · Helsinki, FI', 'text' => 'Finally a service that actually works on my Fire Stick AND smart TV at the same time. The 4-device plan is worth every penny.'],
        ['title' => 'Zero downtime, ever', 'when' => 'Jan 2025', 'author' => 'Jonas H. · Gothenburg, SE', 'text' => 'Been with NordicTV for over a year now. Zero downtime, constant channel list updates. This is how streaming should be done.'],
        ['title' => 'Great value for the price', 'when' => '3 days ago', 'author' => 'Sofia N. · Bergen, NO', 'text' => 'Great value for the price. Support answered my questions within minutes on WhatsApp — no waiting around.'],
        ['title' => 'Replaced four subscriptions', 'when' => '1 week ago', 'author' => 'Henrik D. · Malmö, SE', 'text' => 'I cancelled cable and three streaming apps. One bill, more content, and 4K on everything. Should have done it years ago.'],
    ];
}

/**
 * Score summary shown above the reviews: one headline score, then a breakdown
 * bar per category. Values are iptv_text keys so they stay editable per
 * language rather than being frozen into the template.
 */
$score_overall = iptv_text('reviews_score', '4.8');
$score_label   = iptv_text('reviews_score_label', 'Our review score');

$score_bars = [
    1 => [iptv_text('reviews_bar_1_label', 'Library'), iptv_text('reviews_bar_1_value', '4.9')],
    2 => [iptv_text('reviews_bar_2_label', 'Stability'), iptv_text('reviews_bar_2_value', '4.7')],
    3 => [iptv_text('reviews_bar_3_label', 'Device support'), iptv_text('reviews_bar_3_value', '4.9')],
    4 => [iptv_text('reviews_bar_4_label', 'Value'), iptv_text('reviews_bar_4_value', '4.8')],
];

/**
 * Renders one review card.
 */
$render_review = function ($review) {
    ?>
    <div class="dv2-review-card">
        <div class="dv2-review-top">
            <span class="dv2-review-stars" aria-hidden="true">★★★★★</span>
            <?php if (!empty($review['when'])) : ?>
                <span class="dv2-review-when"><?php echo esc_html($review['when']); ?></span>
            <?php endif; ?>
        </div>
        <?php if (!empty($review['title'])) : ?>
            <div class="dv2-review-title"><?php echo esc_html($review['title']); ?></div>
        <?php endif; ?>
        <p class="dv2-review-body"><?php echo esc_html($review['text']); ?></p>
        <div class="dv2-review-name"><?php echo esc_html($review['author']); ?></div>
    </div>
    <?php
};
?>
<section class="reviews dv2-section">
    <div class="container">
        <div class="dv2-section-head">
            <h2><?php echo esc_html($title); ?></h2>
            <p><?php echo esc_html($subtitle); ?></p>
        </div>

        <div class="dv2-review-summary">
            <div class="dv2-review-score">
                <span class="dv2-review-score-value"><?php echo esc_html($score_overall); ?></span>
                <span class="dv2-review-score-meta">
                    <span class="dv2-review-score-stars" aria-hidden="true">★★★★★</span>
                    <span class="dv2-review-score-label"><?php echo esc_html($score_label); ?></span>
                </span>
            </div>

            <div class="dv2-review-bars">
                <?php foreach ($score_bars as $bar) : ?>
                    <?php
                    // Bars are scored out of 5. Clamped so a bad value cannot
                    // render a bar wider than its track.
                    $pct = max(0, min(100, ((float) str_replace(',', '.', $bar[1]) / 5) * 100));
                    ?>
                    <div class="dv2-review-bar">
                        <div class="dv2-review-bar-head">
                            <span><?php echo esc_html($bar[0]); ?></span>
                            <strong><?php echo esc_html($bar[1]); ?></strong>
                        </div>
                        <div class="dv2-review-bar-track">
                            <span class="dv2-review-bar-fill" style="width:<?php echo esc_attr(round($pct, 1)); ?>%"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php
    // A single scroll-snapped row, advanced by the arrows only — no autoplay.
    // data-review-carousel is what front-page/js/reviews.js binds to.
    ?>
    <div class="dv2-review-carousel" data-review-carousel>
        <button type="button" class="dv2-review-nav dv2-review-nav--prev" data-review-prev
                aria-label="<?php echo esc_attr(iptv_text('reviews_prev', 'Previous reviews')); ?>">
            <span aria-hidden="true">‹</span>
        </button>

        <div class="dv2-review-viewport" data-review-viewport>
            <div class="dv2-review-track">
                <?php foreach ($reviews as $review) {
                    $render_review($review);
                } ?>
            </div>
        </div>

        <button type="button" class="dv2-review-nav dv2-review-nav--next" data-review-next
                aria-label="<?php echo esc_attr(iptv_text('reviews_next', 'More reviews')); ?>">
            <span aria-hidden="true">›</span>
        </button>
    </div>
</section>
