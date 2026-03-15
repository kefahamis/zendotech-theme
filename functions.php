<?php
/**
 * Zendotech Audio Theme Functions
 * WooCommerce + Elementor Integration
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ============================================
   1. THEME SETUP
   ============================================ */
function zendotech_theme_setup()
{
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    // Custom Logo
    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));

    // WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Navigation Menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'zendotech'),
        'footer_category' => __('Footer Category Menu', 'zendotech'),
        'footer_customer' => __('Footer Customer Care Menu', 'zendotech'),
    ));

    // HTML5 Support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Elementor support
    add_theme_support('elementor');
}
add_action('after_setup_theme', 'zendotech_theme_setup');

/* ============================================
   2. ENQUEUE SCRIPTS & STYLES
   ============================================ */
function zendotech_enqueue_scripts()
{
    // Google Fonts
    wp_enqueue_style('zendotech-google-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap', array(), null);

    // Font Awesome
    wp_enqueue_style('zendotech-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

    // Swiper Slider
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);

    // Main Stylesheet
    wp_enqueue_style('zendotech-main-style', get_template_directory_uri() . '/assets/css/style.css', array(), '1.0.6');

    // Page-specific styles
    if (is_page_template('page-shop.php') || (function_exists('is_shop') && is_shop()) || (function_exists('is_product_category') && is_product_category())) {
        wp_enqueue_style('zendotech-shop-style', get_template_directory_uri() . '/assets/css/shop.css', array(), '1.0.1');
        wp_enqueue_script('zendotech-shop-js', get_template_directory_uri() . '/assets/js/shop.js', array(), '1.0.1', true);
    }

    if (function_exists('is_product') && is_product()) {
        wp_enqueue_style('zendotech-product-style', get_template_directory_uri() . '/assets/css/product.css', array(), '1.0.1');
        wp_enqueue_script('zendotech-product-js', get_template_directory_uri() . '/assets/js/product.js', array('zendotech-app-js'), '1.0.1', true);
    }

    if (function_exists('is_cart') && is_cart()) {
        wp_enqueue_style('zendotech-cart-style', get_template_directory_uri() . '/assets/css/cart.css', array(), '1.0.1');
        wp_enqueue_script('zendotech-cart-js', get_template_directory_uri() . '/assets/js/cart.js', array('zendotech-app-js'), '1.0.1', true);
    }

    if (function_exists('is_checkout') && is_checkout()) {
        wp_enqueue_script('zendotech-checkout-js', get_template_directory_uri() . '/assets/js/checkout.js', array('zendotech-app-js'), '1.0.1', true);
    }

    if (function_exists('is_account_page') && is_account_page()) {
        wp_enqueue_style('zendotech-account-style', get_template_directory_uri() . '/assets/css/account.css', array(), '1.0.1');
        wp_enqueue_script('zendotech-account-js', get_template_directory_uri() . '/assets/js/account.js', array(), '1.0.1', true);
    }

    // Cart fragments for header/mini-cart updates (we handle add-to-cart ourselves in app.js)
    if (function_exists('WC')) {
        wp_enqueue_script('wc-cart-fragments');
    }

    // Main App JS (depends on jQuery so WC events work)
    wp_enqueue_script('zendotech-app-js', get_template_directory_uri() . '/assets/js/app.js', array('jquery'), '1.0.1', true);

    // Pass dynamic data to JS
    $categories = array();
    if (function_exists('WC') && taxonomy_exists('product_cat')) {
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'exclude' => array(get_option('default_product_cat')), // exclude "Uncategorized"
        ));
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[] = array(
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'url' => get_term_link($term),
                );
            }
        }
    }

    wp_localize_script('zendotech-app-js', 'zendotechData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'wcAjaxUrl' => class_exists('WC_AJAX') ? WC_AJAX::get_endpoint('%%endpoint%%') : '',
        'shopUrl' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'),
        'cartUrl' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
        'checkoutUrl' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
        'homeUrl' => home_url('/'),
        'categories' => $categories,
        'nonce' => wp_create_nonce('zendotech-add-to-cart'),
    ));
}
add_action('wp_enqueue_scripts', 'zendotech_enqueue_scripts');

/* ============================================
   2B. AJAX ADD-TO-CART HANDLER
   Bridges the JS mini-cart with WooCommerce
   ============================================ */
