<?php
/**
 * Zendotech Front Page Elementor Setup
 *
 * Programmatically populates the front page with all 9 Zendotech
 * Elementor widgets so they appear immediately without manual drag-and-drop.
 */

if (!defined('ABSPATH'))
    exit;

/**
 * Generate a random Elementor-style ID (7 hex chars)
 */
function zendotech_el_id()
{
    return substr(md5(uniqid(mt_rand(), true)), 0, 7);
}

/**
 * Wrap a widget array inside a section > column structure
 */
function zendotech_el_section($widget_data)
{
    return [
        'id' => zendotech_el_id(),
        'elType' => 'section',
        'settings' => ['layout' => 'full_width', 'content_width' => ['size' => '', 'unit' => 'px'], 'gap' => 'no', 'padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true]],
        'elements' => [
            [
                'id' => zendotech_el_id(),
                'elType' => 'column',
                'settings' => ['_column_size' => 100],
                'elements' => [$widget_data],
            ],
        ],
    ];
}

/**
 * Build the full Elementor data array for the front page
 */
function zendotech_build_frontpage_elementor_data()
{
    $sections = [];

    // 1. Hero Banner
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_hero_banner',
        'settings' => [
            'banner_tag' => 'New Arrival',
            'banner_title' => 'Premium Audio Gear',
            'banner_desc' => 'Shop the latest in headphones, speakers, and instruments.',
            'banner_btn_text' => 'Shop Now',
            'banner_btn_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
            'banner_price' => 'From $299',
            'banner_image' => ['url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=400&fit=crop', 'id' => ''],
            'use_featured_product' => '',
            'show_sidebar' => 'yes',
            'card1_tag' => 'Best Seller',
            'card1_title' => 'Marshall Speaker',
            'card1_price' => 'Save $60',
            'card1_image' => ['url' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=200&h=200&fit=crop', 'id' => ''],
            'card2_tag' => 'New',
            'card2_title' => 'Audio-Technica LP',
            'card2_price' => 'From $299',
            'card2_image' => ['url' => 'https://images.unsplash.com/photo-1539375665275-f9de415ef9ac?w=200&h=200&fit=crop', 'id' => ''],
        ],
    ]);

    // 2. Product Grid — Deals of the Day
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_product_grid',
        'settings' => [
            'section_title' => 'Deals of the Day',
            'product_source' => 'on_sale',
            'product_count' => 5,
            'show_countdown' => 'yes',
            'countdown_hours' => 4,
            'countdown_minutes' => 32,
            'countdown_seconds' => 18,
            'show_view_all' => 'yes',
            'view_all_text' => 'View All Deals',
            'view_all_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
            'show_tab_filters' => '',
        ],
    ]);

    // 3. Promo Strip
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_promo_strip',
        'settings' => [
            'icon' => 'fa-solid fa-music',
            'text' => 'Free guitar strings pack with every instrument order — limited time only!',
            'btn_text' => 'Grab Yours',
            'btn_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
    ]);

    // 4. Category Grid
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_category_grid',
        'settings' => [
            'section_title' => 'Shop by Category',
            'cat_count' => 6,
            'fallback_image' => ['url' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop', 'id' => ''],
        ],
    ]);

    // 5. Brand Banners (Dual)
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_brand_banners',
        'settings' => [
            'cards' => [
                [
                    '_id' => zendotech_el_id(),
                    'brand' => 'Bose',
                    'product_name' => 'QuietComfort Ultra',
                    'description' => 'Immersive spatial audio. Silence the world.',
                    'btn_text' => 'Shop Now',
                    'btn_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=350&fit=crop', 'id' => ''],
                    'gradient' => 'bc-gradient-1',
                ],
                [
                    '_id' => zendotech_el_id(),
                    'brand' => 'Marshall',
                    'product_name' => 'Stanmore III',
                    'description' => 'Iconic design meets modern wireless audio.',
                    'btn_text' => 'Shop Now',
                    'btn_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1507667522877-ad03f0c7b0e0?w=400&h=350&fit=crop', 'id' => ''],
                    'gradient' => 'bc-gradient-2',
                ],
            ],
        ],
    ]);

    // 6. Product Grid — Popular Products
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_product_grid',
        'settings' => [
            'section_title' => 'Popular Products',
            'product_source' => 'popular',
            'product_count' => 5,
            'show_countdown' => '',
            'show_view_all' => '',
            'show_tab_filters' => 'yes',
        ],
    ]);

    // 7. Tabbed Product Carousel (New)
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_product_carousel',
        'settings' => [
            'tab1_label' => 'Featured',
            'tab1_source' => 'featured',
            'tab2_label' => 'On Sale',
            'tab2_source' => 'on_sale',
            'tab3_label' => 'Top Rated',
            'tab3_source' => 'popular',
            'products_per_page' => 6,
            'total_products' => 18,
        ],
    ]);

    // 8. Tri Banners
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_tri_banners',
        'settings' => [
            'cards' => [
                [
                    '_id' => zendotech_el_id(),
                    'tag' => 'Best Sellers',
                    'title' => 'Wireless Earbuds',
                    'link_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1590658268037-6bf12f032f55?w=200&h=200&fit=crop', 'id' => ''],
                    'color' => 'tri-pink',
                ],
                [
                    '_id' => zendotech_el_id(),
                    'tag' => 'Top Rated',
                    'title' => 'Vinyl & Records',
                    'link_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=200&h=200&fit=crop', 'id' => ''],
                    'color' => 'tri-gold',
                ],
                [
                    '_id' => zendotech_el_id(),
                    'tag' => 'New Arrivals',
                    'title' => 'DJ Equipment',
                    'link_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1571330735066-03aaa9429d89?w=200&h=200&fit=crop', 'id' => ''],
                    'color' => 'tri-cyan',
                ],
            ],
        ],
    ]);

    // 8. Product Grid — New Arrivals
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_product_grid',
        'settings' => [
            'section_title' => 'New Arrivals',
            'product_source' => 'latest',
            'product_count' => 5,
            'show_countdown' => '',
            'show_view_all' => 'yes',
            'view_all_text' => 'View All',
            'view_all_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
            'show_tab_filters' => '',
        ],
    ]);

    // 9. Newsletter Strip
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_newsletter_strip',
        'settings' => [
            'image' => ['url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=120&h=120&fit=crop', 'id' => ''],
            'heading' => 'Exclusive deals for audiophiles — up to 40% off premium audio gear!',
            'subtitle' => 'Subscribe and never miss a beat.',
            'btn_text' => 'See All Deals',
            'btn_url' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
    ]);

    // 10. Brand Logos
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_brand_logos',
        'settings' => [
            'brands' => [
                ['_id' => zendotech_el_id(), 'name' => 'BOSE', 'font_size' => 18, 'font_weight' => '800', 'color' => '#333333', 'letter_spacing' => 2, 'italic' => '', 'font_family' => ''],
                ['_id' => zendotech_el_id(), 'name' => 'Sennheiser', 'font_size' => 18, 'font_weight' => '700', 'color' => '#333333', 'letter_spacing' => 1, 'italic' => 'yes', 'font_family' => ''],
                ['_id' => zendotech_el_id(), 'name' => 'SONY', 'font_size' => 20, 'font_weight' => '800', 'color' => '#333333', 'letter_spacing' => 1, 'italic' => '', 'font_family' => ''],
                ['_id' => zendotech_el_id(), 'name' => 'Marshall', 'font_size' => 18, 'font_weight' => '700', 'color' => '#C41E3A', 'letter_spacing' => 2, 'italic' => '', 'font_family' => 'serif'],
                ['_id' => zendotech_el_id(), 'name' => 'JBL', 'font_size' => 18, 'font_weight' => '700', 'color' => '#333333', 'letter_spacing' => 2, 'italic' => '', 'font_family' => ''],
                ['_id' => zendotech_el_id(), 'name' => 'Fender', 'font_size' => 16, 'font_weight' => '700', 'color' => '#C30F1F', 'letter_spacing' => 1, 'italic' => '', 'font_family' => ''],
            ],
        ],
    ]);

    // 11. Features Bar
    $sections[] = zendotech_el_section([
        'id' => zendotech_el_id(),
        'elType' => 'widget',
        'widgetType' => 'zendotech_features_bar',
        'settings' => [
            'features' => [
                ['_id' => zendotech_el_id(), 'icon' => 'fa-solid fa-truck-fast', 'title' => 'Free Shipping', 'description' => 'On orders over $75'],
                ['_id' => zendotech_el_id(), 'icon' => 'fa-solid fa-rotate-left', 'title' => '30-Day Returns', 'description' => 'Hassle-free returns'],
                ['_id' => zendotech_el_id(), 'icon' => 'fa-solid fa-shield-halved', 'title' => '2-Year Warranty', 'description' => 'On all audio products'],
                ['_id' => zendotech_el_id(), 'icon' => 'fa-solid fa-headset', 'title' => 'Expert Support', 'description' => 'Audio specialists 24/7'],
            ],
        ],
    ]);

    return $sections;
}

