<?php
/**
 * WooCommerce Single Product Template
 * Matches the original product.html design exactly
 */

if (!defined('ABSPATH'))
    exit;

get_header();

while (have_posts()):
    the_post();
    global $product;

    if (!$product || !is_a($product, 'WC_Product')) {
        continue;
    }

    $product_id = $product->get_id();
    $gallery_ids = $product->get_gallery_image_ids();
    $main_image = get_the_post_thumbnail_url($product_id, 'large') ?: wc_placeholder_img_src('large');
    $categories = wc_get_product_category_list($product_id, ', ');
    $cat_plain = wp_strip_all_tags($categories);
    $first_cat = $cat_plain ? explode(',', $cat_plain)[0] : '';
    $rating = $product->get_average_rating();
    $review_count = $product->get_review_count();
    $on_sale = $product->is_on_sale();
    $in_stock = $product->is_in_stock();
    $stock_qty = $product->get_stock_quantity();
    $sku = $product->get_sku();
    $short_desc = $product->get_short_description();
    $full_desc = $product->get_description();
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $save_amount = ($regular_price && $sale_price) ? ($regular_price - $sale_price) : 0;
    $sale_percent = ($regular_price && $sale_price && $regular_price > 0) ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;

    // Get first category link
    $terms = get_the_terms($product_id, 'product_cat');
    $first_cat_link = ($terms && !is_wp_error($terms)) ? get_term_link($terms[0]) : wc_get_page_permalink('shop');
    $first_cat_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
    ?>

    <!-- ===== BREADCRUMB ===== -->
    <section class="breadcrumb-bar">
        <div class="container">
            <div class="breadcrumb-inner">
                <ol class="breadcrumb">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
                    <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Shop</a></li>
                    <?php if ($first_cat_name): ?>
                        <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
                        <li><a href="<?php echo esc_url($first_cat_link); ?>"><?php echo esc_html($first_cat_name); ?></a></li>
                    <?php endif; ?>
                    <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
                    <li class="current"><?php the_title(); ?></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- ===== SINGLE PRODUCT ===== -->
    <section class="product-section">
        <div class="container">
            <div class="product-top">
                <!-- Gallery -->
                <div class="product-gallery">
                    <div class="gallery-main" id="galleryMain">
                        <?php if ($on_sale && $sale_percent > 0): ?>
                            <span class="gallery-badge sale-tag">-<?php echo esc_html($sale_percent); ?>%</span>
                        <?php endif; ?>
                        <img id="mainImage" src="<?php echo esc_url($main_image); ?>" alt="<?php the_title_attribute(); ?>"
                            data-zoom-src="<?php echo esc_url(get_the_post_thumbnail_url($product_id, 'full') ?: $main_image); ?>">
                        <div class="zoom-lens" id="zoomLens"></div>
                        <button class="gallery-zoom" id="galleryZoomBtn" title="Zoom"><i class="fa-solid fa-expand"></i></button>
                    </div>
                    <?php if (!empty($gallery_ids)): ?>
                        <div class="gallery-thumbs">
                            <button class="thumb active" data-img="<?php echo esc_url($main_image); ?>">
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url($product_id, 'thumbnail')); ?>"
                                    alt="Main">
                            </button>
                            <?php foreach ($gallery_ids as $gid): ?>
                                <button class="thumb" data-img="<?php echo esc_url(wp_get_attachment_image_url($gid, 'large')); ?>">
                                    <img src="<?php echo esc_url(wp_get_attachment_image_url($gid, 'thumbnail')); ?>" alt="Gallery">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <div class="pi-brand-row">
                        <span class="pi-brand"><?php echo esc_html($first_cat_name ?: $cat_plain); ?></span>
                        <div class="pi-share">
                            <button title="Share"><i class="fa-solid fa-share-nodes"></i></button>
                            <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                        </div>
                    </div>

                    <h1 class="pi-title"><?php the_title(); ?></h1>

                    <?php if ($rating > 0): ?>
                        <div class="pi-rating">
                            <div class="stars">
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
                            </div>
                            <span class="rating-text"><?php echo esc_html(number_format($rating, 1)); ?></span>
                            <a href="#tab-reviews" class="review-count">(<?php echo esc_html($review_count); ?> Reviews)</a>
                        </div>
                    <?php endif; ?>

                    <div class="pi-price-block">
                        <?php if ($on_sale && $sale_price): ?>
                            <span class="pi-price"><?php echo wc_price($sale_price); ?></span>
                            <span class="pi-old-price"><?php echo wc_price($regular_price); ?></span>
                            <span class="pi-save">Save <?php echo wc_price($save_amount); ?></span>
                        <?php else: ?>
                            <span class="pi-price"><?php echo $product->get_price_html(); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($on_sale): ?>
                        <!-- Flash Sale -->
                        <div class="flash-sale-bar">
                            <div class="fsb-info">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Flash Sale ends in:</span>
                            </div>
                            <div class="fsb-timer" id="flashTimer">
                                <div class="cd-box"><span id="fsHours">04</span><small>hrs</small></div>
                                <div class="cd-sep">:</div>
                                <div class="cd-box"><span id="fsMinutes">32</span><small>min</small></div>
                                <div class="cd-sep">:</div>
                                <div class="cd-box"><span id="fsSeconds">18</span><small>sec</small></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Key Highlights from short description -->
                    <?php if ($short_desc): ?>
                        <ul class="pi-highlights">
                            <?php
                            // Parse short description into bullet points
                            $lines = preg_split('/[\n\r]+/', strip_tags($short_desc));
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if (!empty($line)) {
                                    echo '<li><i class="fa-solid fa-check"></i> ' . esc_html($line) . '</li>';
                                }
                            }
                            ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Quantity & Cart -->
                    <?php if ($in_stock): ?>
                        <form class="woocommerce-cart-form"
                            action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
                            method="post" enctype="multipart/form-data">
                            <div class="pi-cart-row">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" id="qtyMinus">−</button>
                                    <input type="number" name="quantity" id="qtyInput" value="1" min="1"
                                        max="<?php echo esc_attr($product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : 10); ?>">
                                    <button type="button" class="qty-btn" id="qtyPlus">+</button>
                                </div>
                                <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>"
                                    class="btn btn-primary btn-lg add-to-cart-btn">
                                    <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                                </button>
                                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-buy-now btn-lg">
                                    Buy Now
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>

                    <!-- Stock -->
                    <div class="pi-stock">
                        <?php if ($in_stock): ?>
                            <span class="in-stock"><i class="fa-solid fa-circle-check"></i> In Stock</span>
                            <?php if ($stock_qty): ?>
                                <span class="stock-note"><?php echo esc_html($stock_qty); ?> units available</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="in-stock" style="color: var(--cta);"><i class="fa-solid fa-circle-xmark"></i> Out of
                                Stock</span>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="pi-actions">
                        <?php 
                        $wa_text = rawurlencode('Hi, I would like more information about ' . get_the_title() . ' - ' . get_permalink());
                        ?>
                        <a href="https://wa.me/?text=<?php echo $wa_text; ?>" target="_blank" class="pia-link"><i class="fa-brands fa-whatsapp"></i> Request Information</a>
                        <a href="#" class="pia-link zendotech-compare-btn" 
                           data-product-id="<?php echo esc_attr($product_id); ?>"
                           data-product-name="<?php echo esc_attr(get_the_title()); ?>"
                           data-product-price="<?php echo esc_attr(strip_tags($product->get_price_html())); ?>"
                           data-product-image="<?php echo esc_attr($main_image); ?>">
                           <i class="fa-solid fa-shuffle"></i> Compare
                        </a>
                        <?php if ($sku): ?>
                            <span class="pia-link"><i class="fa-solid fa-barcode"></i> SKU: <?php echo esc_html($sku); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Guarantee Strip -->
                    <div class="pi-guarantees">
                        <div class="pg-item">
                            <i class="fa-solid fa-truck-fast"></i>
                            <div><strong>Free Shipping</strong><span>Orders over $75</span></div>
                        </div>
                        <div class="pg-item">
                            <i class="fa-solid fa-rotate-left"></i>
                            <div><strong>30-Day Returns</strong><span>Hassle-free</span></div>
                        </div>
                        <div class="pg-item">
                            <i class="fa-solid fa-shield-halved"></i>
                            <div><strong>2-Year Warranty</strong><span>Official</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== PRODUCT TABS ===== -->
            <div class="product-tabs">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="description">Description</button>
                    <button class="tab-btn" data-tab="specs">Specifications</button>
                    <button class="tab-btn" data-tab="delivery">Delivery & Return</button>
                    <button class="tab-btn" data-tab="reviews">Reviews (<?php echo esc_html($review_count); ?>)</button>
                </div>

                <!-- Description Tab -->
                <div class="tab-panel active" id="tab-description">
                    <div class="desc-content">
                        <div class="desc-text">
                            <?php if ($full_desc): ?>
                                <?php echo wp_kses_post($full_desc); ?>
                            <?php elseif ($short_desc): ?>
                                <?php echo wp_kses_post($short_desc); ?>
                            <?php else: ?>
                                <p>No description available yet.</p>
                            <?php endif; ?>
                        </div>
                        <?php
                        // Show product image in description
                        $desc_image = !empty($gallery_ids) ? wp_get_attachment_image_url($gallery_ids[0], 'large') : $main_image;
                        ?>
                        <div class="desc-image">
                            <img src="<?php echo esc_url($desc_image); ?>" alt="<?php the_title_attribute(); ?> Lifestyle">
                        </div>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-panel" id="tab-specs">
                    <div class="specs-grid">
                        <?php
                        $attributes = $product->get_attributes();
                        if (!empty($attributes)): ?>
                            <table class="spec-table">
                                <tbody>
                                    <?php foreach ($attributes as $attr): ?>
                                        <tr>
                                            <th><?php echo esc_html(wc_attribute_label($attr->get_name())); ?></th>
                                            <td><?php echo esc_html(implode(', ', $attr->get_options())); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($sku): ?>
                                        <tr>
                                            <th>SKU</th>
                                            <td><?php echo esc_html($sku); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Category</th>
                                        <td><?php echo esc_html($cat_plain); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <table class="spec-table">
                                <tbody>
                                    <tr>
                                        <th>Product Name</th>
                                        <td><?php the_title(); ?></td>
                                    </tr>
                                    <?php if ($sku): ?>
                                        <tr>
                                            <th>SKU</th>
                                            <td><?php echo esc_html($sku); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Category</th>
                                        <td><?php echo esc_html($cat_plain); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Weight</th>
                                        <td><?php echo $product->get_weight() ? esc_html($product->get_weight()) . ' kg' : 'N/A'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Dimensions</th>
                                        <td><?php $dims = $product->get_dimensions(false);
                                        echo !empty(array_filter($dims)) ? esc_html(wc_format_dimensions($dims)) : 'N/A'; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Delivery Tab -->
                <div class="tab-panel" id="tab-delivery">
                    <div class="delivery-content">
                        <div class="del-block">
                            <h4><i class="fa-solid fa-truck"></i> Delivery Information</h4>
                            <ul>
                                <li>Free standard shipping on orders over $75 (3–5 business days)</li>
                                <li>Express shipping available for $12.99 (1–2 business days)</li>
                                <li>Same-day delivery available in select metro areas for $19.99</li>
                                <li>International shipping available to 50+ countries</li>
                            </ul>
                        </div>
                        <div class="del-block">
                            <h4><i class="fa-solid fa-rotate-left"></i> Return Policy</h4>
                            <ul>
                                <li>30-day hassle-free return policy from date of delivery</li>
                                <li>Items must be returned in original packaging with all accessories</li>
                                <li>Refunds processed within 5–7 business days after receiving the return</li>
                                <li>For defective products, we cover return shipping costs</li>
                            </ul>
                        </div>
                        <div class="del-block">
                            <h4><i class="fa-solid fa-shield-halved"></i> Warranty</h4>
                            <ul>
                                <li>2-year manufacturer warranty against defects</li>
                                <li>Extended warranty options available at checkout</li>
                                <li>Warranty does not cover physical damage or water exposure</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-panel" id="tab-reviews">
                    <div class="reviews-content">
                        <?php if ($review_count > 0): ?>
                            <!-- Reviews Summary -->
                            <div class="reviews-summary">
                                <div class="rs-score">
                                    <span class="big-score"><?php echo esc_html(number_format($rating, 1)); ?></span>
                                    <div class="rs-stars">
                                        <?php
                                        for ($i = 0; $i < $full; $i++)
                                            echo '<i class="fa-solid fa-star"></i>';
                                        if ($half)
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        for ($i = 0; $i < $empty; $i++)
                                            echo '<i class="fa-regular fa-star"></i>';
                                        ?>
                                    </div>
                                    <span class="rs-total">Based on <?php echo esc_html($review_count); ?> reviews</span>
                                </div>
                                <div class="rs-bars">
                                    <?php
                                    // Get review star distribution
                                    $comments = get_comments(array(
                                        'post_id' => $product_id,
                                        'status' => 'approve',
                                        'type' => 'review',
                                    ));
                                    $star_counts = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
                                    foreach ($comments as $comment) {
                                        $star = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                                        if (isset($star_counts[$star])) {
                                            $star_counts[$star]++;
                                        }
                                    }
                                    $total_reviews = max(array_sum($star_counts), 1);

                                    for ($s = 5; $s >= 1; $s--):
                                        $pct = round(($star_counts[$s] / $total_reviews) * 100);
                                        ?>
                                        <div class="bar-row">
                                            <span><?php echo $s; ?> ★</span>
                                            <div class="bar-track">
                                                <div class="bar-fill" style="width:<?php echo esc_attr($pct); ?>%"></div>
                                            </div>
                                            <span><?php echo esc_html($star_counts[$s]); ?></span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Review Cards -->
                            <div class="reviews-list">
                                <?php foreach ($comments as $comment):
                                    $c_rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                                    $c_name = $comment->comment_author;
                                    $initials = '';
                                    $name_parts = explode(' ', $c_name);
                                    foreach ($name_parts as $part) {
                                        $initials .= strtoupper(substr($part, 0, 1));
                                    }
                                    $initials = substr($initials, 0, 2);
                                    ?>
                                    <div class="review-card">
                                        <div class="rc-header">
                                            <div class="rc-avatar"><?php echo esc_html($initials); ?></div>
                                            <div class="rc-meta">
                                                <strong><?php echo esc_html($c_name); ?></strong>
                                                <span
                                                    class="rc-date"><?php echo esc_html(get_comment_date('F j, Y', $comment)); ?></span>
                                            </div>
                                            <div class="rc-stars">
                                                <?php
                                                for ($i = 0; $i < $c_rating; $i++)
                                                    echo '<i class="fa-solid fa-star"></i>';
                                                for ($i = $c_rating; $i < 5; $i++)
                                                    echo '<i class="fa-regular fa-star"></i>';
                                                ?>
                                            </div>
                                            <span class="rc-verified"><i class="fa-solid fa-circle-check"></i> Verified
                                                Purchase</span>
                                        </div>
                                        <p class="rc-text"><?php echo esc_html($comment->comment_content); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="padding: 40px; text-align: center; color: var(--text-muted);">No reviews yet. Be the first
                                to review this product!</p>
                        <?php endif; ?>

                        <?php if (get_option('woocommerce_enable_reviews') === 'yes' && $product->get_reviews_allowed()): ?>
                            <?php comments_template(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ===== RELATED PRODUCTS ===== -->
            <?php
            $related_ids = wc_get_related_products($product_id, 4);
            $section_title = 'You May Also Like';

            if (empty($related_ids)) {
                // Fallback to latest products if no related found
                $section_title = 'Latest Products';
                $related_ids = get_posts(array(
                    'post_type' => 'product',
                    'posts_per_page' => 4,
                    'fields' => 'ids',
                    'post__not_in' => array($product_id),
                ));
            }

            if (!empty($related_ids)):
                ?>
                <div class="related-section">
                    <div class="section-head">
                        <h2><?php echo esc_html($section_title); ?></h2>
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="link-arrow">View All <i
                                class="fa-solid fa-arrow-right-long"></i></a>
                    </div>
                    <div class="products-row">
                        <?php
                        foreach ($related_ids as $rid) {
                            $related = wc_get_product($rid);
                            if ($related) {
                                zendotech_product_card($related);
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ===== UPSELL PRODUCTS ===== -->
            <?php
            $upsell_ids = $product->get_upsell_ids();
            if (!empty($upsell_ids)):
                ?>
                <div class="related-section upsell-section" style="margin-top: 50px;">
                    <div class="section-head">
                        <h2>Frequently Bought Together</h2>
                    </div>
                    <div class="products-row">
                        <?php
                        // Limit to 4 for design consistency
                        $upsell_ids = array_slice($upsell_ids, 0, 4);
                        foreach ($upsell_ids as $uid) {
                            $upsell_prod = wc_get_product($uid);
                            if ($upsell_prod) {
                                zendotech_product_card($upsell_prod);
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ===== CROSS-SELL PRODUCTS ===== -->
            <?php
            $cross_sell_ids = $product->get_cross_sell_ids();
            if (!empty($cross_sell_ids)):
                ?>
                <div class="related-section cross-sell-section" style="margin-top: 50px;">
                    <div class="section-head">
                        <h2>Recommended Accessories</h2>
                    </div>
                    <div class="products-row">
                        <?php
                        // Limit to 4 for design consistency
                        $cross_sell_ids = array_slice($cross_sell_ids, 0, 4);
                        foreach ($cross_sell_ids as $cid) {
                            $cross_prod = wc_get_product($cid);
                            if ($cross_prod) {
                                zendotech_product_card($cross_prod);
                            }
                        }
                        ?>
                    </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* ---- THUMBNAIL GALLERY ---- */
            const thumbs = document.querySelectorAll('.thumb');
            const mainImg = document.getElementById('mainImage');
            if (mainImg && thumbs.length) {
                thumbs.forEach(t => {
                    t.addEventListener('click', () => {
                        thumbs.forEach(th => th.classList.remove('active'));
                        t.classList.add('active');
                        // Smooth crossfade transition
                        mainImg.style.opacity = '0';
                        setTimeout(() => {
                            mainImg.src = t.dataset.img;
                            // Update zoom-src to use the same large image
                            const largeSrc = t.dataset.img.replace(/w=\d+/, 'w=1200').replace(/h=\d+/, 'h=1200');
                            mainImg.dataset.zoomSrc = t.dataset.zoomImg || largeSrc;
                            mainImg.style.opacity = '1';
                        }, 200);
                    });
                });
            }

            /* ---- INNER ZOOM ON HOVER ---- */
            const galleryMain = document.getElementById('galleryMain');
            const zoomLens = document.getElementById('zoomLens');
            if (galleryMain && mainImg && zoomLens) {
                const ZOOM_LEVEL = 2.5;

                galleryMain.addEventListener('mouseenter', function () {
                    const zoomSrc = mainImg.dataset.zoomSrc || mainImg.src;
                    zoomLens.style.backgroundImage = `url('${zoomSrc}')`;
                    zoomLens.classList.add('active');
                    mainImg.style.opacity = '0.4';
                });

                galleryMain.addEventListener('mousemove', function (e) {
                    if (!zoomLens.classList.contains('active')) return;
                    const rect = galleryMain.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const xPercent = (x / rect.width) * 100;
                    const yPercent = (y / rect.height) * 100;

                    zoomLens.style.backgroundSize = `${rect.width * ZOOM_LEVEL}px ${rect.height * ZOOM_LEVEL}px`;
                    zoomLens.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
                });

                galleryMain.addEventListener('mouseleave', function () {
                    zoomLens.classList.remove('active');
                    mainImg.style.opacity = '1';
                });
            }

            /* ---- FULLSCREEN LIGHTBOX ---- */
            const zoomBtn = document.getElementById('galleryZoomBtn');
            if (zoomBtn && mainImg) {
                zoomBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const overlay = document.createElement('div');
                    overlay.className = 'lightbox-overlay';
                    overlay.innerHTML = `
                        <button class="lightbox-close"><i class="fa-solid fa-xmark"></i></button>
                        <img src="${mainImg.dataset.zoomSrc || mainImg.src}" alt="Zoomed product">
                    `;
                    document.body.appendChild(overlay);
                    requestAnimationFrame(() => overlay.classList.add('active'));
                    overlay.addEventListener('click', function (ev) {
                        if (ev.target === overlay || ev.target.closest('.lightbox-close')) {
                            overlay.classList.remove('active');
                            setTimeout(() => overlay.remove(), 300);
                        }
                    });
                });
            }

            /* ---- QUANTITY CONTROL ---- */
            const qtyInput = document.getElementById('qtyInput');
            const qtyMinus = document.getElementById('qtyMinus');
            const qtyPlus = document.getElementById('qtyPlus');
            if (qtyInput && qtyMinus && qtyPlus) {
                qtyMinus.addEventListener('click', () => {
                    // Update value and dispatch change event so WooCommerce cart updates if needed
                    const v = parseInt(qtyInput.value) || 1;
                    if (v > 1) {
                        qtyInput.value = v - 1;
                        qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                qtyPlus.addEventListener('click', () => {
                    const v = parseInt(qtyInput.value) || 1;
                    const max = parseInt(qtyInput.max) || 10;
                    if (v < max) {
                        qtyInput.value = v + 1;
                        qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }

            /* ---- PRODUCT TABS ---- */
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanels = document.querySelectorAll('.tab-panel');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabPanels.forEach(p => p.classList.remove('active'));
                    btn.classList.add('active');
                    const panel = document.getElementById('tab-' + btn.dataset.tab);
                    if (panel) panel.classList.add('active');
                });
            });

            /* ---- FLASH SALE COUNTDOWN ---- */
            const fsHours = document.getElementById('fsHours');
            const fsMinutes = document.getElementById('fsMinutes');
            const fsSeconds = document.getElementById('fsSeconds');
            if (fsHours && fsMinutes && fsSeconds) {
                let totalSeconds = 4 * 3600 + 32 * 60 + 18;
                setInterval(() => {
                    if (totalSeconds <= 0) return;
                    totalSeconds--;
                    const h = Math.floor(totalSeconds / 3600);
                    const m = Math.floor((totalSeconds % 3600) / 60);
                    const s = totalSeconds % 60;
                    fsHours.textContent = h.toString().padStart(2, '0');
                    fsMinutes.textContent = m.toString().padStart(2, '0');
                    fsSeconds.textContent = s.toString().padStart(2, '0');
                }, 1000);
            }
        });
    </script>

    <?php
endwhile;
get_footer();
?>