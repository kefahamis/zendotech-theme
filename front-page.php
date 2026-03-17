<?php get_header(); ?>

<!-- ===== MAIN CONTENT ===== -->
<main>
    <?php
    // If the page is built with Elementor, render Elementor content
    if (
        did_action('elementor/loaded')
        && \Elementor\Plugin::$instance->documents->get(get_the_ID())
        && \Elementor\Plugin::$instance->documents->get(get_the_ID())->is_built_with_elementor()
    ) {
        the_content();
    } else {
        // ---- FALLBACK: Original hardcoded layout ----
        ?>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-grid">
                    <!-- Categories Sidebar (Dynamic) -->
                    <aside class="hero-sidebar">
                        <ul class="sidebar-menu">
                            <?php
                            if (taxonomy_exists('product_cat')) {
                                $icon_map = array(
                                    'headphones' => 'fa-headphones',
                                    'speakers' => 'fa-volume-high',
                                    'turntables' => 'fa-record-vinyl',
                                    'guitars' => 'fa-guitar',
                                    'microphones' => 'fa-microphone',
                                    'studio-gear' => 'fa-sliders',
                                    'keyboards' => 'fa-keyboard',
                                    'drums' => 'fa-drum',
                                    'earbuds' => 'fa-headphones',
                                    'soundbars' => 'fa-volume-high',
                                    'monitors' => 'fa-desktop',
                                    'accessories' => 'fa-bolt',
                                );
                            $cats = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => false,
                                'parent' => 0,
                                'exclude' => array(get_option('default_product_cat')),
                            ));
                                if (!is_wp_error($cats)) {
                                    $count = 0;
                                    foreach ($cats as $cat) {
                                        $count++;
                                        $icon = isset($icon_map[$cat->slug]) ? $icon_map[$cat->slug] : 'fa-tag';
                                        $li_class = ($count > 9) ? 'extra-cat' : '';
                                        echo '<li class="' . esc_attr($li_class) . '"><a href="' . esc_url(get_term_link($cat)) . '"><i class="fa-solid ' . esc_attr($icon) . '"></i> ' . esc_html($cat->name) . '</a></li>';
                                    }
                                }
                            }
                            ?>
                            <?php
                            $shop_url = function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : esc_url(home_url('/shop'));

                            if (!is_wp_error($cats) && count($cats) > 9): ?>
                                <li class="view-more-cats-sidebar">
                                    <a href="<?php echo $shop_url; ?>">
                                        <i class="fa-solid fa-plus"></i> View More Categories
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </aside>

                    <!-- Main Banner -->
                    <!-- Main Banner Slider -->
                    <div class="main-banner swiper heroSlider">
                        <div class="swiper-wrapper">
                            <?php
                            $fetch_limit = 8;
                            $featured_prods = wc_get_products(array('featured' => true, 'limit' => $fetch_limit, 'status' => 'publish'));
                            if (count($featured_prods) < 3) {
                                $latest_prods = wc_get_products(array('limit' => 5, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish'));
                                $featured_prods = array_unique(array_merge($featured_prods, $latest_prods), SORT_REGULAR);
                            }

                            if (!empty($featured_prods)):
                                $slide_idx = 0;
                                foreach ($featured_prods as $fp):
                                    $slide_idx++;
                                    $title = $fp->get_name();
                                    $desc = wp_trim_words($fp->get_short_description(), 12);
                                    $price_html = $fp->get_price_html();
                                    $btn_url = get_permalink($fp->get_id());
                                    $img_url = get_the_post_thumbnail_url($fp->get_id(), 'medium_large') ?: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=400&fit=crop';
                                    $bg_class = 'bg-variant-' . (($slide_idx % 3) + 1);
                                    ?>
                                    <div class="swiper-slide">
                                        <div class="banner-bg <?php echo esc_attr($bg_class); ?>">
                                            <div class="banner-img-container" data-swiper-parallax="-300" data-swiper-parallax-duration="1000">
                                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>"
                                                    class="banner-hero-img">
                                            </div>
                                            <div class="banner-content" data-swiper-parallax="-500" data-swiper-parallax-duration="1200">
                                                <span class="banner-tag" data-swiper-parallax="-100">New Arrival</span>
                                                <h1 data-swiper-parallax="-200"><?php echo esc_html($title); ?></h1>
                                                <p data-swiper-parallax="-300"><?php echo esc_html($desc); ?></p>
                                                <div class="banner-cta" data-swiper-parallax="-400">
                                                    <a href="<?php echo esc_url($btn_url); ?>" class="btn btn-primary">Shop Now <i
                                                            class="fa-solid fa-arrow-right"></i></a>
                                                    <span class="banner-price">From
                                                        <strong><?php echo $price_html; ?></strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="swiper-slide">
                                    <div class="banner-bg">
                                        <div class="banner-img-container">
                                            <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=400&fit=crop"
                                                alt="Premium Audio" class="banner-hero-img">
                                        </div>
                                        <div class="banner-content">
                                            <span class="banner-tag">New Arrival</span>
                                            <h1>Premium Audio Gear</h1>
                                            <p>Shop the latest in headphones, speakers, and instruments.</p>
                                            <div class="banner-cta">
                                                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                                                    class="btn btn-primary">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Swiper Navigation -->
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        <!-- Swiper Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                    <!-- Side Banners -->
                    <div class="side-banners">
                        <?php
                        $side_products = wc_get_products(array('limit' => 2, 'orderby' => 'rand', 'status' => 'publish'));
                        if (count($side_products) >= 2):
                            ?>
                            <div class="side-card side-yellow">
                                <div class="sc-text">
                                    <span class="sc-tag">Best Seller</span>
                                    <h4><?php echo esc_html(wp_trim_words($side_products[0]->get_name(), 3)); ?></h4>
                                    <p class="sc-price">From <strong><?php echo $side_products[0]->get_price_html(); ?></strong>
                                    </p>
                                </div>
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url($side_products[0]->get_id(), 'medium') ?: 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=200&h=200&fit=crop'); ?>"
                                    alt="<?php echo esc_attr($side_products[0]->get_name()); ?>">
                            </div>
                            <div class="side-card side-blue">
                                <div class="sc-text">
                                    <span class="sc-tag">New</span>
                                    <h4><?php echo esc_html(wp_trim_words($side_products[1]->get_name(), 3)); ?></h4>
                                    <p class="sc-price">From <strong><?php echo $side_products[1]->get_price_html(); ?></strong>
                                    </p>
                                </div>
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url($side_products[1]->get_id(), 'medium') ?: 'https://images.unsplash.com/photo-1539375665275-f9de415ef9ac?w=200&h=200&fit=crop'); ?>"
                                    alt="<?php echo esc_attr($side_products[1]->get_name()); ?>">
                            </div>
                        <?php else: ?>
                            <div class="side-card side-yellow">
                                <div class="sc-text"><span class="sc-tag">Best Seller</span>
                                    <h4>Marshall Speaker</h4>
                                    <p class="sc-price">Save <strong>$60</strong></p>
                                </div>
                                <img src="https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=200&h=200&fit=crop"
                                    alt="Marshall Speaker">
                            </div>
                            <div class="side-card side-blue">
                                <div class="sc-text"><span class="sc-tag">New</span>
                                    <h4>Audio-Technica LP</h4>
                                    <p class="sc-price">From <strong>$299</strong></p>
                                </div>
                                <img src="https://images.unsplash.com/photo-1539375665275-f9de415ef9ac?w=200&h=200&fit=crop"
                                    alt="Turntable">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deals of the Day (On-Sale Products) -->
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div class="sh-left">
                        <h2>Deals of the Day</h2>
                        <div class="countdown">
                            <span class="cd-label">Ends in:</span>
                            <div class="cd-box"><span>04</span><small>hrs</small></div>
                            <div class="cd-sep">:</div>
                            <div class="cd-box"><span>32</span><small>min</small></div>
                            <div class="cd-sep">:</div>
                            <div class="cd-box"><span>18</span><small>sec</small></div>
                        </div>
                    </div>
                    <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"
                        class="link-arrow">View All Deals <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="products-row">
                    <?php
                    $on_sale_ids = wc_get_product_ids_on_sale();
                    if (!empty($on_sale_ids)) {
                        $sale_query = new WP_Query(array(
                            'post_type' => 'product',
                            'posts_per_page' => 5,
                            'post__in' => $on_sale_ids,
                            'orderby' => 'rand',
                        ));
                        while ($sale_query->have_posts()) {
                            $sale_query->the_post();
                            zendotech_product_card(wc_get_product(get_the_ID()));
                        }
                        wp_reset_postdata();
                    } else {
                        // Fallback: show latest products
                        $latest = wc_get_products(array('limit' => 5, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish'));
                        foreach ($latest as $lp) {
                            zendotech_product_card($lp);
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Promo Banner Strip -->
        <section class="promo-strip">
            <div class="container">
                <div class="promo-inner">
                    <div class="promo-left">
                        <i class="fa-solid fa-music"></i>
                        <div>
                            <h3>Free guitar strings pack with every instrument order — limited time only!</h3>
                        </div>
                    </div>
                    <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"
                        class="btn btn-white">Grab Yours <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </section>

        <!-- Shop by Category (Dynamic) -->
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <h2>Shop by Category</h2>
                </div>
                <div class="categories-grid">
                    <?php
                    if (taxonomy_exists('product_cat')) {
                        $home_cats = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                            'number' => 6,
                            'exclude' => array(get_option('default_product_cat')),
                        ));
                        if (!is_wp_error($home_cats)) {
                            foreach ($home_cats as $hc) {
                                $thumb_id = get_term_meta($hc->term_id, 'thumbnail_id', true);
                                $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop';
                                echo '<a href="' . esc_url(get_term_link($hc)) . '" class="cat-card">';
                                echo '<div class="cat-icon"><img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($hc->name) . '"></div>';
                                echo '<span>' . esc_html($hc->name) . '</span>';
                                echo '</a>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Dual Brand Banners -->
        <section class="section pt-0">
            <div class="container">
                <div class="dual-grid">
                    <div class="brand-card bc-gradient-1">
                        <div class="bc-info">
                            <span class="bc-brand">Bose</span>
                            <h3>QuietComfort Ultra</h3>
                            <p>Immersive spatial audio. Silence the world.</p>
                            <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"
                                class="btn btn-outline-white">Shop Now</a>
                        </div>
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=350&fit=crop"
                            alt="Bose Headphones">
                    </div>
                    <div class="brand-card bc-gradient-2">
                        <div class="bc-info">
                            <span class="bc-brand">Marshall</span>
                            <h3>Stanmore III</h3>
                            <p>Iconic design meets modern wireless audio.</p>
                            <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"
                                class="btn btn-outline-white">Shop Now</a>
                        </div>
                        <img src="https://images.unsplash.com/photo-1507667522877-ad03f0c7b0e0?w=400&h=350&fit=crop"
                            alt="Marshall Speaker">
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Products -->
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <h2>Popular Products</h2>
                    <div class="tab-filters">
                        <button class="active">All</button>
                        <?php
                        if (taxonomy_exists('product_cat')) {
                            $filter_cats = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => true,
                                'number' => 4,
                                'exclude' => array(get_option('default_product_cat')),
                            ));
                            if (!is_wp_error($filter_cats)) {
                                foreach ($filter_cats as $fcx) {
                                    echo '<button>' . esc_html($fcx->name) . '</button>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="products-row">
                    <?php
                    $popular = wc_get_products(array(
                        'limit' => 5,
                        'orderby' => 'popularity',
                        'order' => 'DESC',
                        'status' => 'publish',
                    ));
                    if (empty($popular)) {
                        $popular = wc_get_products(array('limit' => 5, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish'));
                    }
                    foreach ($popular as $pp) {
                        zendotech_product_card($pp);
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Tri Banners -->
        <section class="section pt-0">
            <div class="container">
                <div class="tri-grid">
                    <div class="tri-card tri-pink">
                        <div class="tri-info"><span>Best Sellers</span>
                            <h3>Wireless Earbuds</h3><a
                                href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>">Shop
                                Now <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                        <img src="https://images.unsplash.com/photo-1590658268037-6bf12f032f55?w=200&h=200&fit=crop"
                            alt="Earbuds">
                    </div>
                    <div class="tri-card tri-gold">
                        <div class="tri-info"><span>Top Rated</span>
                            <h3>Vinyl & Records</h3><a
                                href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>">Shop
                                Now <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                        <img src="https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=200&h=200&fit=crop"
                            alt="Vinyl Records">
                    </div>
                    <div class="tri-card tri-cyan">
                        <div class="tri-info"><span>New Arrivals</span>
                            <h3>DJ Equipment</h3><a
                                href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>">Shop
                                Now <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                        <img src="https://images.unsplash.com/photo-1571330735066-03aaa9429d89?w=200&h=200&fit=crop"
                            alt="DJ Equipment">
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals -->
        <section class="section new-arrivals-section">
            <div class="container">
                <div class="section-head">
                    <h2>New Arrivals</h2>
                    <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"
                        class="link-arrow">View All <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>
                <div class="products-row">
                    <?php
                    $new_arrivals = wc_get_products(array(
                        'limit' => 5,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'status' => 'publish',
                    ));
                    foreach ($new_arrivals as $na) {
                        zendotech_product_card($na);
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Newsletter CTA Strip -->
        <section class="newsletter-strip">
            <div class="container">
                <div class="ns-inner">
                    <div class="ns-content">
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=120&h=120&fit=crop"
                            alt="Headphones" class="ns-img">
                        <div>
                            <h3>Exclusive deals for audiophiles — up to 40% off premium audio gear!</h3>
                            <p>Subscribe and never miss a beat.</p>
                        </div>
                    </div>
                    <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"
                        class="btn btn-primary">See All Deals</a>
                </div>
            </div>
        </section>

        <!-- Brand Logos -->
        <section class="brands-section">
            <div class="container">
                <div class="brand-logos">
                    <div class="brand-logo"><span
                            style="font-size:18px;font-weight:800;color:#333;letter-spacing:2px">BOSE</span></div>
                    <div class="brand-logo"><span
                            style="font-size:18px;font-weight:700;color:#333;letter-spacing:1px;font-style:italic">Sennheiser</span>
                    </div>
                    <div class="brand-logo"><span
                            style="font-size:20px;font-weight:800;color:#333;letter-spacing:1px">SONY</span></div>
                    <div class="brand-logo"><span
                            style="font-size:18px;font-weight:700;color:#C41E3A;letter-spacing:2px;font-family:serif">Marshall</span>
                    </div>
                    <div class="brand-logo"><span
                            style="font-size:18px;font-weight:700;color:#333;letter-spacing:2px">JBL</span></div>
                    <div class="brand-logo"><span
                            style="font-size:16px;font-weight:700;color:#C30F1F;letter-spacing:1px">Fender</span></div>
                </div>
            </div>
        </section>

        <!-- Features -->
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
                            <h4>2-Year Warranty</h4>
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
    <?php } // end fallback ?>
</main>

<?php get_footer(); ?>
