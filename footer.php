<!-- ===== FOOTER ===== -->
<footer class="main-footer">
    <!-- Newsletter -->
    <?php if (is_front_page() && zendotech_get_footer_option('newsletter_enabled') === '1'): ?>
        <div class="footer-newsletter">
            <div class="container fn-inner">
                <div class="fn-text">
                    <?php $nl_icon = zendotech_get_footer_option('newsletter_icon'); ?>
                    <?php if ($nl_icon): ?><i class="<?php echo esc_attr($nl_icon); ?>"></i><?php endif; ?>
                    <div>
                        <h4><?php echo zendotech_footer_parse(zendotech_get_footer_option('newsletter_heading')); ?></h4>
                        <p><?php echo wp_kses_post(zendotech_footer_parse(zendotech_get_footer_option('newsletter_text'))); ?>
                        </p>
                    </div>
                </div>
                <form class="fn-form">
                    <input type="email"
                        placeholder="<?php echo esc_attr(zendotech_get_footer_option('newsletter_placeholder')); ?>">
                    <button
                        type="submit"><?php echo esc_html(zendotech_get_footer_option('newsletter_btn_text')); ?></button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="footer-bottom-section">
        <div class="container">
            <div class="footer-top">

                <!-- Column 1: Brand -->
                <div class="footer-col brand-col">
                    <?php zendotech_the_logo('footer-logo'); ?>
                    <p class="footer-desc">
                        <?php echo wp_kses_post(zendotech_get_footer_option('brand_description')); ?>
                    </p>
                    <div class="foot-contact">
                        <?php $contact_label = zendotech_get_footer_option('contact_label'); ?>
                        <?php $contact_phone = zendotech_get_footer_option('contact_phone'); ?>
                        <?php $contact_address = zendotech_get_footer_option('contact_address'); ?>

                        <?php if ($contact_label): ?>
                            <div class="fc-line"><i class="fa-solid fa-headset"></i>
                                <span><?php echo esc_html($contact_label); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($contact_phone): ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>"
                                class="fc-phone"><?php echo esc_html($contact_phone); ?></a>
                        <?php endif; ?>
                        <?php if ($contact_address): ?>
                            <div class="fc-line"><i class="fa-solid fa-location-dot"></i>
                                <span><?php echo esc_html($contact_address); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: Links -->
                <div class="footer-col">
                    <h4 class="footer-title"><?php echo esc_html(zendotech_get_footer_option('col2_title')); ?></h4>
                    <?php
                    $col2_source = zendotech_get_footer_option('col2_source');

                    if ($col2_source === 'auto') {
                        // Auto: WooCommerce product categories
                        echo '<ul class="footer-links">';
                        if (taxonomy_exists('product_cat')) {
                            $footer_cats = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => false,
                                'number' => 6,
                                'exclude' => array(get_option('default_product_cat')),
                            ));
                            if (!is_wp_error($footer_cats) && !empty($footer_cats)) {
                                foreach ($footer_cats as $fc) {
                                    echo '<li><a href="' . esc_url(get_term_link($fc)) . '">' . esc_html($fc->name) . '</a></li>';
                                }
                            }
                        }
                        echo '</ul>';
                    } elseif ($col2_source === 'menu') {
                        $col2_menu = zendotech_get_footer_option('col2_menu');
                        if ($col2_menu) {
                            wp_nav_menu(array(
                                'menu' => $col2_menu,
                                'menu_class' => 'footer-links',
                                'container' => false,
                                'depth' => 1,
                            ));
                        }
                    } else {
                        // Custom links
                        $col2_links = zendotech_get_footer_option('col2_links');
                        if (!empty($col2_links)) {
                            echo '<ul class="footer-links">';
                            foreach ($col2_links as $link) {
                                echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['text']) . '</a></li>';
                            }
                            echo '</ul>';
                        }
                    }
                    ?>
                </div>

                <!-- Column 3: Links -->
                <div class="footer-col">
                    <h4 class="footer-title"><?php echo esc_html(zendotech_get_footer_option('col3_title')); ?></h4>
                    <?php
                    $col3_source = zendotech_get_footer_option('col3_source');

                    if ($col3_source === 'menu') {
                        $col3_menu = zendotech_get_footer_option('col3_menu');
                        if ($col3_menu && has_nav_menu($col3_menu)) {
                            wp_nav_menu(array(
                                'theme_location' => $col3_menu,
                                'menu_class' => 'footer-links',
                                'container' => false,
                                'depth' => 1,
                            ));
                        } elseif ($col3_menu) {
                            wp_nav_menu(array(
                                'menu' => $col3_menu,
                                'menu_class' => 'footer-links',
                                'container' => false,
                                'depth' => 1,
                            ));
                        }
                    } elseif ($col3_source === 'auto') {
                        echo '<ul class="footer-links">';
                        if (taxonomy_exists('product_cat')) {
                            $footer_cats = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => false,
                                'number' => 6,
                                'exclude' => array(get_option('default_product_cat')),
                            ));
                            if (!is_wp_error($footer_cats) && !empty($footer_cats)) {
                                foreach ($footer_cats as $fc) {
                                    echo '<li><a href="' . esc_url(get_term_link($fc)) . '">' . esc_html($fc->name) . '</a></li>';
                                }
                            }
                        }
                        echo '</ul>';
                    } else {
                        // Custom links
                        $col3_links = zendotech_get_footer_option('col3_links');
                        if (!empty($col3_links)) {
                            echo '<ul class="footer-links">';
                            foreach ($col3_links as $link) {
                                echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['text']) . '</a></li>';
                            }
                            echo '</ul>';
                        }
                    }
                    ?>
                </div>

                <!-- Column 4: Social & Payment -->
                <div class="footer-col">
                    <?php
                    $social_title = zendotech_get_footer_option('social_title');
                    $social_links = zendotech_get_footer_option('social_links');
                    ?>

                    <?php if ($social_title): ?>
                        <h4 class="footer-title"><?php echo esc_html($social_title); ?></h4>
                    <?php endif; ?>

                    <?php if (!empty($social_links)): ?>
                        <div class="social-links">
                            <?php foreach ($social_links as $social): ?>
                                <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="<?php echo esc_attr($social['icon']); ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $payment_title = zendotech_get_footer_option('payment_title');
                    $payment_icons = zendotech_get_footer_option('payment_icons');
                    ?>

                    <?php if ($payment_title): ?>
                        <h4 class="footer-title" style="margin-top: 30px;"><?php echo esc_html($payment_title); ?></h4>
                    <?php endif; ?>

                    <?php if (!empty($payment_icons)): ?>
                        <div class="payment-methods">
                            <?php foreach ($payment_icons as $icon): ?>
                                <i class="<?php echo esc_attr($icon); ?>"></i>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p><?php echo wp_kses_post(zendotech_footer_parse(zendotech_get_footer_option('copyright_text'))); ?>
                </p>

                <?php $bottom_links = zendotech_get_footer_option('bottom_links'); ?>
                <?php if (!empty($bottom_links)): ?>
                    <div class="footer-bottom-links">
                        <?php foreach ($bottom_links as $bl): ?>
                            <a href="<?php echo esc_url($bl['url']); ?>"><?php echo esc_html($bl['text']); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="bn-item <?php echo is_front_page() ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
        class="bn-item <?php echo is_shop() ? 'active' : ''; ?>">
        <i class="fa-solid fa-store"></i>
        <span>Shop</span>
    </a>
    <a href="#" class="bn-item" id="mobileMenuOpenBottom">
        <i class="fa-solid fa-list-ul"></i>
        <span>Categories</span>
    </a>
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="bn-item bn-cart">
        <i class="fa-solid fa-bag-shopping"></i>
        <span class="badge-count cart-count-fragment"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        <span>Cart</span>
    </a>
</nav>

<?php wp_footer(); ?>
</body>

</html>