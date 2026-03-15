<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <!-- ===== TOP BAR ===== -->
    <?php if (zendotech_get_header_option('topbar_enabled') === '1'): ?>
        <div class="top-bar">
            <div class="container top-bar-inner">
                <span
                    class="welcome-text"><?php echo zendotech_header_parse(zendotech_get_header_option('topbar_text')); ?></span>
                <div class="top-bar-right">
                    <ul class="top-links">
                        <?php
                        $top_links = zendotech_get_header_option('topbar_links');
                        if (!empty($top_links)):
                            foreach ($top_links as $link): 
                                $url = esc_url($link['url']);
                                // Fix #12: Dynamically set Track Order URL if WooCommerce is active
                                if (strtolower($link['text']) === 'track order' && function_exists('wc_get_page_permalink')) {
                                    $url = esc_url(wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount')));
                                }
                                ?>
                                <li><a href="<?php echo $url; ?>">
                                        <?php if (!empty($link['icon'])): ?><i class="<?php echo esc_attr($link['icon']); ?>"></i>
                                        <?php endif; ?>
                                        <?php echo esc_html($link['text']); ?>
                                    </a></li>
                            <?php endforeach;
                        endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ===== MAIN HEADER ===== -->
    <header
        class="main-header <?php echo (zendotech_get_header_option('sticky_header') === '1') ? 'is-sticky-enabled' : ''; ?>">
        <div class="container header-inner">
            <div class="mobile-nav-trigger">
                <button id="mobileMenuOpen" aria-label="Open Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

            <?php zendotech_the_logo('logo'); ?>


            <div class="search-bar-wrap" id="headerSearchBar">
                <form class="search-bar" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                    <?php if (zendotech_get_header_option('show_search_categories') === '1'): ?>
                        <div class="search-category">
                            <span>All Categories</span>
                            <i class="fa-solid fa-angle-down"></i>
                        </div>
                    <?php endif; ?>
                    <input type="text"
                        placeholder="<?php echo esc_attr(zendotech_get_header_option('search_placeholder')); ?>"
                        name="s" value="<?php echo get_search_query(); ?>">
                    <input type="hidden" name="post_type" value="product">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="header-actions">
                <?php if (zendotech_get_header_option('show_wishlist') === '1'): ?>
                    <a href="#" class="h-action" title="Wishlist" id="headerWishlistBtn">
                        <i class="fa-regular fa-heart"></i>
                        <span class="badge-count">0</span>
                    </a>
                <?php endif; ?>

                <?php if (zendotech_get_header_option('show_compare') === '1'): ?>
                    <a href="#" class="h-action" title="Compare" id="headerCompareBtn">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        <span class="badge-count">0</span>
                    </a>
                <?php endif; ?>

                <div class="mini-cart-wrapper">
                    <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#'; ?>"
                        class="h-action cart-action" title="Bag">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span
                            class="badge-count cart-count-fragment"><?php echo function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : '0'; ?></span>
                        <span
                            class="cart-label cart-total-fragment"><?php echo function_exists('WC') && WC()->cart ? WC()->cart->get_cart_total() : '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">KSh</span>&nbsp;0.00</bdi></span>'; ?></span>
                    </a>

                    <div id="miniCartPanel" class="mini-cart-panel">
                        <div class="mc-header">
                            <h4><i class="fa-solid fa-bag-shopping"></i> Shopping Cart</h4>
                            <button class="mc-close-btn" title="Close"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="mc-body mini-cart-content-fragment">
                            <?php echo zendotech_get_mini_cart_content(); ?>
                        </div>
                        <div class="mc-footer">
                            <div class="mc-subtotal">
                                <div class="mc-subtotal-row">
                                    <span
                                        class="mc-count mc-count-fragment"><?php echo WC()->cart->get_cart_contents_count(); ?>
                                        item<?php echo (WC()->cart->get_cart_contents_count() !== 1 ? 's' : ''); ?></span>
                                    <span class="mc-subtotal-label">Subtotal</span>
                                </div>
                                <span
                                    class="mc-subtotal-val mc-subtotal-val-fragment"><?php echo WC()->cart->get_cart_total(); ?></span>
                            </div>
                            <div class="mc-shipping-note">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: var(--text-muted);">
                                    <input type="checkbox" id="mcShippingCheck" style="margin: 0;">
                                    <span><i class="fa-solid fa-truck-fast" style="margin-right: 4px;"></i> Free shipping on orders over <?php echo function_exists('wc_price') ? wc_price(75000) : 'KSh 75,000'; ?></span>
                                </label>
                            </div>
                            <div class="mc-actions">
                                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="mc-btn mc-btn-outline">View
                                    Cart</a>
                                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
                                    class="mc-btn mc-btn-primary">Checkout <i class="fa-solid fa-lock"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== MOBILE DRAWER ===== -->
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="drawer-overlay" id="drawerOverlay"></div>
        <div class="drawer-content">
            <div class="drawer-header">
                <span class="drawer-logo"><?php bloginfo('name'); ?></span>
                <button class="drawer-close" id="mobileMenuClose"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="drawer-body">
                <div class="drawer-search">
                    <form action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <input type="text" placeholder="Search products..." name="s">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <div class="mobile-links">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'mobile-nav-menu',
                            'depth' => 2,
                        ));
                    }
                    ?>
                </div>

                <div class="drawer-section">
                    <h4>Header Actions</h4>
                    <ul class="mobile-action-links">
                        <li><a href="#" id="mobileWishlistBtn"><i class="fa-regular fa-heart"></i> Wishlist (<span class="badge-count">0</span>)</a></li>
                        <li><a href="#" id="mobileCompareBtn"><i class="fa-solid fa-arrow-right-arrow-left"></i> Compare (<span class="badge-count">0</span>)</a></li>
                        <li><a href="<?php echo wc_get_cart_url(); ?>"><i class="fa-solid fa-bag-shopping"></i> My Cart</a></li>
                    </ul>
                </div>

                <div class="drawer-section">
                    <h4>Categories</h4>
                    <ul class="mobile-cat-list">
                        <?php
                        if (taxonomy_exists('product_cat')) {
                            $cats = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => false,
                                'number' => 10,
                                'exclude' => array(get_option('default_product_cat')),
                            ));
                            if (!is_wp_error($cats)) {
                                foreach ($cats as $cat) {
                                    echo '<li><a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></li>';
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="drawer-footer">
                <a href="<?php echo esc_url(zendotech_get_header_option('nav_cta_url')); ?>" class="drawer-cta">
                    <i class="<?php echo esc_attr(zendotech_get_header_option('nav_cta_icon')); ?>"></i>
                    <?php echo esc_html(zendotech_get_header_option('nav_cta_text')); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- ===== NAVIGATION ===== -->
    <nav class="main-nav">
        <div class="container nav-inner">
            <div class="categories-trigger">
                <i class="fa-solid fa-grip"></i>
                <span><?php echo esc_html(zendotech_get_header_option('nav_cat_label')); ?></span>
                <i class="fa-solid fa-chevron-down chevron"></i>

                <div class="header-cat-dropdown">
                    <ul>
                        <?php
                        if (taxonomy_exists('product_cat')) {
                            $icon_map = array(
                                'headphones' => 'fa-headphones',
                                'speakers' => 'fa-volume-high',
                                'microphones' => 'fa-microphone-lines',
                                'guitars' => 'fa-guitar',
                                'keyboards' => 'fa-music',
                                'studio-gear' => 'fa-sliders',
                                'turntables' => 'fa-record-vinyl',
                                'drums' => 'fa-drum',
                                'accessories' => 'fa-bolt',
                                'earbuds' => 'fa-headphones',
                                'soundbars' => 'fa-volume-high',
                                'monitors' => 'fa-desktop',
                                'mixers' => 'fa-sliders',
                                'studio-equipment' => 'fa-music',
                                'equilizers' => 'fa-chart-simple',
                                'crossovers' => 'fa-shuffle',
                                'generators' => 'fa-bolt-lightning',
                                'amplifiers' => 'fa-plug',
                                'sound-racks' => 'fa-server',
                                'saxaphones' => 'fa-music',
                                'video-accessories' => 'fa-video',
                                'megaphones' => 'fa-bullhorn',
                            );

                            if (!function_exists('zendotech_render_cat_list')) {
                                function zendotech_render_cat_list($parent_id = 0, $icon_map = array())
                                {
                                    $terms = get_terms(array(
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => false,
                                        'parent' => $parent_id,
                                        'exclude' => array(get_option('default_product_cat')),
                                    ));

                                    if (is_wp_error($terms) || empty($terms))
                                        return;

                                    foreach ($terms as $term) {
                                        $children = get_terms(array(
                                            'taxonomy' => 'product_cat',
                                            'hide_empty' => false,
                                            'parent' => $term->term_id,
                                        ));

                                        $has_children = !empty($children) && !is_wp_error($children);
                                        $icon = isset($icon_map[$term->slug]) ? $icon_map[$term->slug] : ($parent_id == 0 ? 'fa-tag' : '');

                                        echo '<li class="' . ($has_children ? 'has-children' : '') . '">';
                                        echo '<a href="' . esc_url(get_term_link($term)) . '">';
                                        if ($icon)
                                            echo '<i class="fa-solid ' . esc_attr($icon) . '"></i> ';
                                        echo esc_html($term->name);
                                        if ($has_children)
                                            echo '<i class="fa-solid fa-chevron-right arrow"></i>';
                                        echo '</a>';

                                        if ($has_children) {
                                            echo '<ul class="sub-menu">';
                                            zendotech_render_cat_list($term->term_id, $icon_map);
                                            echo '</ul>';
                                        }
                                        echo '</li>';
                                    }
                                }
                            }

                            zendotech_render_cat_list(0, $icon_map);
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-menu',
                    'container' => false,
                    'fallback_cb' => false,
                    'depth' => 2,
                ));
            } else {
                // Fallback: auto-generate menu from WC pages + categories
                ?>
                <ul class="nav-menu">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"
                            class="<?php echo is_front_page() ? 'active' : ''; ?>">Home</a></li>
                    <?php if (function_exists('wc_get_page_permalink')): ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                                class="<?php echo (function_exists('is_shop') && is_shop()) ? 'active' : ''; ?>">All
                                Products</a></li>
                    <?php endif; ?>
                    <?php
                    // Top-level categories
                    if (taxonomy_exists('product_cat')) {
                        $top_cats = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                            'number' => 5,
                            'exclude' => array(get_option('default_product_cat')),
                        ));
                        if (!is_wp_error($top_cats)) {
                            foreach ($top_cats as $tc) {
                                echo '<li><a href="' . esc_url(get_term_link($tc)) . '">' . esc_html($tc->name) . '</a></li>';
                            }
                        }
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
            <div class="nav-right">
                <a href="<?php echo esc_url(zendotech_get_header_option('nav_cta_url')); ?>">
                    <?php if (zendotech_get_header_option('nav_cta_icon')): ?>
                        <i class="<?php echo esc_attr(zendotech_get_header_option('nav_cta_icon')); ?>"></i>
                    <?php endif; ?>
                    <?php echo esc_html(zendotech_get_header_option('nav_cta_text')); ?>
                </a>
            </div>
        </div>
    </nav>