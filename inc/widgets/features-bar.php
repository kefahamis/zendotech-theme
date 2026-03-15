<?php
/**
 * Zendotech Features Bar Widget
 * Fixed: icon field uses TEXT (FA class string) not ICONS control to avoid
 *        Elementor Icon Library loading issues; safe null checks; removed
 *        fragile FA6-to-FA5 mapping (user supplies correct class directly)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Features_Bar_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_features_bar'; }
	public function get_title()      { return __( 'Features Bar', 'zendotech' ); }
	public function get_icon()       { return 'eicon-info-box'; }
	public function get_categories() { return [ 'zendotech' ]; }

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => __( 'Features Bar', 'zendotech' ),
		] );

		$this->add_control( 'features', [
			'label'       => __( 'Features', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => [
				[
					'name'        => 'icon',
					'label'       => __( 'Icon Class (Font Awesome)', 'zendotech' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => 'fa-solid fa-truck-fast',
					'description' => __( 'e.g. fa-solid fa-truck-fast', 'zendotech' ),
					'label_block' => true,
				],
				[
					'name'    => 'title',
					'label'   => __( 'Title', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => 'Free Shipping',
				],
				[
					'name'    => 'description',
					'label'   => __( 'Description', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => 'On orders over $75',
				],
			],
			'default'     => [
				[ 'icon' => 'fa-solid fa-truck-fast',    'title' => 'Free Shipping',    'description' => 'On orders over $75' ],
				[ 'icon' => 'fa-solid fa-rotate-left',   'title' => '30-Day Returns',   'description' => 'Hassle-free returns' ],
				[ 'icon' => 'fa-solid fa-shield-halved', 'title' => '2-Year Warranty',  'description' => 'On all audio products' ],
				[ 'icon' => 'fa-solid fa-headset',       'title' => 'Expert Support',   'description' => 'Audio specialists 24/7' ],
			],
			'title_field' => '{{{ title }}}',
		] );

		$this->end_controls_section();

		/* ---- STYLE ---- */

		$this->start_controls_section( 'section_style_container', [
			'label' => __( 'Container', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_group_control( \Elementor\Group_Control_Background::get_type(), [
			'name'     => 'container_background',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .features-bar',
		] );
		$this->add_control( 'container_padding', [
			'label'      => __( 'Padding', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .features-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_icon', [
			'label' => __( 'Icon', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'icon_color', [
			'label'     => __( 'Icon Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .feat-icon i' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'icon_bg_color', [
			'label'     => __( 'Icon Background', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .feat-icon' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'icon_size', [
			'label'     => __( 'Icon Size', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 10, 'max' => 80 ] ],
			'selectors' => [ '{{WRAPPER}} .feat-icon i' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_control( 'icon_radius', [
			'label'      => __( 'Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .feat-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_text', [
			'label' => __( 'Text', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'title_heading', [
			'label' => __( 'Title', 'zendotech' ),
			'type'  => \Elementor\Controls_Manager::HEADING,
		] );
		$this->add_control( 'title_color', [
			'label'     => __( 'Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .feat-item h4' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .feat-item h4',
		] );
		$this->add_control( 'desc_heading', [
			'label'     => __( 'Description', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'desc_color', [
			'label'     => __( 'Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .feat-item p' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'desc_typography',
			'selector' => '{{WRAPPER}} .feat-item p',
		] );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="features-bar">
			<div class="container">
				<div class="features-row">
					<?php foreach ( $s['features'] as $feat ) :
						$icon = ! empty( $feat['icon'] ) ? trim( $feat['icon'] ) : 'fa-solid fa-star';
						?>
						<div class="feat-item">
							<div class="feat-icon">
								<i class="<?php echo esc_attr( $icon ); ?>"></i>
							</div>
							<div>
								<h4><?php echo esc_html( $feat['title'] ); ?></h4>
								<p><?php echo esc_html( $feat['description'] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Features_Bar_Widget() );
} );
