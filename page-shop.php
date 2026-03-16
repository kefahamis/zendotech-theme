<?php

/**

 * Template Name: Shop Layout

 */

get_header();



// Get current category if filtering

$current_cat = get_queried_object();

$is_category = is_product_category();

$page_title = $is_category ? $current_cat->name : 'Shop';



// 12 products per page; paged must work on static Page (use 'page' or GET 'paged')

$per_page = 12;

$paged = 1;

if (get_query_var('paged')) {

    $paged = max(1, (int) get_query_var('paged'));

} elseif (get_query_var('page')) {

    $paged = max(1, (int) get_query_var('page'));

} elseif (!empty($_GET['paged'])) {

    $paged = max(1, (int) $_GET['paged']);

}



// Product query args
$args = array(
    'post_type' => 'product',
    'posts_per_page' => $per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
);

// Sort
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'default';
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
        break;
}


// Category filter
if ($is_category && isset($current_cat->term_id)) {
    // Include children for this category
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $current_cat->term_id,
        ),
    );
} elseif (isset($_GET['product_cat']) && !empty($_GET['product_cat'])) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => sanitize_text_field($_GET['product_cat']),
        ),
    );
}

$products_query = new WP_Query($args);
$total = $products_query->found_posts;
$total_pages = $products_query->max_num_pages;
$start = $total > 0 ? (($paged - 1) * $per_page) + 1 : 0;
$end = $total > 0 ? min($paged * $per_page, $total) : 0;
?>



<!-- ===== BREADCRUMB ===== -->

<section class="breadcrumb-bar">

    <div class="container">

        <div class="breadcrumb-inner">

            <ol class="breadcrumb">

                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>

                <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>

                <?php if ($is_category): ?>

                    <li><a

                            href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>">Shop</a>

                    </li>

                    <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>

                    <li class="current"><?php echo esc_html($page_title); ?></li>

                <?php else: ?>

                    <li class="current">Shop</li>

                <?php endif; ?>

            </ol>

            <h1 class="page-title"><?php echo esc_html($page_title); ?></h1>

        </div>

    </div>

</section>



<!-- ===== SHOP LAYOUT ===== -->

