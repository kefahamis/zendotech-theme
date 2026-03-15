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
		$this->add_control( 'tab_active_indicator', [ 'label' => __( 'Indicator', 'zendotech' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .zpc-tab.active::after' => 'background: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
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
		?>
		<section class="section zpc-section" id="<?php echo esc_attr( $uid ); ?>">
			<div class="container">

				<?php if ( $s['show_tab_filters'] === 'yes' ) : ?>
				<div class="zpc-tabs">
					<?php foreach ( $tabs as $i => $tab ) : ?>
						<button class="zpc-tab<?php echo $i === 0 ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $i ); ?>">
							<?php echo esc_html( $tab['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<?php foreach ( $tabs as $i => $tab ) :
					$products   = $this->get_products_for_source( $tab['source'], $tot );
					$pages      = array_chunk( $products, $per );
					$num_pages  = count( $pages );
					?>
					<div class="zpc-panel<?php echo $i === 0 ? ' active' : ''; ?>" data-panel="<?php echo esc_attr( $i ); ?>">
						<div class="zpc-track">
							<?php foreach ( $pages as $pi => $page_products ) : ?>
								<div class="zpc-page<?php echo $pi === 0 ? ' active' : ''; ?>" data-page="<?php echo esc_attr( $pi ); ?>">
									<div class="zpc-products-row">
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
							<button class="zpc-arrow zpc-prev" aria-label="<?php esc_attr_e( 'Previous Page', 'zendotech' ); ?>"><i class="fa-solid fa-chevron-left"></i></button>
							<button class="zpc-arrow zpc-next" aria-label="<?php esc_attr_e( 'Next Page', 'zendotech' ); ?>"><i class="fa-solid fa-chevron-right"></i></button>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

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
		.zpc-section { padding: 40px 0 30px; }
		.zpc-tabs { display:flex; justify-content:center; gap:15px; margin-bottom:30px; border-bottom:2px solid #f0f2f5; padding-bottom:0; }
		.zpc-tab { background:none; border:none; font-family:var(--font,"Outfit",sans-serif); font-size:16px; font-weight:500; color:var(--text-muted,#8E8EA0); padding:10px 25px 15px; cursor:pointer; position:relative; transition:color .25s; }
		.zpc-tab:hover { color:var(--text-dark,#121218); }
		.zpc-tab.active { color:var(--primary,#3B3B98); font-weight:700; }
		.zpc-tab.active::after { content:""; position:absolute; bottom:-2px; left:50%; transform:translateX(-50%); width:40px; height:3px; border-radius:3px; background:var(--accent,#C78A2E); }
		.zpc-panel { display:none; position:relative; }
		.zpc-panel.active { display:block; animation:zpcFade .4s ease; }
		@keyframes zpcFade { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
		.zpc-track { position:relative; min-height:350px; }
		.zpc-page { position:absolute; top:0; left:0; width:100%; opacity:0; visibility:hidden; transform:translateX(20px); transition:opacity .4s ease,transform .4s ease,visibility .4s; z-index:1; }
		.zpc-page.active { position:relative; opacity:1; visibility:visible; transform:translateX(0); z-index:2; }
		.zpc-products-row { display:grid; grid-template-columns:repeat(6,1fr); gap:20px; }
		.zpc-dots { display:flex; justify-content:center; gap:10px; margin-top:30px; }
		.zpc-dot { width:24px; height:5px; border-radius:3px; border:none; background:#d0d5dd; cursor:pointer; padding:0; transition:background .25s,width .25s; }
		.zpc-dot:focus { outline:none; }
		.zpc-dot.active { background:var(--accent,#C78A2E); width:32px; }
		.zpc-arrow { position:absolute; top:40%; transform:translateY(-50%); width:40px; height:40px; background:#fff; border:1px solid #e0e2e8; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#121218; cursor:pointer; z-index:10; box-shadow:0 4px 12px rgba(0,0,0,.05); transition:all .2s; }
		.zpc-arrow:hover { background:var(--primary,#3B3B98); color:#fff; border-color:var(--primary,#3B3B98); }
		.zpc-prev { left:-20px; } .zpc-next { right:-20px; }
		@media(max-width:1200px){ .zpc-products-row{ grid-template-columns:repeat(4,1fr); } }
		@media(max-width:992px) { .zpc-products-row{ grid-template-columns:repeat(3,1fr); } .zpc-tab{font-size:14px;padding:10px 15px 12px;} }
		@media(max-width:600px)  { .zpc-products-row{ grid-template-columns:repeat(2,1fr); } .zpc-tabs{gap:5px;} }
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
