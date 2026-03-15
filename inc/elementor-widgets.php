<?php
/**
 * Zendotech Elementor Widgets Loader
 *
 * Registers a custom widget category and loads all Zendotech widgets.
 */

if (!defined('ABSPATH'))
    exit;

/**
 * Register Zendotech widget category
 */
function zendotech_elementor_category($elements_manager)
{
    $elements_manager->add_category('zendotech', [
        'title' => __('Zendotech Audio', 'zendotech'),
        'icon' => 'fa fa-music',
    ]);
}
add_action('elementor/elements/categories_registered', 'zendotech_elementor_category');

/**
 * Register all Zendotech widgets
 */
function zendotech_register_elementor_widgets($widgets_manager)
{
    $widget_dir = get_template_directory() . '/inc/widgets/';

    $widgets = [
        'hero-banner',
        'product-grid',
        'product-carousel',
        'promo-strip',
        'category-grid',
        'brand-banners',
        'tri-banners',
        'home-banners',
        'newsletter-strip',
        'brand-logos',
        'features-bar',
    ];

    foreach ($widgets as $widget) {
        $file = $widget_dir . $widget . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
add_action('elementor/widgets/register', 'zendotech_register_elementor_widgets');