<section class="shop-section">

    <div class="container">

        <div class="shop-layout">



            <!-- SIDEBAR -->

            <aside class="shop-sidebar" id="shopSidebar">

                <div class="sidebar-overlay" id="sidebarOverlay"></div>

                <div class="sidebar-inner">

                    <div class="sidebar-header-mobile">

                        <h3>Filters</h3>

                        <button class="sidebar-close" id="sidebarClose"><i class="fa-solid fa-xmark"></i></button>

                    </div>



                    <!-- Categories -->

                    <div class="filter-widget">

                        <h3 class="widget-title">Categories</h3>

                        <ul class="category-list">

                            <?php

                            $cats = get_terms(array(

                                'taxonomy' => 'product_cat',

                                'hide_empty' => true,

                                'parent' => 0,

                                'exclude' => array(get_option('default_product_cat')),

                            ));

                            if (!is_wp_error($cats)) {

                                foreach ($cats as $cat) {

                                    $active = ($is_category && $current_cat->term_id === $cat->term_id) ? ' class="active"' : '';

                                    echo '<li><a href="' . esc_url(get_term_link($cat)) . '"' . $active . ' data-id="' . esc_attr($cat->term_id) . '" data-type="category">';

                                    echo '<span>' . esc_html($cat->name) . '</span> ';

                                    echo '<span class="count">(' . esc_html($cat->count) . ')</span>';

                                    echo '</a></li>';

                                }

                            }

                            ?>

                        </ul>

                    </div>



                    <!-- Price Range -->

                    <div class="filter-widget">

                        <h3 class="widget-title">Price Range</h3>

                        <div class="price-range-wrap">

                            <div class="range-slider-container">

                                <div class="range-track">

                                    <div class="range-fill" id="rangeFill"></div>

                                </div>

                                <input type="range" class="range-input range-min" id="rangeMin" min="0" max="5000" value="0" step="10">

                                <input type="range" class="range-input range-max" id="rangeMax" min="0" max="5000" value="5000" step="10">

                            </div>

                            <div class="price-inputs">

                                <div class="price-field">

                                    <label>Min</label>

                                    <input type="number" id="minPrice" value="0" min="0" max="5000" placeholder="0">

                                </div>

                                <span class="price-sep">—</span>

                                <div class="price-field">

                                    <label>Max</label>

                                    <input type="number" id="maxPrice" value="5000" min="0" max="5000" placeholder="5000">

                                </div>

                            </div>

                            <button class="btn-filter" id="btnFilterPrice">Filter</button>

                        </div>

                    </div>



                    <!-- Brands -->

                    <div class="filter-widget">

                        <h3 class="widget-title">Brands</h3>

                        <ul class="brand-filter-list">

                            <?php

                            // Try to get dynamic brands from taxonomy

                            $brands = array();

                            $taxonomies = array('product_brand', 'pa_brand', 'brand');



                            foreach ($taxonomies as $tax) {

                                if (taxonomy_exists($tax)) {

                                    $terms = get_terms(array(

                                        'taxonomy' => $tax,

                                        'hide_empty' => true,

                                    ));

                                    if (!is_wp_error($terms) && !empty($terms)) {

                                        foreach ($terms as $term) {

                                            $brands[] = $term->name;

                                        }

                                        break; // Found one, use it

                                    }

                                }

                            }



                            // Fallback to hardcoded if still empty

                            if (empty($brands)) {

                                $brands = array('Sony', 'Bose', 'JBL', 'Sennheiser', 'Marshall', 'Fender', 'Yamaha', 'Audio-Technica');

                            }



                            foreach ($brands as $brand):

                                ?>

                                <li>

                                    <label class="check-label">

                                        <input type="checkbox" class="filter-checkbox" data-type="brand"

                                            data-value="<?php echo esc_attr($brand); ?>">

                                        <span class="checkmark"></span>

                                        <span class="bl-name"><?php echo esc_html($brand); ?></span>

                                    </label>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </div>



                    <!-- Product Status -->

                    <div class="filter-widget">

                        <h3 class="widget-title">Product Status</h3>

                        <ul class="status-filter-list">

                            <li>

                                <label class="check-label">

                                    <input type="checkbox" class="filter-checkbox" data-type="status"

                                        data-value="on_sale">

                                    <span class="checkmark"></span>

                                    <span class="bl-name">On Sale</span>

                                </label>

                            </li>

                            <li>

                                <label class="check-label">

                                    <input type="checkbox" class="filter-checkbox" data-type="status"

                                        data-value="instock">

                                    <span class="checkmark"></span>

                                    <span class="bl-name">In Stock</span>

                                </label>

                            </li>

                            <li>

                                <label class="check-label">

                                    <input type="checkbox" class="filter-checkbox" data-type="status" data-value="new">

                                    <span class="checkmark"></span>

                                    <span class="bl-name">New Arrivals</span>

                                </label>

                            </li>

                        </ul>

                    </div>



                    <!-- Rating -->

                    <div class="filter-widget">

                        <h3 class="widget-title">Customer Rating</h3>

                        <ul class="rating-filter-list">

                            <li>

                                <label class="check-label">

                                    <input type="checkbox" class="filter-checkbox" data-type="rating" data-value="5">

                                    <span class="checkmark"></span>

                                    <span class="rating-stars">

                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i

                                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i

                                            class="fa-solid fa-star"></i>

                                    </span>

                                </label>

                            </li>

                            <li>

                                <label class="check-label">

                                    <input type="checkbox" class="filter-checkbox" data-type="rating" data-value="4">

                                    <span class="checkmark"></span>

                                    <span class="rating-stars">

                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i

                                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i

                                            class="fa-regular fa-star"></i>

                                    </span>

                                </label>

                            </li>

                            <li>

                                <label class="check-label">

                                    <input type="checkbox" class="filter-checkbox" data-type="rating" data-value="3">

                                    <span class="checkmark"></span>

                                    <span class="rating-stars">

                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i

                                            class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i><i

                                            class="fa-regular fa-star"></i>

                                    </span>

                                </label>

                            </li>

                        </ul>

                    </div>



                    <!-- Sidebar CTA Banner -->

                    <div class="sidebar-banner">

                        <div class="sb-content">

                            <span class="sb-tag">Limited Offer</span>

                            <h4>Up to 40% Off</h4>

                            <p>On premium headphones</p>

                            <a href="<?php echo function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('shop')) : '#'; ?>"

                                class="btn btn-primary btn-sm">Shop Now</a>

                        </div>

                    </div>

                </div>

            </aside>



            <!-- MAIN CONTENT -->

            <div class="shop-main" id="shopMainContent">

                <!-- Toolbar -->

                <div class="shop-toolbar">

                    <div class="toolbar-left">

                        <p class="results-count">

                            <?php if ($total > 0): ?>

                                Showing <strong><?php echo $start; ?>–<?php echo $end; ?></strong> of

                                <strong><?php echo $total; ?></strong> results

                            <?php else: ?>

                                No products found

                            <?php endif; ?>

                        </p>

                    </div>

                    <div class="toolbar-right">

                        <div class="view-switcher">

                            <button class="view-btn active" data-view="grid" title="Grid View"><i

                                    class="fa-solid fa-grip"></i></button>

                            <button class="view-btn" data-view="list" title="List View"><i

                                    class="fa-solid fa-list"></i></button>

                        </div>

                        <div class="sort-dropdown">

                            <select id="sortBy">

                                <?php

                                $sort_options = array(

                                    'default' => 'Default Sorting',

                                    'popularity' => 'Sort by Popularity',

                                    'rating' => 'Sort by Average Rating',

                                    'date' => 'Sort by Newness',

                                    'price-low' => 'Price: Low to High',

                                    'price-high' => 'Price: High to Low',

                                );

                                foreach ($sort_options as $key => $label) {

                                    $selected = ($orderby === $key) ? ' selected' : '';

                                    echo '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($label) . '</option>';

                                }

                                ?>

                            </select>

                        </div>

                        <button class="filter-toggle-btn" id="filterToggle">

                            <i class="fa-solid fa-sliders"></i> Filters

                        </button>

                    </div>

                </div>



                <!-- Products Grid -->

                <div class="shop-grid" id="shopGrid">
                    <?php
                    if ($products_query->have_posts()) {
                        while ($products_query->have_posts()) {
                            $products_query->the_post();
                            $product = wc_get_product(get_the_ID());
                            if ($product) {
                                zendotech_product_card($product);
                            }
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<div class="no-products"><p>No products found.</p></div>';
                    }
                    ?>
                </div>


                <!-- Pagination -->

                <?php

                $total_pages = max(1, (int) ceil($total / $per_page));

                $permalink = $is_category && isset($current_cat->term_id) ? get_term_link($current_cat) : get_permalink();

                if (is_wp_error($permalink)) {

                    $permalink = get_permalink();

                }

                if ($total_pages > 1):

                ?>

                    <?php

                    $permalink = is_wp_error($permalink) ? get_permalink() : $permalink;
                    $base = esc_url(add_query_arg('paged', '%#%', $permalink));

                    // Preserve orderby parameter in pagination links
                    if ($orderby !== 'default') {
                        $base = esc_url(add_query_arg('orderby', $orderby, $base));
                    }

                    $pagination_links = paginate_links(array(

                        'base' => $base,

                        'format' => '',

                        'current' => max(1, $paged),

                        'total' => $total_pages,

                        'prev_text' => '<i class="fa-solid fa-arrow-left"></i>',

                        'next_text' => '<i class="fa-solid fa-arrow-right"></i>',

                        'type' => 'array',

                        'show_all' => false,

                    ));

                    if ($pagination_links):

                        ?>

                        <nav class="shop-pagination modern" aria-label="Shop pagination">

                            <ul>

                                <?php foreach ($pagination_links as $link): ?>

                                    <li><?php echo $link; ?></li>

                                <?php endforeach; ?>

                            </ul>

                        </nav>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>

<style>

    .shop-pagination.modern ul {

        list-style:none;

        padding:0;

        margin:20px 0 0;

        display:flex;

        flex-wrap:wrap;

        gap:10px;

        justify-content:center;

        align-items:center;

    }

    .shop-pagination.modern li {

        margin:0;

    }

    .shop-pagination.modern .page-numbers {

        display:flex;

        align-items:center;

        justify-content:center;

        width:44px;

        height:44px;

        border-radius:50%;

        border:1px solid rgba(9,19,70,0.15);

        background:#fff;

        color:#1a1b29;

        font-weight:600;

        transition:all .25s ease;

    }

    .shop-pagination.modern .page-numbers:hover {

        box-shadow:0 8px 20px rgba(9,19,70,.15);

        transform:translateY(-2px);

    }

    .shop-pagination.modern .page-numbers.current {

        background:#151a94;

        color:#fff;

        border-color:#151a94;

        box-shadow:0 12px 30px rgba(21,26,148,.25);

    }

    .shop-pagination.modern .page-numbers.prev,

    .shop-pagination.modern .page-numbers.next {

        width:auto;

        padding:0 16px;

        border-radius:999px;

    }

    @media (max-width:768px) {

        .shop-pagination.modern .page-numbers {

            width:auto;

            padding:0 12px;

        }

        .shop-pagination.modern ul {

            justify-content:flex-start;

            gap:8px;

        }

    }

</style>



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

    // Mobile filter sidebar toggle

    const filterToggle = document.getElementById('filterToggle');

    const sidebar = document.getElementById('shopSidebar');

    const sidebarClose = document.getElementById('sidebarClose');

    const sidebarOverlay = document.getElementById('sidebarOverlay');



    if (filterToggle) {

        filterToggle.addEventListener('click', () => {

            sidebar.classList.add('open');

            document.body.style.overflow = 'hidden';

        });

    }



    function closeSidebar() {

        sidebar.classList.remove('open');

        document.body.style.overflow = '';

    }



    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);

    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);



    // View switcher

    const viewBtns = document.querySelectorAll('.view-btn');

    const shopGrid = document.getElementById('shopGrid');



    viewBtns.forEach(btn => {

        btn.addEventListener('click', () => {

            viewBtns.forEach(b => b.classList.remove('active'));

            btn.classList.add('active');

            const view = btn.dataset.view;

            if (view === 'list') {

                shopGrid.classList.add('list-view');

            } else {

                shopGrid.classList.remove('list-view');

            }

        });

    });

</script>



<?php get_footer(); ?>

