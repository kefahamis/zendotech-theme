<?php
/**
 * Template Name: Cart Page
 */
get_header();

wp_enqueue_style('zendotech-cart-style', get_template_directory_uri() . '/assets/css/cart.css', array(), '1.0.1');
?>

<!-- ===== BREADCRUMB ===== -->
<section class="breadcrumb-section">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
            <li class="active">Shopping Cart</li>
        </ul>
    </div>
</section>

<!-- ===== CART CONTENT ===== -->
<section class="cart-section">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        <?php echo do_shortcode('[woocommerce_cart]'); ?>
    </div>
</section>



<?php get_footer(); ?>
