<?php
/**
 * Template Name: Product Layout
 */
get_header();
?>

<!-- ===== BREADCRUMB ===== -->
<section class="breadcrumb-section">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('shop'))); ?>">Headphones</a></li>
            <li class="active">Sony WH-1000XM5</li>
        </ul>
    </div>
</section>

<!-- ===== PRODUCT LAYOUT ===== -->
<section class="product-section">
    <div class="container">
        <div class="product-top">
            <!-- Gallery -->
            <div class="product-gallery">
                <div class="gallery-main">
                    <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=600&h=600&fit=crop"
                        id="mainImg" alt="Main Product">
                </div>
                <div class="gallery-thumbs">
                    <div class="thumb active"><img
                            src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=100&h=100&fit=crop"
                            alt="Thumb 1"></div>
                    <div class="thumb"><img
                            src="https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=100&h=100&fit=crop"
                            alt="Thumb 2"></div>
                    <div class="thumb"><img
                            src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop"
                            alt="Thumb 3"></div>
                    <div class="thumb"><img
                            src="https://images.unsplash.com/photo-1484704849700-f032a568e944?w=100&h=100&fit=crop"
                            alt="Thumb 4"></div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <div class="pi-brand-row">
                    <span class="pi-brand">Sony</span>
                    <div class="pi-share">
                        <button title="Share"><i class="fa-solid fa-share-nodes"></i></button>
                        <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                    </div>
                </div>

                <h1 class="pi-title">Sony WH-1000XM5 Wireless Noise-Cancelling Headphones</h1>

                <div class="pi-rating">
                    <div class="stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="rating-text">4.7</span>
                    <a href="#reviews" class="review-count">(312 Reviews)</a>
                    <span class="pi-sep">|</span>
                    <span class="pi-sold"><i class="fa-solid fa-fire"></i> 2.4k sold</span>
                </div>

                <div class="pi-price-wrap">
                    <span class="pi-price">$348.00</span>
                    <span class="pi-old-price">$399.00</span>
                    <span class="pi-badge">Save $51</span>
                </div>

                <p class="pi-desc">
                    Industry-leading noise cancellation optimized to you. Magnificent Sound, engineered to perfection.
                    Crystal clear hands-free calling. Up to 30-hour battery life with quick charging.
                </p>

                <div class="pi-meta">
                    <!-- Color Selection -->
                    <div class="meta-row">
                        <span class="meta-label">Color:</span>
                        <div class="color-options">
                            <label class="color-opt active" style="background-color: #222;" title="Black"><input
                                    type="radio" name="color"></label>
                            <label class="color-opt" style="background-color: #f5f5f5; border:1px solid #ddd;"
                                title="Silver"><input type="radio" name="color"></label>
                            <label class="color-opt" style="background-color: #0d1b2a;" title="Midnight Blue"><input
                                    type="radio" name="color"></label>
                        </div>
                    </div>

                    <!-- Qty -->
                    <div class="meta-row">
                        <span class="meta-label">Quantity:</span>
                        <div class="qty-control">
                            <button class="qty-btn minus"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" value="1" min="1" max="10">
                            <button class="qty-btn plus"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </div>

                <div class="pi-actions">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('cart'))); ?>" class="add-to-cart-btn"><i
                            class="fa-solid fa-cart-shopping"></i> Add to Cart</a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('checkout'))); ?>"
                        class="buy-now-btn">Buy It Now</a>
                </div>

                <div class="pi-extras">
                    <div class="extra-item"><i class="fa-solid fa-truck"></i> Free Shipping</div>
                    <div class="extra-item"><i class="fa-solid fa-shield-halved"></i> 2 Year Warranty</div>
                    <div class="extra-item"><i class="fa-solid fa-rotate-left"></i> 30-Day Returns</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="product-tabs">
            <ul class="tabs-nav">
                <li class="active" data-tab="desc">Description</li>
                <li data-tab="specs">Specifications</li>
                <li data-tab="delivery">Delivery & Returns</li>
                <li data-tab="reviews">Reviews (312)</li>
            </ul>
            <div class="tabs-content">
                <!-- Description Tab -->
                <div class="tab-pane active" id="desc">
                    <h3>Product Description</h3>
                    <p>The Sony WH-1000XM5 headphones rewrite the rules for distraction-free listening. One processors
                        control 8 microphones for unprecedented noise cancellation and exceptional call quality. Newly
                        developed driver unit, DSEE – Extreme and Hi-Res audio support provide awe-inspiring audio
                        quality.</p>
                    <p>With 2 processors controlling 8 microphones, Auto NC Optimizer for automatically optimizing noise
                        canceling based on your wearing conditions and environment, and a specially designed driver
                        unit, the WH-1000XM5 headphones with industry-leading noise canceling rewrite the rules for
                        distraction-free listening.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== RELATED PRODUCTS ===== -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Related Products</h2>
        </div>
        <div class="products-row">
            <!-- Related 1 -->
            <div class="product-card" data-product-id="44">
                <div class="pc-img">
                    <img src="https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=300&h=300&fit=crop"
                        alt="Related 1">
                    <div class="pc-overlay">
                        <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                        <button title="Quick View" data-product-url="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>"><i class="fa-regular fa-eye"></i></button>
                        <button title="Compare"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                    </div>
                </div>
                <div class="pc-body">
                    <span class="pc-cat">Headphones</span>
                    <h4><a href="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>">Beats Studio
                            Pro</a></h4>
                    <div class="pc-pricing"><span class="new-price">$199.00</span></div>
                    <a href="<?php echo esc_url(wc_get_page_permalink('cart')); ?>" class="atc-btn" data-product_id="44">Add
                        to Cart</a>
                </div>
            </div>
            <div class="product-card" data-product-id="45">
                <div class="pc-img">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop"
                        alt="Related 2">
                    <div class="pc-overlay">
                        <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                        <button title="Quick View" data-product-url="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>"><i class="fa-regular fa-eye"></i></button>
                        <button title="Compare"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                    </div>
                </div>
                <div class="pc-body">
                    <span class="pc-cat">Headphones</span>
                    <h4><a href="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>">Bose QC
                            45</a></h4>
                    <div class="pc-pricing"><span class="new-price">$299.00</span></div>
                    <a href="<?php echo esc_url(wc_get_page_permalink('cart')); ?>" class="atc-btn" data-product_id="45">Add
                        to Cart</a>
                </div>
            </div>
            <div class="product-card" data-product-id="46">
                <div class="pc-img">
                    <img src="https://images.unsplash.com/photo-1613040809024-b4ef7ba99bc3?w=300&h=300&fit=crop"
                        alt="Related 3">
                    <div class="pc-overlay">
                        <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                        <button title="Quick View" data-product-url="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>"><i class="fa-regular fa-eye"></i></button>
                        <button title="Compare"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                    </div>
                </div>
                <div class="pc-body">
                    <span class="pc-cat">Headphones</span>
                    <h4><a href="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>">JBL Tour One
                            M2</a></h4>
                    <div class="pc-pricing"><span class="new-price">$299.00</span></div>
                    <a href="<?php echo esc_url(wc_get_page_permalink('cart')); ?>" class="atc-btn" data-product_id="46">Add
                        to Cart</a>
                </div>
            </div>
            <div class="product-card" data-product-id="47">
                <div class="pc-img">
                    <img src="https://images.unsplash.com/photo-1577174881658-0f30ed549adc?w=300&h=300&fit=crop"
                        alt="Related 4">
                    <div class="pc-overlay">
                        <button title="Wishlist"><i class="fa-regular fa-heart"></i></button>
                        <button title="Quick View" data-product-url="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>"><i class="fa-regular fa-eye"></i></button>
                        <button title="Compare"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                    </div>
                </div>
                <div class="pc-body">
                    <span class="pc-cat">Headphones</span>
                    <h4><a href="<?php echo esc_url(get_permalink(get_page_by_path('product'))); ?>">Audio-Technica
                            ATH-M50x</a></h4>
                    <div class="pc-pricing"><span class="new-price">$169.00</span></div>
                    <a href="<?php echo esc_url(wc_get_page_permalink('cart')); ?>" class="atc-btn" data-product_id="47">Add
                        to Cart</a>
                </div>
            </div>
        </div>
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