<?php
/**
 * Template Name: Store - Shop
 * Template Post Type: page
 * 
 * Custom template for the Shop/Products page
 */

defined('ABSPATH') || exit;

get_header();
?>

<style>
    /* Shop Page Specific Styles */
    .store-page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        padding: 6rem 0 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    .store-page-header h1 {
        color: #fff;
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
    }

    .store-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem 3rem;
    }

    /* Product Grid */
    .woocommerce ul.products {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        list-style: none;
        padding: 0;
    }

    .woocommerce ul.products li.product {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .woocommerce ul.products li.product:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(16, 137, 242, 0.15);
    }

    .woocommerce ul.products li.product a img {
        width: 100%;
        height: 200px;
        object-fit: contain;
        background: #F2F8FE;
        padding: 1rem;
    }

    .woocommerce ul.products li.product .woocommerce-loop-product__title {
        font-size: 1rem;
        font-weight: 600;
        color: #1F2937;
        margin: 1rem;
    }

    .woocommerce ul.products li.product .price {
        padding: 0 1rem;
        font-size: 1.125rem;
        font-weight: 700;
        color: #1089F2;
    }

    .woocommerce ul.products li.product .button {
        display: block;
        margin: 1rem;
        padding: 0.75rem 1rem;
        background: #1089F2;
        color: #fff;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .woocommerce ul.products li.product .button:hover {
        background: #0B70D0;
    }

    .woocommerce ul.products li.product .onsale {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #FBBF24;
        color: #000;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .woocommerce ul.products {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .store-page-header {
            padding: 5rem 0 1.5rem;
        }

        .store-page-header h1 {
            font-size: 1.75rem;
        }
    }
</style>

<!-- Page Header -->
<div class="store-page-header">
    <h1>
        <?php the_title(); ?>
    </h1>
</div>

<!-- Shop Content -->
<main class="store-content">
    <?php
    // Display WooCommerce shop content
    if (function_exists('woocommerce_content')) {
        woocommerce_content();
    } else {
        while (have_posts()):
            the_post();
            the_content();
        endwhile;
    }
    ?>
</main>

<?php
get_footer();