/**
 * Populate the front page with Elementor widget data.
 * Runs once on theme setup — creates the page if needed,
 * sets it as the static front page, and injects all widget data.
 */
function zendotech_populate_frontpage_elementor()
{
    // Only run once
    if (get_option('zendotech_frontpage_populated'))
        return;

    // Elementor must be active
    if (!did_action('elementor/loaded'))
        return;

    // Find or create the front page
    $front_page_id = get_option('page_on_front');

    if (!$front_page_id) {
        // Check if a page with the slug "home" or "front-page" exists
        $home_page = get_page_by_path('home');
        if (!$home_page) {
            $home_page = get_page_by_path('front-page');
        }

        if ($home_page) {
            $front_page_id = $home_page->ID;
        } else {
            // Create a new front page
            $front_page_id = wp_insert_post([
                'post_title' => 'Home',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'home',
            ]);
        }

        // Set as static front page
        update_option('show_on_front', 'page');
        update_option('page_on_front', $front_page_id);
    }

    if (!$front_page_id || is_wp_error($front_page_id))
        return;

    // Build Elementor data
    $data = zendotech_build_frontpage_elementor_data();

    // Save Elementor meta
    update_post_meta($front_page_id, '_elementor_edit_mode', 'builder');
    update_post_meta($front_page_id, '_elementor_data', wp_json_encode($data));
    update_post_meta($front_page_id, '_elementor_version', '3.0.0');
    update_post_meta($front_page_id, '_elementor_template_type', 'wp-page');

    // Also set post content so the_content() renders Elementor
    wp_update_post([
        'ID' => $front_page_id,
        'post_content' => '<!-- wp:paragraph --><p>Built with Elementor.</p><!-- /wp:paragraph -->',
    ]);

    // Mark as done so this doesn't re-run
    update_option('zendotech_frontpage_populated', true);
}
add_action('admin_init', 'zendotech_populate_frontpage_elementor');

