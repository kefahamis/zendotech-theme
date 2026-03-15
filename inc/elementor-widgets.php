<?php
/**
 * Zendotech Elementor Widgets Loader
 * Fixed: widget files now self-register via add_action so this file only
 *        needs to require them; no double-registration risk
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Zendotech widget category
 */
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
	$elements_manager->add_category( 'zendotech', [
		'title' => __( 'Zendotech Audio', 'zendotech' ),
		'icon'  => 'fa fa-music',
	] );
} );

/**
 * Load widget files — each file calls add_action( 'elementor/widgets/register', ... )
 * internally so registration happens at the correct hook timing.
 */
add_action( 'elementor/widgets/register', function() {
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

	foreach ( $widgets as $widget ) {
		$file = $widget_dir . $widget . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}, 5 ); // priority 5 = before Elementor's own priority so classes exist when needed
