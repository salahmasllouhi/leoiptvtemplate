<?php
/**
 * Offer Steps Section — "How It Works"
 *
 * Variables expected from template-offer-landing.php:
 *   $offer_steps_rows  (array) – ACF repeater rows [{step_number, step_title, step_description}, ...]
 *   $offer_checkout_url (string)
 *   $offer_paid_months  (int)
 *   $offer_free_months  (int)
 */

$total_months = ($offer_paid_months ?? 12) + ($offer_free_months ?? 3);
$paid = $offer_paid_months ?? 12;
$free = $offer_free_months ?? 3;

// Default steps
$default_steps = [
    [
        'number' => '1',
        'title' => iptv_text('offer_step_1_title', 'Pick Your Plan'),
        'description' => iptv_text('offer_step_1_desc', 'Click the button below and you\'ll go straight to checkout — no browsing required.'),
    ],
    [
        'number' => '2',
        'title' => iptv_text('offer_step_2_title', 'Complete Checkout'),
        'description' => iptv_text('offer_step_2_desc', 'Enter your email and phone. That\'s all we need. Secure payment in seconds.'),
    ],
    [
        'number' => '3',
        /* translators: %d = total months */
        'title' => sprintf(iptv_text('offer_step_3_title', 'Get %d Months Activated'), $total_months),
        /* translators: %1$d = paid months, %2$d = free bonus months */
        'description' => sprintf(
            iptv_text('offer_step_3_desc', 'Your subscription is activated instantly. Pay for %1$d months, enjoy %2$d months free — all in one go.'),
            $paid,
            $free
        ),
    ],
];

$steps = [];
if (!empty($offer_steps_rows)) {
    foreach ($offer_steps_rows as $i => $row) {
        $steps[] = [
            'number' => $row['step_number'] ?? (string) ($i + 1),
            'title' => $row['step_title'] ?? '',
            'description' => $row['step_description'] ?? '',
        ];
    }
} else {
    $steps = $default_steps;
}
?>

<section class="offer-steps" id="offer-steps">
    <div class="offer-section-inner">
        <div class="offer-section-header">
            <div class="offer-section-tag"><?php echo esc_html(iptv_text('offer_steps_tag', 'How It Works')); ?></div>
            <h2 class="offer-section-title">
                <?php echo esc_html(iptv_text('offer_steps_title', '3 simple steps to get started')); ?>
            </h2>
        </div>

        <div class="offer-steps__track">
            <?php foreach ($steps as $i => $step): ?>
                <?php if (empty($step['title']))
                    continue; ?>
                <div class="offer-steps__step">
                    <div class="offer-steps__circle" aria-hidden="true">
                        <?php echo esc_html($step['number'] ?: ($i + 1)); ?>
                    </div>
                    <div class="offer-steps__content">
                        <h3 class="offer-steps__title"><?php echo esc_html($step['title']); ?></h3>
                        <?php if (!empty($step['description'])): ?>
                            <p class="offer-steps__desc"><?php echo wp_kses_post($step['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($i < count($steps) - 1): ?>
                    <div class="offer-steps__connector" aria-hidden="true"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .offer-steps {
        background: var(--bg-section);
        padding: 5rem 1.5rem;
    }

    .offer-steps__track {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 0;
        margin-top: 3rem;
        flex-wrap: wrap;
    }

    .offer-steps__step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        min-width: 180px;
        max-width: 260px;
        padding: 0 1rem;
    }

    .offer-steps__connector {
        flex-shrink: 0;
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, var(--color-teal), var(--color-indigo-hover));
        margin-top: 2rem;
        opacity: 0.5;
        align-self: flex-start;
        margin-top: 1.75rem;
    }

    .offer-steps__circle {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-teal), var(--color-indigo-hover));
        color: #fff;
        font-size: 1.25rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-bottom: 1rem;
        box-shadow: 0 4px 16px rgba(16, 137, 242, 0.3);
    }

    .offer-steps__content {
        flex: 1;
    }

    .offer-steps__title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.5rem;
    }

    .offer-steps__desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.55;
        margin: 0;
    }

    @media (max-width: 640px) {
        .offer-steps__track {
            flex-direction: column;
            align-items: center;
            gap: 0;
        }

        .offer-steps__step {
            flex-direction: row;
            text-align: left;
            max-width: 100%;
            padding: 0;
            gap: 1rem;
        }

        .offer-steps__connector {
            width: 2px;
            height: 2rem;
            margin: 0 0 0 1.4rem;
            background: linear-gradient(180deg, var(--color-teal), var(--color-indigo-hover));
        }

        .offer-steps__circle {
            margin-bottom: 0;
            flex-shrink: 0;
        }
    }
</style>