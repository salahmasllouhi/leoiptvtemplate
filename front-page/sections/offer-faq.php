<?php
/**
 * Offer FAQ Section
 *
 * Variables expected from template-offer-landing.php:
 *   $offer_faq_rows  (array) – ACF repeater rows [{faq_question, faq_answer}, ...]
 */

// Default offer-specific FAQs (objection removers)
$default_faq = [
    [
        'question' => iptv_text('offer_faq_q1', 'What devices does it work on?'),
        'answer' => iptv_text('offer_faq_a1', 'NordicTV works on Smart TVs, Android, iPhone & iPad, Amazon Firestick, MAG boxes, Windows and macOS — basically any device with an internet connection.'),
    ],
    [
        'question' => iptv_text('offer_faq_q2', 'Is there a contract or auto-renewal?'),
        'answer' => iptv_text('offer_faq_a2', 'No contract. No auto-renewal. You pay once and your subscription runs for the full period. When it expires, it\'s up to you to renew.'),
    ],
    [
        'question' => iptv_text('offer_faq_q3', 'How do I get the free months?'),
        'answer' => iptv_text('offer_faq_a3', 'The bonus months are included automatically in this offer — no codes needed. When you purchase, your full subscription (paid + bonus months) is activated in one go.'),
    ],
    [
        'question' => iptv_text('offer_faq_q4', 'When does it activate?'),
        'answer' => iptv_text('offer_faq_a4', 'Instantly after payment is confirmed. Your login details are sent to your email within minutes. If there is any delay, our support team is available 24/7.'),
    ],
    [
        'question' => iptv_text('offer_faq_q5', 'Is payment secure?'),
        'answer' => iptv_text('offer_faq_a5', 'Yes. Payments are processed via industry-standard secure gateways with 256-bit SSL encryption. We never store your card details.'),
    ],
    [
        'question' => iptv_text('offer_faq_q6', 'Can I get support if I need help?'),
        'answer' => wp_kses_post(iptv_text('offer_faq_a6', 'Absolutely. Our support team is available 24/7 via email at <a href="mailto:support@nordictv.io">support@nordictv.io</a>.')),
    ],
];

$faq_items = [];
if (!empty($offer_faq_rows)) {
    foreach ($offer_faq_rows as $row) {
        $q = $row['faq_question'] ?? '';
        $a = $row['faq_answer'] ?? '';
        if (!empty($q)) {
            $faq_items[] = ['question' => $q, 'answer' => $a];
        }
    }
} else {
    $faq_items = $default_faq;
}
?>

<section class="offer-faq" id="offer-faq">
    <div class="offer-section-inner">
        <div class="offer-section-header">
            <div class="offer-section-tag"><?php echo esc_html(iptv_text('offer_faq_tag', 'FAQ')); ?></div>
            <h2 class="offer-section-title">
                <?php echo esc_html(iptv_text('offer_faq_title', 'Any questions? We\'ve got answers.')); ?>
            </h2>
        </div>

        <div class="offer-faq__list">
            <?php foreach ($faq_items as $item): ?>
                <div class="offer-faq__item">
                    <button class="offer-faq__question" type="button" aria-expanded="false">
                        <?php echo esc_html($item['question']); ?>
                        <span class="offer-faq__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </span>
                    </button>
                    <div class="offer-faq__answer" hidden>
                        <div class="offer-faq__answer-inner">
                            <?php echo wp_kses_post($item['answer']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .offer-faq {
        background: var(--bg-section);
        padding: 5rem 1.5rem;
    }

    .offer-faq__list {
        margin-top: 2.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .offer-faq__item {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .offer-faq__item.open {
        border-color: var(--color-teal);
        box-shadow: 0 0 0 3px rgba(16, 137, 242, 0.08);
    }

    .offer-faq__question {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.25rem;
        background: none;
        border: none;
        cursor: pointer;
        text-align: left;
        font-size: 0.975rem;
        font-weight: 600;
        color: var(--text-primary);
        font-family: inherit;
        line-height: 1.4;
    }

    .offer-faq__icon {
        flex-shrink: 0;
        color: var(--text-muted);
        transition: transform 0.2s ease, color 0.2s ease;
        display: flex;
    }

    .offer-faq__item.open .offer-faq__icon {
        transform: rotate(180deg);
        color: var(--color-teal);
    }

    .offer-faq__answer {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.3s ease;
    }

    .offer-faq__item.open .offer-faq__answer {
        max-height: 500px;
    }

    .offer-faq__answer-inner {
        padding: 0 1.25rem 1.1rem;
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.65;
    }

    .offer-faq__answer-inner a {
        color: var(--color-teal);
        text-decoration: underline;
    }

    @media (min-width: 768px) {
        .offer-faq__list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            align-items: start;
        }
    }

    @media (max-width: 480px) {
        .offer-faq {
            padding: 4rem 1rem;
        }
    }
</style>

<script>
    (function () {
        document.querySelectorAll('.offer-faq__question').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item = btn.closest('.offer-faq__item');
                var answer = item.querySelector('.offer-faq__answer');
                var isOpen = item.classList.contains('open');

                // Close all others
                document.querySelectorAll('.offer-faq__item.open').forEach(function (openItem) {
                    if (openItem !== item) {
                        openItem.classList.remove('open');
                        openItem.querySelector('.offer-faq__question').setAttribute('aria-expanded', 'false');
                        openItem.querySelector('.offer-faq__answer').removeAttribute('hidden');
                    }
                });

                item.classList.toggle('open', !isOpen);
                btn.setAttribute('aria-expanded', (!isOpen).toString());
                if (!isOpen) {
                    answer.removeAttribute('hidden');
                }
            });
        });
    })();
</script>