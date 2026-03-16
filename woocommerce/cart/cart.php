<?php
/**
 * Cart Page - Zendotech Audio Custom Design
 * Overrides WooCommerce default cart template
 * Matches the original cart.html design exactly
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<!-- ===== BREADCRUMB ===== -->
<section class="breadcrumb-bar">
    <div class="container">
        <div class="breadcrumb-inner">
            <ol class="breadcrumb">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
                <li class="current">Shopping Cart</li>
            </ol>
            <h1 class="page-title">Shopping Cart</h1>
        </div>
    </div>
</section>

<!-- ===== CART CONTENT ===== -->
<section class="cart-section">
    <div class="container">

        <?php if (WC()->cart->is_empty()) : ?>
            <!-- Empty Cart State -->
            <div class="cart-empty">
                <div class="ce-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h2>Your cart is currently empty</h2>
                <p>Looks like you haven't added any products yet. Explore our collection and find something you'll love!</p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-primary">
                    <i class="fa-solid fa-arrow-left"></i> Return to Shop
                </a>
            </div>
        <?php else : ?>
            <!-- Cart with Items -->
            <div class="cart-content">

                <!-- Progress Bar for Free Shipping -->
                <?php
                $cart_total = WC()->cart->get_subtotal();
                $free_shipping_min = 75;
                $remaining = max(0, $free_shipping_min - $cart_total);
                $progress = min(100, ($cart_total / $free_shipping_min) * 100);
                ?>
                <div class="cart-progress">
                    <div class="cp-bar">
                        <div class="cp-fill <?php echo $remaining <= 0 ? 'complete' : ''; ?>" style="width: <?php echo esc_attr($progress); ?>%"></div>
                    </div>
                    <p class="cp-text">
                        <i class="fa-solid fa-truck-fast"></i>
                        <?php if ($remaining > 0) : ?>
                            <span>Add <strong><?php echo wc_price($remaining); ?></strong> more to get <strong>FREE Shipping!</strong></span>
                        <?php else : ?>
                            <span><strong>Congratulations!</strong> You've qualified for <strong>FREE Shipping!</strong></span>
                        <?php endif; ?>
                    </p>
                </div>

                <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                    <?php do_action('woocommerce_before_cart_table'); ?>

                    <div class="cart-layout">
                        <!-- LEFT: Cart Table -->
                        <div class="cart-table-wrap">

                            <!-- Table Header -->
                            <div class="ct-header">
                                <span class="ct-h-product">Product</span>
                                <span class="ct-h-price">Price</span>
                                <span class="ct-h-qty">Quantity</span>
                                <span class="ct-h-subtotal">Subtotal</span>
                                <span class="ct-h-action"></span>
                            </div>

                            <!-- Cart Items -->
                            <div class="ct-body">
                                <?php
                                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                        $product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                                        $thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                        $product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                        $product_subtotal  = apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                                ?>
                                        <div class="ct-item woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                                            <!-- Product Cell -->
                                            <div class="ct-product" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                                <div class="ct-product-img">
                                                    <?php
                                                    if ($product_permalink) {
                                                        echo '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
                                                    } else {
                                                        echo $thumbnail;
                                                    }
                                                    ?>
                                                </div>
                                                <div class="ct-product-info">
                                                    <h4>
                                                        <?php if ($product_permalink) : ?>
                                                            <a href="<?php echo esc_url($product_permalink); ?>"><?php echo wp_kses_post($product_name); ?></a>
                                                        <?php else : ?>
                                                            <?php echo wp_kses_post($product_name); ?>
                                                        <?php endif; ?>
                                                    </h4>
                                                    <div class="ct-product-meta">
                                                        <?php
                                                        // Show product categories
                                                        $cats = wc_get_product_category_list($product_id, ', ');
                                                        if ($cats) {
                                                            echo '<span><i class="fa-solid fa-tag"></i> ' . wp_kses_post($cats) . '</span>';
                                                        }
                                                        // Show SKU
                                                        if ($_product->get_sku()) {
                                                            echo '<span>SKU: ' . esc_html($_product->get_sku()) . '</span>';
                                                        }
                                                        // Show cart item data (variations, etc.)
                                                        echo wc_get_formatted_cart_item_data($cart_item);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Price Cell -->
                                            <div class="ct-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                                <?php echo $product_price; ?>
                                            </div>

                                            <!-- Quantity Cell -->
                                            <div class="ct-qty" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                                <?php
                                                if ($_product->is_sold_individually()) {
                                                    $min_quantity = 1;
                                                    $max_quantity = 1;
                                                } else {
                                                    $min_quantity = 0;
                                                    $max_quantity = $_product->get_max_purchase_quantity();
                                                }

                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $max_quantity,
                                                        'min_value'    => $min_quantity,
                                                        'product_name' => $product_name,
                                                        'classes'      => array('ct-qty-input'),
                                                    ),
                                                    $_product,
                                                    false
                                                );

                                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                                ?>
                                            </div>

                                            <!-- Subtotal Cell -->
                                            <div class="ct-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                                <?php echo $product_subtotal; ?>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="ct-remove-wrap">
                                                <?php
                                                echo apply_filters(
                                                    'woocommerce_cart_item_remove_link',
                                                    sprintf(
                                                        '<a href="%s" class="ct-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa-solid fa-xmark"></i></a>',
                                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                        esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), $product_name)),
                                                        esc_attr($product_id),
                                                        esc_attr($_product->get_sku())
                                                    ),
                                                    $cart_item_key
                                                );
                                                ?>
                                            </div>
                                        </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>

                            <!-- Cart Actions Row -->
                            <div class="ct-actions">
                                <div class="coupon-wrap">
                                    <?php if (wc_coupons_enabled()) : ?>
                                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                        <button type="submit" class="btn-coupon<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
                                            <?php esc_html_e('Apply Coupon', 'woocommerce'); ?>
                                        </button>
                                        <?php do_action('woocommerce_cart_coupon'); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="ct-action-btns">
                                    <button type="submit" class="btn-update<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                                        <i class="fa-solid fa-rotate"></i> <?php esc_html_e('Update Cart', 'woocommerce'); ?>
                                    </button>
                                    <?php do_action('woocommerce_cart_actions'); ?>
                                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT: Cart Totals Sidebar -->
                        <aside class="cart-totals">
                            <h3 class="ct-title">Cart Totals</h3>

                            <div class="ct-row">
                                <span><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
                                <span class="ct-val"><?php wc_cart_totals_subtotal_html(); ?></span>
                            </div>

                            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                                <div class="ct-row coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                                    <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
                                    <span class="ct-val"><?php wc_cart_totals_coupon_html($coupon); ?></span>
                                </div>
                            <?php endforeach; ?>

                            <div class="ct-row ct-shipping">
                                <span><?php esc_html_e('Shipping', 'woocommerce'); ?></span>
                                <div class="ct-shipping-options">
                                    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                                        <?php do_action('woocommerce_cart_totals_before_shipping'); ?>
                                        <?php wc_cart_totals_shipping_html(); ?>
                                        <?php do_action('woocommerce_cart_totals_after_shipping'); ?>
                                    <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
                                        <p class="shipping-note"><?php esc_html_e('Shipping costs will be calculated at checkout.', 'woocommerce'); ?></p>
                                    <?php else : ?>
                                        <label class="shipping-option">
                                            <span class="so-radio" style="border-color: var(--primary);position:relative;"><span style="position:absolute;top:3px;left:3px;width:8px;height:8px;border-radius:50%;background:var(--primary);"></span></span>
                                            <span class="so-label">Free Shipping</span>
                                            <span class="so-price"><?php echo function_exists('wc_price') ? wc_price(0) : 'KSh 0.00'; ?></span>
                                        </label>
                                        <p class="shipping-note">Shipping options will be updated during checkout.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                                <div class="ct-row fee">
                                    <span><?php echo esc_html($fee->name); ?></span>
                                    <span class="ct-val"><?php wc_cart_totals_fee_html($fee); ?></span>
                                </div>
                            <?php endforeach; ?>

                            <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                                <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                                    <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                                        <div class="ct-row tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                                            <span><?php echo esc_html($tax->label); ?></span>
                                            <span class="ct-val"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="ct-row tax-total">
                                        <span><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
                                        <span class="ct-val"><?php wc_cart_totals_taxes_total_html(); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php do_action('woocommerce_cart_totals_before_order_total'); ?>

                            <div class="ct-divider"></div>

                            <div class="ct-row ct-total-row">
                                <span><?php esc_html_e('Total', 'woocommerce'); ?></span>
                                <span class="ct-total-val"><?php wc_cart_totals_order_total_html(); ?></span>
                            </div>

                            <?php do_action('woocommerce_cart_totals_after_order_total'); ?>

                            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn-checkout">
                                <i class="fa-solid fa-lock"></i> <?php esc_html_e('Proceed to Checkout', 'woocommerce'); ?>
                            </a>

                            <div class="ct-secure">
                                <div class="secure-badges">
                                    <span><i class="fa-solid fa-shield-halved"></i> Secure Checkout</span>
                                    <span><i class="fa-solid fa-rotate-left"></i> 30 Day Returns</span>
                                </div>
                                <div class="payment-methods">
                                    <i class="fa-brands fa-cc-visa"></i>
                                    <i class="fa-brands fa-cc-mastercard"></i>
                                    <i class="fa-brands fa-cc-paypal"></i>
                                    <i class="fa-brands fa-cc-apple-pay"></i>
                                    <i class="fa-brands fa-cc-amex"></i>
                                </div>
                            </div>

                            <!-- Continue Shopping -->
                            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="ct-continue">
                                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
                            </a>
                        </aside>
                    </div>

                    <?php do_action('woocommerce_after_cart_table'); ?>
                </form>

            </div>
        <?php endif; ?>

    </div>
</section>

<!-- ===== FEATURES BAR ===== -->
<section class="features-bar">
    <div class="container">
        <div class="features-row">
            <div class="feat-item">
                <div class="feat-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div>
                    <h4>Free Shipping</h4>
                    <p>On orders over <?php echo function_exists('wc_price') ? wc_price(75000) : 'KSh 75,000'; ?></p>
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

<?php do_action('woocommerce_after_cart'); ?>
