<?php
/**
 * Zendotech Category Grid Widget
 * Fixed: WC null check, safe fallback image, proper add_action
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Category_Grid_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_category_grid'; }
	public function get_title()      { return __( 'Category Grid', 'zendotech' ); }
	public function get_icon()       { return 'eicon-gallery-grid'; }
	public function get_categories() { return [ 'zendotech' ]; }

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => __( 'Category Grid', 'zendotech' ),
		] );

		$this->add_control( 'section_title', [
			'label'   => __( 'Section Title', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Shop by Category',
		] );
		$this->add_control( 'cat_count', [
			'label'   => __( 'Number of Categories', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 6,
			'min'     => 2,
			'max'     => 12,
		] );
		$this->add_control( 'fallback_image', [
			'label'       => __( 'Fallback Image', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::MEDIA,
			'default'     => [ 'url' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop' ],
			'description' => __( 'Used when a category has no thumbnail.', 'zendotech' ),
		] );

		$this->end_controls_section();

		/* ---- STYLE ---- */
		$this->start_controls_section( 'section_style_card', [
			'label' => __( 'Category Card', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'icon_size', [
			'label'      => __( 'Icon Size', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 60, 'max' => 200 ] ],
			'selectors'  => [ '{{WRAPPER}} .cat-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_control( 'label_color', [
			'label'     => __( 'Label Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .cat-card span' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'label_typography',
			'selector' => '{{WRAPPER}} .cat-card span',
		] );
		$this->add_control( 'icon_bg', [
			'label'     => __( 'Icon Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .cat-icon' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'icon_hover_border', [
			'label'     => __( 'Hover Border Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .cat-card:hover .cat-icon' => 'border-color: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$fallback = ! empty( $s['fallback_image']['url'] ) ? $s['fallback_image']['url'] : 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop';
		$grid_id  = 'zct-' . wp_unique_id();

		if ( ! function_exists( 'get_terms' ) || ! taxonomy_exists( 'product_cat' ) ) {
			echo '<p>' . __( 'WooCommerce product categories not found.', 'zendotech' ) . '</p>';
			return;
		}

		$cats = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'parent'     => 0,
			'number'     => intval( $s['cat_count'] ),
			'exclude'    => [ get_option( 'default_product_cat' ) ],
		] );

		if ( is_wp_error( $cats ) || empty( $cats ) ) {
			echo '<p>' . __( 'No categories found.', 'zendotech' ) . '</p>';
			return;
		}
		?>
		<section class="section">
			<div class="container">
				<div class="section-head">
					<h2><?php echo esc_html( $s['section_title'] ); ?></h2>
				</div>
				<div id="<?php echo esc_attr( $grid_id ); ?>" class="zct-grid-wrapper">
				<div class="zct-slider-wrap">
					<div class="zct-slider">
						<?php foreach ( $cats as $hc ) :
							$thumb_id  = get_term_meta( $hc->term_id, 'thumbnail_id', true );
							$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : $fallback;
							$thumb_url = $thumb_url ?: $fallback;
							?>
							<a href="<?php echo esc_url( get_term_link( $hc ) ); ?>" class="cat-card">
								<div class="cat-icon">
								<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $hc->name ); ?>">
								</div>
								<span><?php echo esc_html( $hc->name ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="zct-slider-nav-wrap">
					<button class="zct-slider-nav zct-slider-prev" aria-label="<?php esc_attr_e( 'Previous categories', 'zendotech' ); ?>"><i class="fa-solid fa-chevron-left"></i></button>
					<button class="zct-slider-nav zct-slider-next" aria-label="<?php esc_attr_e( 'Next categories', 'zendotech' ); ?>"><i class="fa-solid fa-chevron-right"></i></button>
				</div>
			</div>
		</section>
		<style>
		#<?php echo esc_html( $grid_id ); ?> .zct-slider-wrap { position:relative; background:#fff; border-radius:36px; box-shadow:0 20px 45px rgba(10,12,40,.15); padding:24px 32px 20px; margin-top:30px; overflow:hidden; }
		#<?php echo esc_html( $grid_id ); ?> .zct-slider { display:flex; gap:28px; overflow-x:auto; scroll-behavior:smooth; padding:8px 12px 0; }
		#<?php echo esc_html( $grid_id ); ?> .zct-slider::-webkit-scrollbar { display:none; }
		#<?php echo esc_html( $grid_id ); ?> .cat-card { display:flex; flex-direction:column; align-items:center; gap:12px; min-width:150px; text-decoration:none; color:#1a1b29; font-weight:600; text-align:center; }
		#<?php echo esc_html( $grid_id ); ?> .cat-icon { width:120px; height:120px; border-radius:50%; border:1px solid rgba(15,21,38,.08); display:flex; align-items:center; justify-content:center; background:#fafafa; box-shadow:0 18px 30px rgba(15,21,38,.08); transition:all .35s ease; }
		#<?php echo esc_html( $grid_id ); ?> .cat-icon img { width:70%; height:70%; object-fit:contain; }
		#<?php echo esc_html( $grid_id ); ?> .cat-card span { font-size:16px; }
		#<?php echo esc_html( $grid_id ); ?> .cat-card:hover .cat-icon { transform:translateY(-6px); border-color:var(--accent,#c62b21); }
		#<?php echo esc_html( $grid_id ); ?> .zct-slider-nav-wrap { display:flex; justify-content:center; gap:16px; margin-top:20px; }
		#<?php echo esc_html( $grid_id ); ?> .zct-slider-nav { width:40px; height:40px; border-radius:50%; border:none; background:#fff; box-shadow:0 12px 30px rgba(10,12,40,.2); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .3s ease; }
		#<?php echo esc_html( $grid_id ); ?> .zct-slider-nav:hover { background:#f5f5f5; transform:scale(1.1); }
		@media (max-width:900px) { #<?php echo esc_html( $grid_id ); ?> .zct-slider-wrap { padding:18px 12px; } #<?php echo esc_html( $grid_id ); ?> .cat-card { min-width:120px; gap:8px; } }
		</style>
		<script>
		(function(){
			var root = document.getElementById('<?php echo esc_js( $grid_id ); ?>');
			if (!root) return;
			var track = root.querySelector('.zct-slider');
			var prev = root.querySelector('.zct-slider-prev');
			var next = root.querySelector('.zct-slider-next');
			function scroll(distance){
				if (!track) return;
				track.scrollBy({ left: distance, behavior: 'smooth' });
			}
			prev && prev.addEventListener('click', function(){ scroll(-track.clientWidth + 40); });
			next && next.addEventListener('click', function(){ scroll(track.clientWidth - 40); });
		})();
		</script>
		<?php
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Category_Grid_Widget() );
} );