function zendotech_ajax_add_to_cart()
{
    // Prefer nonce; if missing/expired (e.g. cached page or guest), allow when request looks same-site + valid product
    $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
    $nonce_ok = ($nonce !== '' && wp_verify_nonce($nonce, 'zendotech-add-to-cart'));

    if (!$nonce_ok) {
        $product_id_raw = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : (isset($_POST['add-to-cart']) ? $_POST['add-to-cart'] : 0);
        $product_id_check = absint($product_id_raw);
        $referer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'])) : '';
        $site_host = wp_parse_url(home_url(), PHP_URL_HOST);
        $referer_host = $site_host && $referer ? wp_parse_url($referer, PHP_URL_HOST) : '';
        $same_site = ($site_host && $referer_host && strtolower($referer_host) === strtolower($site_host));
        $valid_product = $product_id_check && wc_get_product($product_id_check);
        if (!$same_site || !$valid_product) {
            wp_send_json_error(array('message' => 'Session expired. Please refresh the page and try again.'));
        }
    }

    if (!function_exists('WC')) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    // Ensure session/cart exist (critical for guest users and admin-ajax)
    if (WC()->session && method_exists(WC()->session, 'set_customer_session_cookie')) {
        WC()->session->set_customer_session_cookie(true);
    }
    if (!WC()->cart) {
        wp_send_json_error(array('message' => 'Cart is not available. Please refresh the page.'));
    }

    // Read from POST or REQUEST (some setups send as REQUEST)
    $product_id = 0;
    if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        $product_id = absint($_POST['product_id']);
    } elseif (isset($_POST['add-to-cart']) && is_numeric($_POST['add-to-cart'])) {
        $product_id = absint($_POST['add-to-cart']);
    } elseif (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id'])) {
        $product_id = absint($_REQUEST['product_id']);
    }
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;

    if (!$product_id) {
        wp_send_json_error(array('message' => 'Invalid product'));
    }

    $product = wc_get_product($product_id);
    if (!$product || !$product->exists()) {
        wp_send_json_error(array('message' => 'Product not found'));
    }
    if ($product->is_type('variable') && empty($variation_id)) {
        wp_send_json_error(array('message' => 'Please select product options before adding this product to your cart.'));
    }

    $variations = array();
    if (!empty($_POST) && is_array($_POST)) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variations[$key] = sanitize_text_field($value);
            }
        }
    }

    if ($variation_id) {
        $added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations);
    } else {
        $added = WC()->cart->add_to_cart($product_id, $quantity);
    }

    if (!$added) {
        $reason = '';
        if (function_exists('wc_get_notices')) {
            $notices = wc_get_notices('error');
            if (!empty($notices) && isset($notices[0]['notice'])) {
                $reason = wp_strip_all_tags($notices[0]['notice']);
            }
            wc_clear_notices();
        }
        wp_send_json_error(array('message' => $reason ? $reason : 'Could not add to cart.'));
    }

    // Build refreshed fragments so frontend JS can update WooCommerce fragments
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    $fragments = array(
        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
    );
    $fragments = array_merge($fragments, zendotech_cart_count_fragment(array()));

    $cart_hash = method_exists('WC_Cart', 'get_cart_hash') ? WC()->cart->get_cart_hash() : md5(json_encode(WC()->cart->get_cart()));

    wp_send_json_success(array('fragments' => $fragments, 'cart_hash' => $cart_hash));
}
add_action('wp_ajax_zendotech_add_to_cart', 'zendotech_ajax_add_to_cart');
add_action('wp_ajax_nopriv_zendotech_add_to_cart', 'zendotech_ajax_add_to_cart');

/**
 * AJAX: Get current WooCommerce cart contents for frontend sync
 */
function zendotech_get_cart()
{
    check_ajax_referer('zendotech-add-to-cart', 'nonce');

    if (!WC()) {
        wp_send_json_error(array('message' => 'WooCommerce not available'));
    }

    $cart = WC()->cart->get_cart();
    $items = array();
    foreach ($cart as $cart_item_key => $cart_item) {
        $product = wc_get_product($cart_item['product_id']);
        if (!$product) continue;
        $image = get_the_post_thumbnail_url($product->get_id(), 'thumbnail') ?: wc_placeholder_img_src();
        $items[] = array(
            'id' => (string) $product->get_id(),
            'key' => $cart_item_key,
            'name' => $product->get_name(),
            'price' => wc_get_price_to_display($product),
            'qty' => intval($cart_item['quantity']),
            'image' => $image,
            'variation' => isset($cart_item['variation']) ? $cart_item['variation'] : array(),
        );
    }

    $totals = array(
        'subtotal' => WC()->cart->get_cart_subtotal(),
        'total' => WC()->cart->get_total(),
        'cart_hash' => method_exists('WC_Cart', 'get_cart_hash') ? WC()->cart->get_cart_hash() : md5(json_encode(WC()->cart->get_cart())),
    );

    wp_send_json_success(array('items' => $items, 'totals' => $totals));
}
add_action('wp_ajax_zendotech_get_cart', 'zendotech_get_cart');
add_action('wp_ajax_nopriv_zendotech_get_cart', 'zendotech_get_cart');

// Temporary probe endpoint to verify admin-ajax.php accessibility (remove after debugging)
function zendotech_probe_ajax()
{
    // Return minimal success so client can verify admin-ajax is reachable and not blocked by server rules
    wp_send_json_success(array('probe' => true));
}
add_action('wp_ajax_zendotech_probe', 'zendotech_probe_ajax');
add_action('wp_ajax_nopriv_zendotech_probe', 'zendotech_probe_ajax');

// Temporary REST endpoint for diagnostics: /wp-json/zendotech/v1/probe
add_action('rest_api_init', function () {
    register_rest_route('zendotech/v1', '/probe', array(
        'methods' => 'GET',
        'callback' => function () {
            return rest_ensure_response(array('probe' => true, 'time' => current_time('mysql')));
        },
        'permission_callback' => '__return_true'
    ));
});

