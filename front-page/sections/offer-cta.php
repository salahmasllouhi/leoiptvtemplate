<?php
/**
 * Offer Final CTA Section
 *
 * Variables expected from template-offer-landing.php:
 *   $offer_final_cta_text  (string)
 *   $offer_urgency_line    (string)
 *   $offer_checkout_url    (string)
 *   $offer_paid_months     (int)
 *   $offer_free_months     (int)
 */

$total = ($offer_paid_months ?? 12) + ($offer_free_months ?? 3);
$cta = !empty($offer_final_cta_text) ? $offer_final_cta_text : $offer_cta_text;
?>

<section class="offer-final-cta">
    <div class="offer-final-cta__inner">
        <h2 class="offer-final-cta__title">
            <?php printf(
                /* translators: %d = total months */
                esc_html(iptv_text('offer_final_cta_title', 'Don\'t miss %d months for the price of %d')),
                (int) $total,
                (int) ($offer_paid_months ?? 12)
            ); ?>
        </h2>

        <p class="offer-final-cta__subtitle">
            <?php echo esc_html(iptv_text('offer_final_cta_subtitle', '40,000+ channels. 200,000+ movies. Any device. Activated instantly.')); ?>
        </p>

        <a href="<?php echo esc_url($offer_checkout_url); ?>"
            class="btn btn-primary offer-cta-btn offer-final-cta__btn">
            <?php echo esc_html($cta); ?>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
            </svg>
        </a>

        <?php if (!empty($offer_urgency_line)): ?>
            <p class="offer-final-cta__urgency">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                <?php echo esc_html($offer_urgency_line); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<style>
    .offer-final-cta {
        background: linear-gradient(135deg, var(--color-navy) 0%, var(--color-indigo) 100%);
        padding: 5rem 1.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .offer-final-cta::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 50% 80%, rgba(16, 137, 242, 0.1) 0%, transparent 65%);
        pointer-events: none;
    }

    .offer-final-cta__inner {
        position: relative;
        max-width: 640px;
        margin: 0 auto;
    }

    .offer-final-cta__title {
        font-size: clamp(1.6rem, 4vw, 2.5rem);
        font-weight: 800;
        color: #fff;
        margin: 0 0 1rem;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .offer-final-cta__subtitle {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.7);
        margin: 0 0 2.25rem;
        line-height: 1.55;
    }

    .offer-final-cta__btn {
        font-size: 1.1rem;
        padding: 16px 44px;
        box-shadow: 0 8px 32px rgba(16, 137, 242, 0.35);
    }

    .offer-final-cta__urgency {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin: 1.25rem 0 0;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.55);
        font-style: italic;
    }

    .offer-final-cta__urgency svg {
        flex-shrink: 0;
        color: var(--color-gold);
    }

    @media (max-width: 480px) {
        .offer-final-cta {
            padding: 4rem 1rem;
        }

        .offer-final-cta__btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>