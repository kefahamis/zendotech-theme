<?php
/**
 * WooCommerce Template Wrapper
 * Routes to the correct template based on page type.
 * - Single product pages → woocommerce/single-product.php
 * - Archive/shop/category pages → page-shop.php
 */

if (is_singular('product')) {
    // Let WooCommerce load its single-product template override
    // from /woocommerce/single-product.php
    include(get_template_directory() . '/woocommerce/single-product.php');
} else {
    // Shop archive, product category, product tag pages
    include(get_template_directory() . '/page-shop.php');
}
