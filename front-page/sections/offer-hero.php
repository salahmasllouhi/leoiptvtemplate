<?php
/**
 * Offer Hero Section — Two-column layout (content left, image right)
 *
 * Variables expected from template-offer-landing.php:
 *   $offer_headline      (string)
 *   $offer_subline       (string)
 *   $offer_cta_text      (string)
 *   $offer_checkout_url  (string)
 *   $offer_paid_months   (int)
 *   $offer_free_months   (int)
 */

$total_months = ($offer_paid_months ?? 12) + ($offer_free_months ?? 3);
$hero_image = 'https://nordictv.io/wp-content/uploads/2026/03/offer-123.png';
?>

<section class="offer-hero">
    <div class="offer-hero__inner">

        <!-- Left: Content -->
        <div class="offer-hero__content">
            <div class="offer-hero__badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                <?php echo esc_html(iptv_text('offer_hero_badge', 'Limited Time Offer')); ?>
            </div>

            <h1 class="offer-hero__headline">
                <?php echo esc_html($offer_headline); ?>
            </h1>

            <?php if (!empty($offer_subline)): ?>
                <p class="offer-hero__subline">
                    <?php echo esc_html($offer_subline); ?>
                </p>
            <?php endif; ?>

            <div class="offer-hero__meta">
                <span class="offer-hero__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <?php printf(esc_html__('%d months activated instantly', 'my-iptv'), (int) $total_months); ?>
                </span>
                <span class="offer-hero__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <?php echo esc_html(iptv_text('offer_hero_no_contract', 'No contract, cancel anytime')); ?>
                </span>
                <span class="offer-hero__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <?php echo esc_html(iptv_text('offer_hero_instant', 'Instant delivery by email')); ?>
                </span>
            </div>

            <a href="<?php echo esc_url($offer_checkout_url); ?>" class="btn btn-primary offer-cta-btn offer-hero__cta">
                <?php echo esc_html($offer_cta_text); ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <polyline points="12 5 19 12 12 19" />
                </svg>
            </a>
        </div>

        <!-- Right: Hero image -->
        <div class="offer-hero__image-wrap">
            <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr($offer_headline); ?>"
                class="offer-hero__image" loading="eager" decoding="async" />
        </div>

    </div>
</section>

<style>
    /* ── Hero layout ─────────────────────────────────────────── */
    .offer-hero {
        background: linear-gradient(135deg, var(--color-navy) 0%, var(--color-indigo) 60%, var(--color-indigo-hover) 100%);
        /* ← HERO PADDING: "top  sides  bottom"  — edit here to adjust spacing */
        padding: 4rem 2rem 3rem;
        position: relative;
        overflow: hidden;
    }

    .offer-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 70% 50%, rgba(16, 137, 242, 0.12) 0%, transparent 65%);
        pointer-events: none;
    }

    .offer-hero__inner {
        position: relative;
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        /* ← GAP between content column and image column — edit this value */
        gap: 1rem;
    }

    /* ── Left column ─────────────────────────────────────────── */
    .offer-hero__content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .offer-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(16, 137, 242, 0.15);
        border: 1px solid rgba(16, 137, 242, 0.35);
        color: var(--color-teal);
        padding: 0.45rem 1.1rem;
        border-radius: var(--radius-full);
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }

    .offer-hero__headline {
        font-size: clamp(1.9rem, 4vw, 3rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        margin: 0 0 1rem;
        letter-spacing: -0.02em;
    }

    .offer-hero__subline {
        font-size: clamp(0.95rem, 2vw, 1.15rem);
        color: rgba(255, 255, 255, 0.75);
        margin: 0 0 1.75rem;
        line-height: 1.55;
    }

    .offer-hero__meta {
        display: flex;
        flex-direction: row;
        /* ← keeps tags on the same line */
        flex-wrap: wrap;
        /* wraps only if the screen is too narrow */
        gap: 0.5rem 1.25rem;
        margin-bottom: 2rem;
    }

    .offer-hero__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .offer-hero__meta-item svg {
        color: var(--color-teal);
        flex-shrink: 0;
    }

    .offer-hero__cta {
        font-size: 1.05rem;
        padding: 15px 36px;
        box-shadow: 0 8px 32px rgba(16, 137, 242, 0.35);
    }

    /* ── Right column: image ─────────────────────────────────── */
    .offer-hero__image-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .offer-hero__image {
        width: 100%;
        max-width: 520px;
        height: auto;
        border-radius: var(--radius-lg);
        object-fit: cover;
        filter: drop-shadow(0 24px 48px rgba(0, 0, 0, 0.35));
        animation: offerHeroFloat 4s ease-in-out infinite;
    }

    @keyframes offerHeroFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* ── Mobile: stack vertically ────────────────────────────── */
    @media (max-width: 768px) {
        .offer-hero {
            padding: 5.5rem 1.25rem 3.5rem;
        }

        .offer-hero__inner {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }

        .offer-hero__content {
            align-items: center;
            text-align: center;
        }

        .offer-hero__meta {
            justify-content: center;
        }

        .offer-hero__image {
            max-width: 320px;
        }

        /* image below content on mobile */
        .offer-hero__image-wrap {
            order: 2;
        }

        .offer-hero__content {
            order: 1;
        }
    }
</style>