/* ============================================
   3. WOOCOMMERCE WRAPPER
   ============================================ */
function zendotech_woocommerce_wrapper_before()
{
    echo '<main id="primary" class="site-main"><div class="container">';
}
function zendotech_woocommerce_wrapper_after()
{
    echo '</div></main>';
}
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'zendotech_woocommerce_wrapper_before');
add_action('woocommerce_after_main_content', 'zendotech_woocommerce_wrapper_after');

// Remove default WooCommerce sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/* ============================================
   4. HELPER: CUSTOM LOGO OR FALLBACK
   ============================================ */
function zendotech_the_logo($class = 'logo')
{
    $home = esc_url(home_url('/'));
    if (has_custom_logo()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        echo '<a href="' . $home . '" class="' . esc_attr($class) . '">';
        echo '<img src="' . esc_url($logo_url) . '" alt="' . get_bloginfo('name') . '" class="logo-img">';
        echo '<span class="logo-text">' . get_bloginfo('name') . '</span>';
        echo '</a>';
    } else {
        echo '<a href="' . $home . '" class="' . esc_attr($class) . '">';
        echo '<span class="logo-text">' . get_bloginfo('name') . '</span>';
        echo '</a>';
    }
}

/* ============================================
   5. HELPER: PRODUCT CARD HTML
   ============================================ */
function zendotech_product_card($product)
{
    if (!$product || !is_a($product, 'WC_Product'))
        return;

    $id = $product->get_id();
    $name = $product->get_name();
    $permalink = get_permalink($id);
    $image = get_the_post_thumbnail_url($id, 'medium') ?: wc_placeholder_img_src('medium');
    $price_html = $product->get_price_html();
    $rating = $product->get_average_rating();
    $review_count = $product->get_review_count();
    $on_sale = $product->is_on_sale();
    $is_new = (strtotime($product->get_date_created()) > strtotime('-30 days'));
    $categories = wc_get_product_category_list($id, ', ');
    $cat_names = wp_strip_all_tags($categories);
    $first_cat = explode(',', $cat_names)[0];

    // Sale percentage
    $sale_pct = '';
    if ($on_sale && $product->get_regular_price() && $product->get_sale_price()) {
        $pct = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
        $sale_pct = '-' . $pct . '%';
    }
    ?>
    <div class="product-card" data-product-id="<?php echo esc_attr($id); ?>">
        <div class="pc-img">
            <?php if ($on_sale && $sale_pct): ?>
                <span class="sale-tag"><?php echo esc_html($sale_pct); ?></span>
            <?php elseif ($is_new): ?>
                <span class="new-tag">New</span>
            <?php endif; ?>
            <a href="<?php echo esc_url($permalink); ?>">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>">
            </a>
            <div class="pc-overlay">
                <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                <button title="Quick View" data-product-url="<?php echo esc_url($permalink); ?>"><i
                        class="fa-regular fa-eye"></i></button>
                <button title="Compare"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
            </div>
        </div>
        <div class="pc-body">
            <span class="pc-cat"><?php echo esc_html($first_cat); ?></span>
            <h4><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($name); ?></a></h4>
            <?php if ($rating > 0): ?>
                <div class="pc-stars">
                    <?php
                    $full = floor($rating);
                    $half = ($rating - $full >= 0.25) ? 1 : 0;
                    $empty = 5 - $full - $half;
                    for ($i = 0; $i < $full; $i++)
                        echo '<i class="fa-solid fa-star"></i>';
                    if ($half)
                        echo '<i class="fa-solid fa-star-half-stroke"></i>';
                    for ($i = 0; $i < $empty; $i++)
                        echo '<i class="fa-regular fa-star"></i>';
                    ?>
                    <span>(<?php echo esc_html($review_count); ?>)</span>
                </div>
            <?php endif; ?>
            <div class="pc-pricing"><?php echo $price_html; ?></div>
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                class="atc-btn add_to_cart_button ajax_add_to_cart product_type_simple"
                data-product_id="<?php echo esc_attr($id); ?>"
                data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1"
                aria-label="Add <?php echo esc_attr($name); ?> to cart">
                <i class="fa-solid fa-cart-shopping"></i> Add to Cart
            </a>
        </div>
    </div>
    <?php
}

/* ============================================
   6. PRODUCT & CATEGORY IMPORT ON ACTIVATION
   ============================================ */
