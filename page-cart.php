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

<!-- Features Bar -->
<section class="features-bar">
    <div class="container">
        <div class="features-row">
            <div class="feat-item">
                <div class="feat-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div>
                    <h4>Free Shipping</h4>
                    <p>On orders over $75</p>
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="fa-solid fa-rotate-left"></i></div>
                <div>
                    <h4>30-Day Returns</h4>
                    <p>Hassle-free returns</p>
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <h4>1-Year Warranty</h4>
                    <p>On all audio products</p>
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="fa-solid fa-headset"></i></div>
                <div>
                    <h4>Expert Support</h4>
                    <p>Audio specialists 24/7</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
