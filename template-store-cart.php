<?php
/**
 * Template Name: Store - Cart
 * Template Post Type: page
 * 
 * Custom template for the Cart page
 */

defined('ABSPATH') || exit;

get_header();
?>

<style>
    /* Cart Page Styles */
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

    /* Cart Layout */
    .woocommerce-cart .woocommerce {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        align-items: start;
    }

    /* Cart Table */
    .woocommerce table.shop_table.cart {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: none;
    }

    .woocommerce table.shop_table.cart thead th {
        background: #F2F8FE;
        padding: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6B7280;
        border: none;
    }

    .woocommerce table.shop_table.cart tbody td {
        padding: 1rem;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }

    .woocommerce table.shop_table.cart .product-thumbnail img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        background: #F2F8FE;
        border-radius: 8px;
    }

    .woocommerce table.shop_table.cart .product-name a {
        color: #1F2937;
        font-weight: 600;
    }

    /* Cart Totals */
    .woocommerce .cart_totals {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
    }

    .woocommerce .cart_totals h2 {
        font-size: 1.125rem;
        margin-bottom: 1rem;
    }

    .woocommerce .cart_totals table th,
    .woocommerce .cart_totals table td {
        padding: 0.75rem 0;
        border-bottom: 1px solid #F3F4F6;
    }

    .woocommerce .checkout-button {
        width: 100%;
        padding: 1rem;
        background: #1089F2;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
    }

    .woocommerce .checkout-button:hover {
        background: #0B70D0;
    }

    @media (max-width: 768px) {
        .woocommerce-cart .woocommerce {
            grid-template-columns: 1fr;
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

<!-- Cart Content -->
<main class="store-content">
    <?php
    while (have_posts()):
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