function zendotech_import_products()
{
    if (!function_exists('WC') || !class_exists('WC_Product_Simple'))
        return;

    // ---- CATEGORIES ----
    $cat_data = array(
        'Headphones' => 'headphones',
        'Speakers' => 'speakers',
        'Microphones' => 'microphones',
        'Guitars' => 'guitars',
        'Keyboards' => 'keyboards',
        'Studio Gear' => 'studio-gear',
        'Turntables' => 'turntables',
        'Drums' => 'drums',
        'Earbuds' => 'earbuds',
        'Soundbars' => 'soundbars',
        'Monitors' => 'monitors',
        'Accessories' => 'accessories',
    );

    $cat_ids = array();
    foreach ($cat_data as $name => $slug) {
        $existing = term_exists($slug, 'product_cat');
        if ($existing) {
            $cat_ids[$slug] = $existing['term_id'];
        } else {
            $result = wp_insert_term($name, 'product_cat', array('slug' => $slug));
            if (!is_wp_error($result)) {
                $cat_ids[$slug] = $result['term_id'];
            }
        }
    }

    // ---- ALL PRODUCTS FROM index.html ----
    $products = array(
        // === DEALS OF THE DAY ===
        array('name' => 'Beyerdynamic DT 900 Pro X Studio', 'price' => '209', 'sale' => '299', 'cat' => 'headphones', 'img' => 'https://images.unsplash.com/photo-1545127398-14699f92334b?w=600&h=600&fit=crop', 'desc' => 'Open-back studio headphones with STELLAR.45 driver for accurate studio sound.', 'rating' => 4.5, 'reviews' => 142),
        array('name' => 'JBL Flip 6 Portable Bluetooth Speaker', 'price' => '129', 'cat' => 'speakers', 'img' => 'https://images.unsplash.com/photo-1558537348-c0f8e733989d?w=600&h=600&fit=crop', 'desc' => 'Bold sound, deep bass, IP67 waterproof and dustproof portable speaker.', 'rating' => 5.0, 'reviews' => 287),
        array('name' => 'Blue Yeti X USB Condenser Mic', 'price' => '149', 'cat' => 'microphones', 'img' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=600&h=600&fit=crop', 'desc' => 'Professional USB condenser microphone for streaming, podcasting, and recording.', 'rating' => 4.0, 'reviews' => 198),
        array('name' => 'Fender Player Stratocaster Electric', 'price' => '679', 'sale' => '799', 'cat' => 'guitars', 'img' => 'https://images.unsplash.com/photo-1550291652-6ea9114a47b1?w=600&h=600&fit=crop', 'desc' => 'Classic Stratocaster tone with modern playability and alder body.', 'rating' => 5.0, 'reviews' => 76),
        array('name' => 'Audio-Technica AT-LP120X Direct-Drive', 'price' => '249', 'cat' => 'turntables', 'img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=600&h=600&fit=crop', 'desc' => 'Fully manual direct-drive turntable with USB output for digitizing records.', 'rating' => 4.5, 'reviews' => 112),

        // === POPULAR PRODUCTS ===
        array('name' => 'Apple AirPods Pro (2nd Gen) USB-C', 'price' => '249', 'cat' => 'earbuds', 'img' => 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=600&h=600&fit=crop', 'desc' => 'Active noise cancellation with adaptive transparency and personalized spatial audio.', 'rating' => 4.5, 'reviews' => 312),
        array('name' => 'Taylor 214ce Acoustic-Electric Guitar', 'price' => '1099', 'sale' => '1249', 'cat' => 'guitars', 'img' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=600&h=600&fit=crop', 'desc' => 'Grand Auditorium body with Sitka spruce top and ES2 electronics.', 'rating' => 5.0, 'reviews' => 64),
        array('name' => 'Sonos Arc Premium Dolby Atmos', 'price' => '899', 'cat' => 'soundbars', 'img' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=600&h=600&fit=crop', 'desc' => 'Premium smart soundbar with Dolby Atmos, Trueplay tuning and voice control.', 'rating' => 4.0, 'reviews' => 89),
        array('name' => 'Akai MPK Mini MK3 MIDI Controller', 'price' => '119', 'cat' => 'studio-gear', 'img' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=600&h=600&fit=crop', 'desc' => '25-key USB MIDI keyboard controller with MPC drum pads and joystick.', 'rating' => 5.0, 'reviews' => 245),
        array('name' => 'Yamaha HS5 Powered Studio Monitor', 'price' => '199', 'cat' => 'monitors', 'img' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=600&h=600&fit=crop', 'desc' => '5-inch 2-way bass-reflex bi-amplified nearfield studio monitor with flat response.', 'rating' => 4.5, 'reviews' => 167),

        // === NEW ARRIVALS ===
        array('name' => 'Yamaha P-125 Digital Piano 88 Key', 'price' => '649', 'cat' => 'keyboards', 'img' => 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=600&h=600&fit=crop', 'desc' => '88-key weighted action digital piano with GHS keyboard and Pure CF Sound Engine.', 'rating' => 5.0, 'reviews' => 93),
        array('name' => 'Bose QuietComfort Ultra Over-Ear', 'price' => '349', 'sale' => '429', 'cat' => 'headphones', 'img' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=600&h=600&fit=crop', 'desc' => 'World-class noise cancellation with immersive spatial audio and 24-hour battery.', 'rating' => 4.5, 'reviews' => 208),
        array('name' => 'Focusrite Scarlett 2i2 4th Gen USB', 'price' => '189', 'cat' => 'studio-gear', 'img' => 'https://images.unsplash.com/photo-1598653222000-6b7b7a552625?w=600&h=600&fit=crop', 'desc' => 'USB-C audio interface with Air mode for bright, open recordings and ultra-low latency.', 'rating' => 5.0, 'reviews' => 378),
        array('name' => 'Roland TD-17KVX Electronic Drum Kit', 'price' => '1499', 'cat' => 'drums', 'img' => 'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?w=600&h=600&fit=crop', 'desc' => 'V-Drums kit with mesh pads, Bluetooth audio streaming, and 50 preset kits.', 'rating' => 4.0, 'reviews' => 54),
        array('name' => 'Crosley C200 Belt-Drive Turntable', 'price' => '179', 'cat' => 'turntables', 'img' => 'https://images.unsplash.com/photo-1539375665275-f9de415ef9ac?w=600&h=600&fit=crop', 'desc' => 'Two-speed belt-driven turntable with S-shaped tonearm and adjustable counterweight.', 'rating' => 4.5, 'reviews' => 132),

        // === HERO / BANNER FEATURED ===
        array('name' => 'Sony WH-1000XM5 Wireless', 'price' => '348', 'cat' => 'headphones', 'img' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=600&h=600&fit=crop', 'desc' => 'Industry-leading noise cancellation with exceptional sound quality and 30-hour battery.', 'rating' => 4.5, 'reviews' => 245),
        array('name' => 'Marshall Stanmore III Bluetooth Speaker', 'price' => '379', 'cat' => 'speakers', 'img' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=600&h=600&fit=crop', 'desc' => 'Iconic design meets modern wireless audio with Playfinity and Dynamic Loudness.', 'rating' => 4.5, 'reviews' => 156),

        // === EXTRA PRODUCTS FOR VARIETY ===
        array('name' => 'Sennheiser Momentum 4 Wireless', 'price' => '379', 'cat' => 'headphones', 'img' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=600&fit=crop', 'desc' => 'Superior sound with adaptive noise cancellation and 60-hour battery life.', 'rating' => 4.5, 'reviews' => 167),
        array('name' => 'Audio-Technica ATH-M50x', 'price' => '169', 'cat' => 'headphones', 'img' => 'https://images.unsplash.com/photo-1487215078519-e21cc028cb29?w=600&h=600&fit=crop', 'desc' => 'Professional studio monitor headphones with 45mm large-aperture drivers.', 'rating' => 4.5, 'reviews' => 425),
        array('name' => 'Shure SM7B Dynamic Microphone', 'price' => '399', 'cat' => 'microphones', 'img' => 'https://images.unsplash.com/photo-1598653222000-6b7b7a552625?w=600&h=600&fit=crop', 'desc' => 'Professional dynamic microphone for broadcast, podcast and recording studios.', 'rating' => 5.0, 'reviews' => 512),
    );

    $imported = 0;
    foreach ($products as $p) {
        // Check if product already exists using WP_Query
        $existing_query = new WP_Query(array(
            'post_type' => 'product',
            'title' => $p['name'],
            'posts_per_page' => 1,
            'fields' => 'ids',
        ));
        if ($existing_query->have_posts()) {
            wp_reset_postdata();
            continue;
        }
        wp_reset_postdata();

        $product = new WC_Product_Simple();
        $product->set_name($p['name']);
        $product->set_regular_price(isset($p['sale']) ? $p['sale'] : $p['price']);
        if (isset($p['sale'])) {
            $product->set_sale_price($p['price']);
        }
        $product->set_description($p['desc']);
        $product->set_short_description($p['desc']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        $product->set_average_rating($p['rating']);
        $product->set_reviews_allowed(true);

        // Set category
        if (isset($cat_ids[$p['cat']])) {
            $product->set_category_ids(array($cat_ids[$p['cat']]));
        }

        $product_id = $product->save();

        // Set featured image from URL
        if ($product_id && !empty($p['img'])) {
            zendotech_set_product_image($product_id, $p['img'], $p['name']);
        }

        $imported++;
    }

    update_option('zendotech_products_imported', true);
    return $imported;
}
add_action('after_switch_theme', 'zendotech_import_products');

/**
 * Admin page to manually trigger product import
 * Added under Tools AND as a top-level menu for visibility
 */
function zendotech_import_admin_menu()
{
    // Under Tools menu
    add_management_page(
        'Import ZendoTech Products',
        'Import Products',
        'manage_options',
        'zendotech-import',
        'zendotech_import_admin_page'
    );

    // Also as a top-level menu with icon
    add_menu_page(
        'Import ZendoTech Products',
        'Import Products',
        'manage_options',
        'zendotech-import-main',
        'zendotech_import_admin_page',
        'dashicons-download',
        58
    );
}
add_action('admin_menu', 'zendotech_import_admin_menu');

// Auto-enable WooCommerce AJAX add-to-cart on archives
function zendotech_enable_ajax_add_to_cart()
{
    if (function_exists('WC') && get_option('woocommerce_enable_ajax_add_to_cart') !== 'yes') {
        update_option('woocommerce_enable_ajax_add_to_cart', 'yes');
    }
}
add_action('init', 'zendotech_enable_ajax_add_to_cart');
/**
 * Search form customize
 */
add_filter('get_search_form', 'my_search_form');

function my_search_form($text)
{
    $text = str_replace('value="Search"', 'value="&#xf002;"', $text);
    return $text;
}

// Check for newly created inc directory files
if ( file_exists( get_template_directory() . '/inc/payment-setup.php' ) ) {
    require_once get_template_directory() . '/inc/payment-setup.php';
}

// Set currency to Kenyan Shilling (KSh)
function zendotech_set_currency($currency)
{
    return 'KES';
}
add_filter('woocommerce_currency', 'zendotech_set_currency', 999);

function zendotech_currency_symbol($symbol, $currency)
{
    if ($currency === 'KES') {
        return 'KSh';
    }
    return $symbol;
}
add_filter('woocommerce_currency_symbol', 'zendotech_currency_symbol', 999, 2);

function zendotech_import_admin_page()
{
    $message = '';
    if (isset($_POST['zendotech_run_import']) && check_admin_referer('zendotech_import_nonce')) {
        // Reset the flag so import can run again
        delete_option('zendotech_products_imported');
        $count = zendotech_import_products();
        $message = $count > 0 ? "Successfully imported {$count} new products!" : "All products already exist. No new products imported.";
    }
    ?>
    <div class="wrap">
        <h1>Import ZendoTech Products</h1>
        <?php if ($message): ?>
            <div class="notice notice-success">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>
        <p>Click the button below to import all demo products and categories into WooCommerce.</p>
        <p><strong>Note:</strong> Existing products will not be duplicated.</p>
        <form method="post">
            <?php wp_nonce_field('zendotech_import_nonce'); ?>
            <p><input type="submit" name="zendotech_run_import" class="button button-primary" value="Import Products Now">
            </p>
        </form>
    </div>
    <?php
}

/**
 * Helper: Download and set product featured image
 */
function zendotech_set_product_image($product_id, $image_url, $title)
{
    if (!function_exists('media_sideload_image')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

    $attach_id = media_sideload_image($image_url, $product_id, sanitize_title($title), 'id');
    if (!is_wp_error($attach_id)) {
        set_post_thumbnail($product_id, $attach_id);
    }
}

/* ============================================
   7. WOOCOMMERCE: UPDATE CART COUNT VIA AJAX
   ============================================ */

/**
 * Render mini-cart content (shared between initial load and AJAX)
 */
function zendotech_get_mini_cart_content()
{
    ob_start();
    $cart_items = WC()->cart->get_cart();
    if (empty($cart_items)): ?>
        <div class="mc-empty-inner">
            <div class="mc-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <p>Your cart is empty</p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="mc-shop-link">Start Shopping <i
                    class="fa-solid fa-arrow-right"></i></a>
        </div>
    <?php else:
        foreach ($cart_items as $cart_item_key => $cart_item):
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)):
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>
                <div class="mc-item">
                    <div class="mc-item-img">
                        <?php echo $_product->get_image(); ?>
                    </div>
                    <div class="mc-item-info">
                        <h5 class="mc-item-name">
                            <?php if (!$product_permalink):
                                echo wp_kses_post($_product->get_name());
                            else: ?>
                                <a href="<?php echo esc_url($product_permalink); ?>"><?php echo wp_kses_post($_product->get_name()); ?></a>
                            <?php endif; ?>
                        </h5>
                        <div class="mc-item-bottom">
                            <div class="mc-qty-wrap">
                                <span class="mc-qty-val">Qty: <?php echo $cart_item['quantity']; ?></span>
                            </div>
                            <span
                                class="mc-item-price"><?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?></span>
                        </div>
                    </div>
                    <?php
                    echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                        '<a href="%s" class="mc-item-remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><i class="fa-solid fa-trash-can"></i></a>',
                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                        esc_attr__('Remove this item', 'woocommerce'),
                        esc_attr($product_id),
                        esc_attr($cart_item_key),
                        esc_attr($_product->get_sku())
                    ), $cart_item_key);
                    ?>
                </div>
                <?php
            endif;
        endforeach;
    endif;
    return ob_get_clean();
}

function zendotech_cart_count_fragment($fragments)
{
    ob_start();
    ?>
    <span class="badge-count cart-count-fragment"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count-fragment'] = ob_get_clean();

    ob_start();
    ?>
                        <span
                            class="cart-label cart-total-fragment"><?php echo function_exists('WC') && WC()->cart ? WC()->cart->get_cart_total() : '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">KSh</span>&nbsp;0.00</bdi></span>'; ?></span>
    <?php
    $fragments['.cart-total-fragment'] = ob_get_clean();

    // Mini-cart items fragment
    $fragments['.mini-cart-content-fragment'] = zendotech_get_mini_cart_content();

    // New: Mini-cart subtotal fragment
    $fragments['.mc-subtotal-val-fragment'] = WC()->cart->get_cart_total();
    $fragments['.mc-count-fragment'] = WC()->cart->get_cart_contents_count() . ' item' . (WC()->cart->get_cart_contents_count() !== 1 ? 's' : '');

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'zendotech_cart_count_fragment');

/* ============================================
   8. ELEMENTOR: SUPPORT FOR PAGES & PRODUCTS
   ============================================ */
function zendotech_elementor_support()
{
    if (did_action('elementor/loaded')) {
        add_post_type_support('page', 'elementor');
        add_post_type_support('product', 'elementor');
    }
}
add_action('init', 'zendotech_elementor_support');

/* ============================================
   9. FOOTER BUILDER — CUSTOM ADMIN PAGE
   ============================================ */
require_once get_template_directory() . '/inc/footer-builder.php';
require_once get_template_directory() . '/inc/header-builder.php';

/* ============================================
   10. ELEMENTOR: CUSTOM WIDGETS
   ============================================ */
function zendotech_load_elementor_widgets()
{
    if (did_action('elementor/loaded')) {
        require_once get_template_directory() . '/inc/elementor-widgets.php';
    }
}
add_action('after_setup_theme', 'zendotech_load_elementor_widgets');

/* ============================================
   11. ELEMENTOR: FRONT PAGE AUTO-SETUP
   ============================================ */
require_once get_template_directory() . '/inc/elementor-setup.php';

/* ============================================
   12. QUICK VIEW: AJAX HANDLER
   ============================================ */
function zendotech_ajax_quick_view()
{
    if (!isset($_POST['product_id']))
        wp_send_json_error('Missing Product ID');

    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product)
        wp_send_json_error('Product not found');

    // Get Data
    $name = $product->get_name();
    $sku = $product->get_sku() ?: 'N/A';
    $price_html = $product->get_price_html();
    $short_desc = $product->get_short_description();
    $rating_html = wc_get_rating_html($product->get_average_rating());
    $review_count = $product->get_review_count();
    $stock_status = $product->get_stock_status();
    $stock_label = ($stock_status == 'instock') ? 'In Stock' : 'Out of Stock';
    $stock_class = ($stock_status == 'instock') ? 'qv-in-stock' : 'qv-out-stock';

    // Categories
    $categories = wc_get_product_category_list($product_id, ', ');

    // Images
    $main_img_id = get_post_thumbnail_id($product_id);
    $main_url = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'large') : wc_placeholder_img_src('large');
    $attachment_ids = $product->get_gallery_image_ids();
    
    $gallery = array();
    if ($main_url) {
        $gallery[] = $main_url;
    }
    
    foreach ($attachment_ids as $id) {
        $url = wp_get_attachment_image_url($id, 'large');
        if ($url && !in_array($url, $gallery)) {
            $gallery[] = $url;
        }
    }
    
    // De-duplicate URLs again just to be safe
    $gallery = array_unique($gallery);

    ob_start();
    ?>
    <div class="qv-grid">
        <div class="qv-gallery">
            <div class="qv-main-image">
                <img src="<?php echo esc_url($main_url); ?>" alt="<?php echo esc_attr($name); ?>" id="qvMainImg">
                <?php if (count($gallery) > 1): ?>
                    <button class="qv-nav qv-prev"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="qv-nav qv-next"><i class="fa-solid fa-chevron-right"></i></button>
                <?php endif; ?>
            </div>
            <?php if (count($gallery) > 1): ?>
                <div class="qv-thumbnails">
                    <?php foreach ($gallery as $i => $url): ?>
                        <div class="qv-thumb <?php echo $i === 0 ? 'active' : ''; ?>" data-url="<?php echo esc_url($url); ?>">
                            <img src="<?php echo esc_url($url); ?>" alt="Thumbnail">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="qv-details">
            <h2 class="qv-title"><?php echo esc_html($name); ?></h2>
            <div class="qv-meta-top">
                <span class="qv-sku-label">SKU: <?php echo esc_html($sku); ?></span>
            </div>

            <div class="qv-rating-row">
                <?php echo $rating_html; ?>
                <span class="qv-review-count"><?php echo $review_count; ?>
                    Review<?php echo $review_count != 1 ? 's' : ''; ?></span>
            </div>

            <div class="qv-status">
                <span class="qv-status-badge <?php echo esc_attr($stock_class); ?>">
                    <i class="fa-solid fa-circle-check"></i> <?php echo esc_html($stock_label); ?>
                </span>
            </div>

            <div class="qv-price-large"><?php echo $price_html; ?></div>

            <div class="qv-desc"><?php echo $short_desc; ?></div>

            <div class="qv-actions-box">
                <div class="qv-qty-wrapper">
                    <div class="qv-qty-input">
                        <button type="button" class="qv-qty-minus">-</button>
                        <input type="number" id="qvQty" value="1" min="1" max="100">
                        <button type="button" class="qv-qty-plus">+</button>
                    </div>
                </div>
                <button type="button" class="qv-add-btn ajax_add_to_cart" data-product_id="<?php echo $product_id; ?>">
                    Add to cart
                </button>
            </div>

            <div class="qv-extra-actions">
                <button type="button" class="qv-wishlist-btn"><i class="fa-regular fa-heart"></i> Add to wishlist</button>
                <button type="button" class="qv-compare-btn"><i class="fa-solid fa-arrow-right-arrow-left"></i>
                    Compare</button>
            </div>

            <div class="qv-info-box">
                <div class="qv-info-item">
                    <i class="fa-solid fa-box-open"></i>
                    <div>
                        <strong>2-day Delivery</strong>
                        <span>Speedy and reliable parcel delivery!</span>
                    </div>
                </div>
            </div>

            <div class="qv-meta-bottom">
                <span><strong>Category:</strong> <?php echo $categories; ?></span>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    wp_send_json_success($html);
}
add_action('wp_ajax_zendotech_quick_view', 'zendotech_ajax_quick_view');
add_action('wp_ajax_nopriv_zendotech_quick_view', 'zendotech_ajax_quick_view');

/* ============================================
   AJAX SEARCH AUTOCOMPLETE
   ============================================ */
function zendotech_ajax_search() {
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

    if (strlen($keyword) < 2) {
        wp_send_json_error('Keyword too short');
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        's'              => $keyword,
    );

    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $query = new WP_Query($args);
    
    if (!$query->have_posts()) {
        wp_send_json_error('No results');
    }

    ob_start();
    echo '<ul class="asr-list">';
    while ($query->have_posts()) {
        $query->the_post();
        global $product;
        
        $img = wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail');
        ?>
        <li class="asr-item">
            <a href="<?php the_permalink(); ?>">
                <div class="asr-img"><img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>"></div>
                <div class="asr-info">
                    <span class="asr-title"><?php the_title(); ?></span>
                    <span class="asr-price"><?php echo $product->get_price_html(); ?></span>
                </div>
            </a>
        </li>
        <?php
    }
    echo '</ul>';
    $html = ob_get_clean();

    wp_reset_postdata();
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_zendotech_ajax_search', 'zendotech_ajax_search');
add_action('wp_ajax_nopriv_zendotech_ajax_search', 'zendotech_ajax_search');

/* ============================================
   13. SHOP: AJAX FILTER HANDLER
   ============================================ */
function zendotech_ajax_shop_filter()
{
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'default';
    $cat_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
    $min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
    $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : 999999;
    $brands = isset($_POST['brands']) ? array_map('sanitize_text_field', $_POST['brands']) : array();
    $status = isset($_POST['status']) ? array_map('sanitize_text_field', $_POST['status']) : array();
    $rating = isset($_POST['rating']) ? array_map('intval', $_POST['rating']) : array();

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
        'paged' => $paged,
        'post_status' => 'publish',
    );

    // Sorting
    switch ($orderby) {
        case 'price-low':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-high':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    // Tax Query (Category)
    $tax_query = array('relation' => 'AND');
    if ($cat_id > 0) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $cat_id,
        );
    }

    // Meta Query (Price, Brands, Rating)
    $meta_query = array('relation' => 'AND');

    // Price
    $meta_query[] = array(
        'key' => '_price',
        'value' => array($min_price, $max_price),
        'compare' => 'BETWEEN',
        'type' => 'NUMERIC'
    );

    // Brands (Custom implementation for the mockup brands)
    if (!empty($brands)) {
        // Since brands might be attributes or custom fields, we'll try to find them in the title or a hypothetical 'brand' field
        // For this theme, we'll assume they are stored in a meta key 'zendotech_brand'
        $meta_query[] = array(
            'key' => 'zendotech_brand',
            'value' => $brands,
            'compare' => 'IN'
        );
    }

    // Status Filters
    if (in_array('on_sale', $status)) {
        $product_ids_on_sale = wc_get_product_ids_on_sale();
        $args['post__in'] = !empty($product_ids_on_sale) ? $product_ids_on_sale : array(0);
    }
    if (in_array('instock', $status)) {
        $meta_query[] = array(
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '='
        );
    }

    // Ratings
    if (!empty($rating)) {
        $meta_query[] = array(
            'key' => '_wc_average_rating',
            'value' => $rating,
            'compare' => 'IN',
            'type' => 'NUMERIC'
        );
    }

    $args['meta_query'] = $meta_query;
    $args['tax_query'] = $tax_query;

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if ($product) {
                zendotech_product_card($product);
            }
        }
    } else {
        echo '<div class="no-products"><p>No products found matching your selection.</p></div>';
    }
    $grid_html = ob_get_clean();

    ob_start();
    if ($query->max_num_pages > 1) {
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => $paged,
            'format' => '?paged=%#%',
            'prev_text' => '<i class="fa-solid fa-chevron-left"></i>',
            'next_text' => '<i class="fa-solid fa-chevron-right"></i>',
            'prev_next' => true,
        ));
    }
    $pagination_html = ob_get_clean();

    $start = ($paged - 1) * 12 + 1;
    $end = min($paged * 12, $query->found_posts);
    $count_text = sprintf('Showing <strong>%d–%d</strong> of <strong>%d</strong> results', $start, $end, $query->found_posts);

    wp_reset_postdata();

    wp_send_json_success(array(
        'grid' => $grid_html,
        'pagination' => $pagination_html,
        'countText' => $count_text,
        'found' => $query->found_posts
    ));
}
add_action('wp_ajax_zendotech_ajax_shop_filter', 'zendotech_ajax_shop_filter');
add_action('wp_ajax_nopriv_zendotech_ajax_shop_filter', 'zendotech_ajax_shop_filter');
