<?php
/**
 * Zendotech Product Carousel Widget (Tabbed)
 * Fixed: inline CSS/JS deduplication via wp_add_inline_style / static flag,
 *        safe null checks, proper widget ID scoping
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Product_Carousel_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_product_carousel'; }
	public function get_title()      { return __( 'Product Carousel (Tabbed)', 'zendotech' ); }
	public function get_icon()       { return 'eicon-carousel'; }
	public function get_categories() { return [ 'zendotech' ]; }

	/** Register shared styles once via Elementor's style system */
	public function get_style_depends() {
		return [ 'zendotech-product-carousel' ];
	}

	protected function register_controls() {

		/* ---- Content ---- */
		$this->start_controls_section( 'section_content', [
			'label' => __( 'Product Carousel', 'zendotech' ),
		] );

		$this->add_control( 'tab1_label',  [ 'label' => __( 'Tab 1 Label', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Featured' ] );
		$this->add_control( 'tab1_source', [
			'label'   => __( 'Tab 1 Source', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'featured',
			'options' => $this->source_options(),
		] );
		$this->add_control( 'tab2_label',  [ 'label' => __( 'Tab 2 Label', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'On Sale' ] );
		$this->add_control( 'tab2_source', [
			'label'   => __( 'Tab 2 Source', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'on_sale',
			'options' => $this->source_options(),
		] );
		$this->add_control( 'tab3_label',  [ 'label' => __( 'Tab 3 Label', 'zendotech' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Top Rated' ] );
		$this->add_control( 'tab3_source', [
			'label'   => __( 'Tab 3 Source', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'popular',
			'options' => $this->source_options(),
		] );

		$this->add_control( 'products_per_page', [
			'label'   => __( 'Products Per Page', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 6,
			'min'     => 3,
			'max'     => 12,
		] );
		$this->add_control( 'total_products', [
			'label'       => __( 'Total Products to Load', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'default'     => 12,
			'min'         => 6,
			'max'         => 24,
			'description' => __( 'Carousel pages = total / per page.', 'zendotech' ),
		] );
		$this->add_control( 'show_dots', [
			'label'   => __( 'Show Pagination Dots', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );
		$this->add_control( 'show_arrows', [
			'label'   => __( 'Show Navigation Arrows', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'no',
		] );
		$this->add_control( 'show_tab_filters', [
			'label'   => __( 'Show Tabs', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );
		$this->add_control( 'section_title', [
			'label'   => __( 'Section Title', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'Laptops & Computers', 'zendotech' ),
		] );
		$this->add_control( 'banner_heading', [
			'label'   => __( 'Banner Heading', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'MacBook Air 2020', 'zendotech' ),
		] );
		$this->add_control( 'banner_subheading', [
			'label'   => __( 'Banner Subheading', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'Small chip. Giant leap.', 'zendotech' ),
		] );
		$this->add_control( 'banner_cta_label', [
			'label'   => __( 'Banner CTA Text', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __( 'Shop now', 'zendotech' ),
		] );
		$this->add_control( 'banner_cta_url', [
			'label' => __( 'Banner CTA URL', 'zendotech' ),
			'type'  => \Elementor\Controls_Manager::URL,
		] );
		$this->add_control( 'banner_image', [
			'label'   => __( 'Banner Image', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => [
				'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=500&fit=crop',
			],
		] );

		$this->end_controls_section();

		/* ---- STYLE ---- */

		// Container
		$this->start_controls_section( 'section_style_container', [
			'label' => __( 'Section Container', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_group_control( \Elementor\Group_Control_Background::get_type(), [
			'name'     => 'section_background',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .zpc-section',
		] );
		$this->add_control( 'section_padding', [
			'label'      => __( 'Padding', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .zpc-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->end_controls_section();

		// Tabs
		$this->start_controls_section( 'section_style_tabs', [
			'label'     => __( 'Tabs', 'zendotech' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_tab_filters' => 'yes' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'tabs_typography',
			'selector' => '{{WRAPPER}} .zpc-tab',
		] );
		$this->start_controls_tabs( 'tabs_style' );
		$this->start_controls_tab( 'tab_normal', [ 'label' => __( 'Normal', 'zendotech' ) ] );
		$this->add_control( 'tab_color',    [ 'label' => __( 'Text', 'zendotech' ),  'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .zpc-tab' => 'color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_active', [ 'label' => __( 'Active', 'zendotech' ) ] );
		$this->add_control( 'tab_active_color', [ 'label' => __( 'Text', 'zendotech' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .zpc-tab.active' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'tab_active_indicator', [ 'label' => __( 'Indicator', 'zendotech' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .zpc-tab.active' => 'border-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_heading', [
			'label' => __( 'Section Title', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'heading_typography',
			'selector' => '{{WRAPPER}} .zpc-heading-text h2',
		] );
		$this->add_control( 'heading_color', [
			'label'     => __( 'Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .zpc-heading-text h2' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'heading_underline_color', [
			'label'     => __( 'Underline Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#c62b21',
			'selectors' => [ '{{WRAPPER}} .zpc-heading-line' => 'background: {{VALUE}};' ],
		] );
		$this->end_controls_section();

		// Product Card
		$this->start_controls_section( 'section_style_product_card', [
			'label' => __( 'Product Card', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'card_bg_color', [
			'label'     => __( 'Background Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .product-card' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'card_border_radius', [
			'label'      => __( 'Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [
			'name'     => 'card_box_shadow',
			'selector' => '{{WRAPPER}} .product-card',
		] );
		$this->end_controls_section();

		// Dots
		$this->start_controls_section( 'section_style_dots', [
			'label'     => __( 'Pagination Dots', 'zendotech' ),
			'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_dots' => 'yes' ],
		] );
		$this->add_control( 'dot_color', [
			'label'     => __( 'Dot Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .zpc-dot' => 'background: {{VALUE}};' ],
		] );
		$this->add_control( 'dot_active_color', [
			'label'     => __( 'Active Dot Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .zpc-dot.active' => 'background: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	protected function render() {
		// Register shared stylesheet once
		$this->maybe_register_styles();

		$s   = $this->get_settings_for_display();
		$uid = 'zpc-' . $this->get_id();
		$per = max( 1, intval( $s['products_per_page'] ) );
		$tot = max( $per, intval( $s['total_products'] ) );

		if ( ! function_exists( 'wc_get_products' ) ) {
			echo '<p>' . __( 'WooCommerce is required for this widget.', 'zendotech' ) . '</p>';
			return;
		}

		$tabs = [
			[ 'label' => $s['tab1_label'], 'source' => $s['tab1_source'] ],
			[ 'label' => $s['tab2_label'], 'source' => $s['tab2_source'] ],
			[ 'label' => $s['tab3_label'], 'source' => $s['tab3_source'] ],
		];
		$section_title_text = ! empty( $s['section_title'] ) ? $s['section_title'] : __( 'Laptops & Computers', 'zendotech' );
		$banner_heading      = ! empty( $s['banner_heading'] ) ? $s['banner_heading'] : '';
		$banner_subheading   = ! empty( $s['banner_subheading'] ) ? $s['banner_subheading'] : '';
		$banner_cta_label    = ! empty( $s['banner_cta_label'] ) ? $s['banner_cta_label'] : '';
		$banner_cta_data     = ! empty( $s['banner_cta_url'] ) ? $s['banner_cta_url'] : [];
		$banner_cta_url      = ! empty( $banner_cta_data['url'] ) ? $banner_cta_data['url'] : '';
		$banner_cta_target   = ! empty( $banner_cta_data['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
		$banner_image_url    = ! empty( $s['banner_image']['url'] ) ? $s['banner_image']['url'] : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=500&fit=crop';
		?>
		<section class="section zpc-section" id="<?php echo esc_attr( $uid ); ?>">
		<div class="container">

				<div class="zpc-heading-row">
					<div class="zpc-heading-text">
						<h2><?php echo esc_html( $section_title_text ); ?></h2>
						<span class="zpc-heading-line"></span>
					</div>
					<?php if ( $s['show_tab_filters'] === 'yes' ) : ?>
						<div class="zpc-tabs">
							<?php foreach ( $tabs as $i => $tab ) : ?>
								<button class="zpc-tab<?php echo $i === 0 ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $i ); ?>">
									<?php echo esc_html( $tab['label'] ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="zpc-layout">
					<div class="zpc-banner-panel">
						<div class="zpc-banner-card">
							<?php if ( $banner_heading ) : ?>
								<h3><?php echo esc_html( $banner_heading ); ?></h3>
							<?php endif; ?>
							<?php if ( $banner_subheading ) : ?>
								<p><?php echo esc_html( $banner_subheading ); ?></p>
							<?php endif; ?>
							<?php if ( $banner_cta_label && $banner_cta_url ) : ?>
								<a class="btn btn-primary zpc-banner-cta" href="<?php echo esc_url( $banner_cta_url ); ?>"<?php echo $banner_cta_target; ?>>
									<?php echo esc_html( $banner_cta_label ); ?> <i class="fa-solid fa-arrow-right-long"></i>
								</a>
							<?php endif; ?>
							<?php if ( $banner_image_url ) : ?>
								<div class="zpc-banner-image">
									<img src="<?php echo esc_url( $banner_image_url ); ?>" alt="">
								</div>
							<?php endif; ?>
						</div>
					</div>

					<div class="zpc-carousel-panel">
						<?php foreach ( $tabs as $i => $tab ) :
							$products   = $this->get_products_for_source( $tab['source'], $tot );
							$pages      = array_chunk( $products, $per );
							$num_pages  = count( $pages );
							?>
							<div class="zpc-panel<?php echo $i === 0 ? ' active' : ''; ?>" data-panel="<?php echo esc_attr( $i ); ?>">
								<div class="zpc-track">
									<?php foreach ( $pages as $pi => $page_products ) : ?>
										<div class="zpc-page<?php echo $pi === 0 ? ' active' : ''; ?>" data-page="<?php echo esc_attr( $pi ); ?>">
											<div class="zpc-products-grid">
												<?php foreach ( $page_products as $product ) :
													if ( ! $product || ! is_a( $product, 'WC_Product' ) ) continue;
													if ( function_exists( 'zendotech_product_card' ) ) zendotech_product_card( $product );
												endforeach; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<?php if ( $num_pages > 1 && $s['show_dots'] === 'yes' ) : ?>
									<div class="zpc-dots">
										<?php for ( $d = 0; $d < $num_pages; $d++ ) : ?>
											<button class="zpc-dot<?php echo $d === 0 ? ' active' : ''; ?>" data-page="<?php echo esc_attr( $d ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Page %d', 'zendotech' ), $d + 1 ) ); ?>"></button>
										<?php endfor; ?>
									</div>
								<?php endif; ?>

								<?php if ( $num_pages > 1 && $s['show_arrows'] === 'yes' ) : ?>
									<div class="zpc-panel-controls">
										<button class="zpc-arrow zpc-prev" aria-label="<?php esc_attr_e( 'Previous Page', 'zendotech' ); ?>"><i class="fa-solid fa-chevron-left"></i></button>
										<button class="zpc-arrow zpc-next" aria-label="<?php esc_attr_e( 'Next Page', 'zendotech' ); ?>"><i class="fa-solid fa-chevron-right"></i></button>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<script>
		(function(){
			var root = document.getElementById('<?php echo esc_js( $uid ); ?>');
			if (!root) return;

			function goToPage(panel, idx) {
				var pages = panel.querySelectorAll('.zpc-page');
				var dots  = panel.querySelectorAll('.zpc-dot');
				if (!pages.length) return;
				if (idx < 0) idx = pages.length - 1;
				if (idx >= pages.length) idx = 0;
				pages.forEach(function(p){ p.classList.remove('active'); });
				dots.forEach(function(d){ d.classList.remove('active'); });
				var pg = panel.querySelector('.zpc-page[data-page="' + idx + '"]');
				if (pg) pg.classList.add('active');
				var dt = panel.querySelector('.zpc-dot[data-page="' + idx + '"]');
				if (dt) dt.classList.add('active');
			}

			// Tab switching
			root.querySelectorAll('.zpc-tab').forEach(function(btn){
				btn.addEventListener('click', function(){
					var idx = this.getAttribute('data-tab');
					root.querySelectorAll('.zpc-tab').forEach(function(t){ t.classList.remove('active'); });
					root.querySelectorAll('.zpc-panel').forEach(function(p){ p.classList.remove('active'); });
					this.classList.add('active');
					var panel = root.querySelector('.zpc-panel[data-panel="' + idx + '"]');
					if (panel) panel.classList.add('active');
				});
			});

			// Dots
			root.querySelectorAll('.zpc-dot').forEach(function(dot){
				dot.addEventListener('click', function(){
					goToPage(this.closest('.zpc-panel'), parseInt(this.getAttribute('data-page'), 10));
				});
			});

			// Arrows
			root.querySelectorAll('.zpc-arrow').forEach(function(arrow){
				arrow.addEventListener('click', function(){
					var panel = this.closest('.zpc-panel');
					var active = panel.querySelector('.zpc-page.active');
					var cur = active ? parseInt(active.getAttribute('data-page'), 10) : 0;
					goToPage(panel, this.classList.contains('zpc-prev') ? cur - 1 : cur + 1);
				});
			});
		})();
		</script>
		<?php
	}

	/** Output shared CSS once per page load (not per-widget-instance) */
	private function maybe_register_styles() {
		if ( wp_style_is( 'zendotech-product-carousel', 'registered' ) ) return;
		$css = '
		.zpc-section { padding: 50px 0 45px; }
		.zpc-heading-row { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; flex-wrap:wrap; border-bottom:1px solid #eef1f5; padding-bottom:22px; margin-bottom:34px; }
		.zpc-heading-text h2 { margin:0; font-size:32px; font-weight:700; letter-spacing:-0.02em; color:var(--text-dark,#15151e); }
		.zpc-heading-line { width:90px; height:4px; border-radius:3px; margin-top:10px; background:var(--accent,#c62b21); box-shadow:0 8px 20px rgba(198,43,33,.35); }
		.zpc-tabs { display:flex; flex-wrap:wrap; gap:12px; margin:0; justify-content:flex-start; }
		.zpc-tab { border:1px dashed #dce0e8; border-radius:999px; padding:7px 24px; font-size:15px; font-weight:600; color:#5c5d70; background:#fff; box-shadow:0 12px 28px rgba(15,16,40,.08); transition:all .25s ease; }
		.zpc-tab.active { color:var(--accent,#c62b21); border-color:var(--accent,#c62b21); border-style:solid; }
		.zpc-tab.active::after { content:none; }
		.zpc-layout { display:grid; grid-template-columns:320px minmax(0,1fr); gap:30px; align-items:flex-start; }
		.zpc-banner-panel { position:sticky; top:25px; align-self:start; }
		.zpc-banner-card { background:#fff; border-radius:28px; padding:30px 26px 24px; box-shadow:0 28px 50px rgba(19,20,45,.08); border:1px solid rgba(0,0,0,.05); display:flex; flex-direction:column; gap:14px; position:relative; overflow:hidden; }
		.zpc-banner-card h3 { margin:0; font-size:26px; font-weight:700; color:#1e1e26; }
		.zpc-banner-card p { margin:0; color:#6c6f83; font-size:16px; line-height:1.6; }
		.zpc-banner-cta { align-self:flex-start; }
		.zpc-banner-image { margin-top:12px; }
		.zpc-banner-image img { width:100%; border-radius:20px; box-shadow:0 18px 40px rgba(0,0,0,.08); height:auto; object-fit:cover; }
		.zpc-panel { display:none; position:relative; padding-top:30px; }
		.zpc-panel.active { display:block; animation:zpcFade .4s ease; }
		@keyframes zpcFade { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
		.zpc-track { position:relative; min-height:350px; }
		.zpc-page { position:absolute; top:0; left:0; width:100%; opacity:0; visibility:hidden; transform:translateX(20px); transition:opacity .4s ease,transform .4s ease,visibility .4s; z-index:1; }
		.zpc-page.active { position:relative; opacity:1; visibility:visible; transform:translateX(0); z-index:2; }
		.zpc-products-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:18px; }
		.zpc-products-grid .product-card { width:100%; }
		.zpc-dots { display:flex; justify-content:flex-start; gap:10px; margin-top:32px; }
		.zpc-dot { width:22px; height:6px; border-radius:999px; border:none; background:#d7d9e0; cursor:pointer; transition:all .25s ease; }
		.zpc-dot.active { width:40px; background:var(--accent,#c62b21); }
		.zpc-panel-controls { position:absolute; top:12px; right:0; display:flex; gap:8px; z-index:5; }
		.zpc-arrow { width:44px; height:44px; border-radius:10px; border:1px solid #dfe2e7; background:#fff; color:#1d1d2d; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow:0 14px 30px rgba(11,14,40,.12); cursor:pointer; transition:all .25s ease; }
		.zpc-arrow:hover { border-color:var(--accent,#c62b21); color:var(--accent,#c62b21); }
		@media (max-width:1200px) { .zpc-layout { grid-template-columns:1fr; } .zpc-panel-controls { position:static; margin-bottom:18px; justify-content:flex-end; } .zpc-page { position:relative; } }
		@media (max-width:768px) { .zpc-heading-row { flex-direction:column; align-items:flex-start; } .zpc-tabs { justify-content:flex-start; } .zpc-tab { padding:6px 18px; font-size:14px; } }
		';
		wp_register_style( 'zendotech-product-carousel', false );
		wp_add_inline_style( 'zendotech-product-carousel', $css );
	}

	private function source_options() {
		return [
			'featured' => __( 'Featured', 'zendotech' ),
			'on_sale'  => __( 'On Sale', 'zendotech' ),
			'popular'  => __( 'Top Rated / Popular', 'zendotech' ),
			'latest'   => __( 'Latest', 'zendotech' ),
		];
	}

	private function get_products_for_source( $source, $limit ) {
		if ( ! function_exists( 'wc_get_products' ) ) return [];

		switch ( $source ) {
			case 'on_sale':
				$ids = wc_get_product_ids_on_sale();
				if ( empty( $ids ) ) return wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );
				$q = new WP_Query( [ 'post_type' => 'product', 'posts_per_page' => $limit, 'post__in' => $ids, 'orderby' => 'rand', 'post_status' => 'publish' ] );
				$products = [];
				while ( $q->have_posts() ) { $q->the_post(); $products[] = wc_get_product( get_the_ID() ); }
				wp_reset_postdata();
				return $products;

			case 'popular':
				return wc_get_products( [ 'limit' => $limit, 'orderby' => 'rating', 'order' => 'DESC', 'status' => 'publish' ] );

			case 'featured':
				$prods = wc_get_products( [ 'limit' => $limit, 'featured' => true, 'status' => 'publish' ] );
				return ! empty( $prods ) ? $prods : wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );

			default:
				return wc_get_products( [ 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );
		}
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Product_Carousel_Widget() );
} );