/**
 * Admin notice after setup completes
 */
function zendotech_frontpage_setup_notice()
{
    if (!get_option('zendotech_frontpage_populated'))
        return;
    if (get_option('zendotech_frontpage_notice_dismissed'))
        return;

    $front_page_id = get_option('page_on_front');
    if (!$front_page_id)
        return;

    $edit_url = admin_url('post.php?post=' . $front_page_id . '&action=elementor');
    ?>
    <div class="notice notice-success is-dismissible zendotech-frontpage-notice">
        <p>
            <strong>🎉 Zendotech Front Page Ready!</strong>
            All widgets have been pre-populated with demo data.
            <a href="<?php echo esc_url($edit_url); ?>">Edit with Elementor</a> to customize.
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'zendotech_frontpage_setup_notice');

/**
 * Allow re-population via admin action: ?zendotech_repopulate_front=1
 */
function zendotech_repopulate_frontpage()
{
    if (!isset($_GET['zendotech_repopulate_front']))
        return;
    if (!current_user_can('manage_options'))
        return;

    delete_option('zendotech_frontpage_populated');
    zendotech_populate_frontpage_elementor();

    wp_redirect(admin_url('?zendotech_repopulated=1'));
    exit;
}
add_action('admin_init', 'zendotech_repopulate_frontpage');
