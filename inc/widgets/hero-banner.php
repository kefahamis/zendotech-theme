<?php
/**
 * Zendotech Hero Banner Widget
 * Fixed: show_side_cards condition mismatch, null-safe btn_url, missing sidebar condition
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Hero_Banner_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_hero_banner'; }
	public function get_title()      { return __( 'Hero Banner', 'zendotech' ); }
	public function get_icon()       { return 'eicon-banner'; }
	public function get_categories() { return [ 'zendotech' ]; }

	protected function register_controls() {

		/* ---- SLIDES ---- */
		$this->start_controls_section( 'section_slides', [
			'label' => __( 'Slides', 'zendotech' ),
		] );

		$repeater = new \Elementor\Repeater();

		$repeater->add_control( 'slide_tag', [
			'label'   => __( 'Tag', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'New Arrival',
		] );
		$repeater->add_control( 'slide_title', [
			'label'   => __( 'Title', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Premium Audio Gear',
		] );
		$repeater->add_control( 'slide_desc', [
			'label'   => __( 'Description', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => 'Professional studio sound for everyone.',
		] );
		$repeater->add_control( 'slide_price', [
			'label'   => __( 'Price Text', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'From $299',
		] );
		$repeater->add_control( 'slide_btn_text', [
			'label'   => __( 'Button Text', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Shop Now',
		] );
		$repeater->add_control( 'slide_btn_url', [
			'label'   => __( 'Button URL', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );
		$repeater->add_control( 'slide_image', [
			'label'   => __( 'Slide Image', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=400&fit=crop' ],
		] );
		$repeater->add_control( 'slide_bg_image', [
			'label'   => __( 'Background Image', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1000&h=800&fit=crop' ],
		] );
		$repeater->add_control( 'slide_bg_color', [
			'label'       => __( 'Background Color', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::COLOR,
			'default'     => '',
			'description' => __( 'Leave blank to use default variant colour.', 'zendotech' ),
		] );

		$this->add_control( 'slides', [
			'label'       => __( 'Slider Slides', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'slide_title'    => 'Sony WH-1000XM5 Wireless',
					'slide_tag'      => 'Best Seller',
					'slide_desc'     => 'Industry-leading noise cancellation with exceptional sound quality.',
					'slide_price'    => 'From $348',
					'slide_btn_text' => 'Shop Now',
					'slide_image'    => [ 'url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=400&fit=crop' ],
					'slide_bg_image' => [ 'url' => 'https://images.unsplash.com/photo-1470309864661-68328b2cd0a5?w=1200&h=600&fit=crop' ],
				],
				[
					'slide_title'    => 'Marshall Stanmore III',
					'slide_tag'      => 'New Arrival',
					'slide_desc'     => 'Iconic design meets modern wireless audio.',
					'slide_price'    => 'From $379',
					'slide_btn_text' => 'Shop Now',
					'slide_image'    => [ 'url' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=400&fit=crop' ],
					'slide_bg_image' => [ 'url' => 'https://images.unsplash.com/photo-1455792233700-18cfce9c5c17?w=1200&h=600&fit=crop' ],
				],
				[
					'slide_title'    => 'Bose QuietComfort Ultra',
					'slide_tag'      => 'Premium',
					'slide_desc'     => 'World-class noise cancellation with immersive spatial audio.',
					'slide_price'    => 'From $349',
					'slide_btn_text' => 'Shop Now',
					'slide_image'    => [ 'url' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=500&h=400&fit=crop' ],
					'slide_bg_image' => [ 'url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=1200&h=600&fit=crop' ],
				],
			],
			'title_field' => '{{{ slide_title }}}',
		] );

		$this->end_controls_section();

		/* ---- SLIDER SETTINGS ---- */
		$this->start_controls_section( 'section_slider_settings', [
			'label' => __( 'Slider Settings', 'zendotech' ),
		] );

		$this->add_control( 'transition_effect', [
			'label'   => __( 'Transition Effect', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'fade',
			'options' => [
				'slide'    => __( 'Slide', 'zendotech' ),
				'fade'     => __( 'Fade', 'zendotech' ),
				'creative' => __( 'Creative Fade', 'zendotech' ),
			],
		] );
		$this->add_control( 'autoplay_delay', [
			'label'   => __( 'Autoplay Delay (ms)', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 6000,
			'min'     => 1000,
			'max'     => 20000,
			'step'    => 500,
		] );
		$this->add_control( 'transition_speed', [
			'label'   => __( 'Transition Speed (ms)', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 1200,
			'min'     => 300,
			'max'     => 5000,
			'step'    => 100,
		] );
		$this->add_control( 'pause_on_hover', [
			'label'   => __( 'Pause on Hover', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->end_controls_section();

		/* ---- SIDE CARDS ---- */
		$this->start_controls_section( 'section_side_cards', [
			'label' => __( 'Side Cards', 'zendotech' ),
		] );

		$this->add_control( 'show_sidebar', [
			'label'   => __( 'Show Category Sidebar', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		// Card 1
		$this->add_control( 'card1_heading', [
			'label'     => __( 'Card 1', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'card1_tag',   [ 'label' => __( 'Tag', 'zendotech' ),   'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Best Seller' ] );
		$this->add_control( 'card1_title', [ 'label' => __( 'Title', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Marshall Speaker' ] );
		$this->add_control( 'card1_price', [ 'label' => __( 'Price', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Save $60' ] );
		$this->add_control( 'card1_image', [
			'label'   => __( 'Image', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=200&h=200&fit=crop' ],
		] );

		// Card 2
		$this->add_control( 'card2_heading', [
			'label'     => __( 'Card 2', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'card2_tag',   [ 'label' => __( 'Tag', 'zendotech' ),   'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'New' ] );
		$this->add_control( 'card2_title', [ 'label' => __( 'Title', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Audio-Technica LP' ] );
		$this->add_control( 'card2_price', [ 'label' => __( 'Price', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'From $299' ] );
		$this->add_control( 'card2_image', [
			'label'   => __( 'Image', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => 'https://images.unsplash.com/photo-1539375665275-f9de415ef9ac?w=200&h=200&fit=crop' ],
		] );

		$this->end_controls_section();

		/* ---- STYLE: Slider Content ---- */
		$this->start_controls_section( 'section_style_slider_content', [
			'label' => __( 'Slider Content', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'slider_tag_color', [
			'label'     => __( 'Tag Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .banner-tag' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'slider_tag_bg', [
			'label'     => __( 'Tag Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .banner-tag' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'slider_tag_typography',
			'selector' => '{{WRAPPER}} .banner-tag',
		] );
		$this->add_control( 'slider_title_color', [
			'label'     => __( 'Title Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .banner-content h1' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'slider_title_typography',
			'selector' => '{{WRAPPER}} .banner-content h1',
		] );
		$this->add_control( 'slider_desc_color', [
			'label'     => __( 'Description Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .banner-content p' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'slider_desc_typography',
			'selector' => '{{WRAPPER}} .banner-content p',
		] );

		$this->end_controls_section();

		/* ---- STYLE: Slider Buttons ---- */
		$this->start_controls_section( 'section_style_slider_buttons', [
			'label' => __( 'Slider Buttons', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'btn_typography',
			'selector' => '{{WRAPPER}} .banner-cta .btn',
		] );
		$this->add_control( 'btn_primary_color', [
			'label'     => __( 'Button Text Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .banner-cta .btn' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_primary_bg', [
			'label'     => __( 'Button Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .banner-cta .btn' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_border_radius', [
			'label'      => __( 'Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .banner-cta .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		/* ---- STYLE: Category Sidebar ---- */
		$this->start_controls_section( 'section_style_sidebar', [
			'label'     => __( 'Category Sidebar', 'zendotech' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_sidebar' => 'yes' ],
		] );

		$this->add_control( 'sidebar_bg_color', [
			'label'     => __( 'Background Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .hero-sidebar' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'sidebar_link_color', [
			'label'     => __( 'Link Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .hero-sidebar a' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'sidebar_link_hover_color', [
			'label'     => __( 'Link Hover Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .hero-sidebar a:hover' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="hero-section">
			<div class="container">
				<div class="hero-grid">

					<?php if ( $s['show_sidebar'] === 'yes' ) : ?>
						<aside class="hero-sidebar">
							<ul class="sidebar-menu">
								<?php
								if ( function_exists( 'get_terms' ) && taxonomy_exists( 'product_cat' ) ) {
									$icon_map = [
										'headphones'  => 'fa-headphones',
										'speakers'    => 'fa-volume-high',
										'turntables'  => 'fa-record-vinyl',
										'guitars'     => 'fa-guitar',
										'microphones' => 'fa-microphone',
										'studio-gear' => 'fa-sliders',
										'keyboards'   => 'fa-keyboard',
										'drums'       => 'fa-drum',
										'earbuds'     => 'fa-headphones',
										'soundbars'   => 'fa-volume-high',
										'monitors'    => 'fa-desktop',
										'accessories' => 'fa-bolt',
									];
									$cats = get_terms( [
										'taxonomy'   => 'product_cat',
										'hide_empty' => false,
										'parent'     => 0,
										'exclude'    => [ get_option( 'default_product_cat' ) ],
									] );
									if ( ! is_wp_error( $cats ) ) {
										$count = 0;
										foreach ( $cats as $cat ) {
											$count++;
											$icon     = $icon_map[ $cat->slug ] ?? 'fa-tag';
											$li_class = ( $count > 9 ) ? 'extra-cat' : '';
											echo '<li class="' . esc_attr( $li_class ) . '"><a href="' . esc_url( get_term_link( $cat ) ) . '"><i class="fa-solid ' . esc_attr( $icon ) . '"></i> ' . esc_html( $cat->name ) . '</a></li>';
										}
										if ( count( $cats ) > 9 ) {
											$shop_url = function_exists( 'wc_get_page_permalink' ) ? esc_url( wc_get_page_permalink( 'shop' ) ) : esc_url( home_url( '/shop' ) );
											echo '<li class="view-more-cats-sidebar"><a href="' . $shop_url . '"><i class="fa-solid fa-plus"></i> ' . __( 'View More Categories', 'zendotech' ) . '</a></li>';
										}
									}
								}
								?>
							</ul>
						</aside>
					<?php endif; ?>

					<!-- Main Banner Slider -->
					<div class="main-banner swiper heroSlider"
						data-autoplay-delay="<?php echo esc_attr( $s['autoplay_delay'] ); ?>"
						data-speed="<?php echo esc_attr( $s['transition_speed'] ); ?>"
						data-pause-on-hover="<?php echo esc_attr( $s['pause_on_hover'] ); ?>"
						data-effect="<?php echo esc_attr( $s['transition_effect'] ); ?>">
						<div class="swiper-wrapper">
							<?php
							$slides_data = [];
							if ( ! empty( $s['slides'] ) ) {
								foreach ( $s['slides'] as $ms ) {
									$slides_data[] = [
										'tag'      => $ms['slide_tag'] ?? 'New Arrival',
										'title'    => $ms['slide_title'] ?? 'Premium Audio',
										'desc'     => $ms['slide_desc'] ?? '',
										'price'    => $ms['slide_price'] ?? '',
										'btn_text' => $ms['slide_btn_text'] ?? 'Shop Now',
										'url'      => ! empty( $ms['slide_btn_url']['url'] ) ? $ms['slide_btn_url']['url'] : '#',
										'img'      => ! empty( $ms['slide_image']['url'] ) ? $ms['slide_image']['url'] : 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&h=400&fit=crop',
										'bg_color' => $ms['slide_bg_color'] ?? '',
										'bg_img'   => ! empty( $ms['slide_bg_image']['url'] ) ? $ms['slide_bg_image']['url'] : '',
									];
								}
							}

							if ( ! empty( $slides_data ) ) :
								$idx = 0;
								foreach ( $slides_data as $sd ) :
									$idx++;
									$bg_class = 'bg-variant-' . ( ( $idx % 3 ) + 1 );
									$bg_styles = [];
									if ( ! empty( $sd['bg_color'] ) ) {
										$bg_styles[] = 'background:' . esc_attr( $sd['bg_color'] ) . ' !important';
									}
									if ( ! empty( $sd['bg_img'] ) ) {
										$bg_styles[] = 'background-image:url(' . esc_url( $sd['bg_img'] ) . ')';
										$bg_styles[] = 'background-size:cover';
										$bg_styles[] = 'background-repeat:no-repeat';
										$bg_styles[] = 'background-position:center';
									}
									$bg_style = $bg_styles ? implode( ';', $bg_styles ) . ';' : '';
									?>
									<div class="swiper-slide">
										<div class="banner-bg <?php echo esc_attr( $bg_class ); ?>"<?php if ( $bg_style ) echo ' style="' . $bg_style . '"'; ?>>
											<div class="banner-img-container" data-swiper-parallax="-300" data-swiper-parallax-duration="1000">
												<img src="<?php echo esc_url( $sd['img'] ); ?>" alt="<?php echo esc_attr( $sd['title'] ); ?>" class="banner-hero-img">
											</div>
											<div class="banner-content" data-swiper-parallax="-500" data-swiper-parallax-duration="1200">
												<span class="banner-tag" data-swiper-parallax="-100"><?php echo esc_html( $sd['tag'] ); ?></span>
												<h1 data-swiper-parallax="-200"><?php echo esc_html( $sd['title'] ); ?></h1>
												<p data-swiper-parallax="-300"><?php echo esc_html( $sd['desc'] ); ?></p>
												<div class="banner-cta" data-swiper-parallax="-400">
													<a href="<?php echo esc_url( $sd['url'] ); ?>" class="btn btn-primary">
														<?php echo esc_html( $sd['btn_text'] ); ?>
														<i class="fa-solid fa-arrow-right"></i>
													</a>
													<span class="banner-price"><?php echo wp_kses_post( $sd['price'] ); ?></span>
												</div>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							<?php else : ?>
								<div class="swiper-slide">
									<div class="banner-bg">
										<div class="banner-content">
											<h1>Configure Slider Content</h1>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
						<div class="swiper-pagination"></div>
					</div>

					<div class="side-banners">
						<div class="side-card side-yellow">
							<div class="sc-text">
								<span class="sc-tag"><?php echo esc_html( $s['card1_tag'] ); ?></span>
								<h4><?php echo esc_html( $s['card1_title'] ); ?></h4>
								<p class="sc-price"><?php echo esc_html( $s['card1_price'] ); ?></p>
							</div>
							<?php if ( ! empty( $s['card1_image']['url'] ) ) : ?>
								<img src="<?php echo esc_url( $s['card1_image']['url'] ); ?>" alt="<?php echo esc_attr( $s['card1_title'] ); ?>">
							<?php endif; ?>
						</div>
						<div class="side-card side-blue">
							<div class="sc-text">
								<span class="sc-tag"><?php echo esc_html( $s['card2_tag'] ); ?></span>
								<h4><?php echo esc_html( $s['card2_title'] ); ?></h4>
								<p class="sc-price"><?php echo esc_html( $s['card2_price'] ); ?></p>
							</div>
							<?php if ( ! empty( $s['card2_image']['url'] ) ) : ?>
								<img src="<?php echo esc_url( $s['card2_image']['url'] ); ?>" alt="<?php echo esc_attr( $s['card2_title'] ); ?>">
							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
		</section>
		<?php
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Hero_Banner_Widget() );
} );
