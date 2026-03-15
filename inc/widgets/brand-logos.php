<?php
/**
 * Zendotech Brand Logos Widget
 * Fixed: null-safe image url, safe style output, proper add_action
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Brand_Logos_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_brand_logos'; }
	public function get_title()      { return __( 'Brand Logos', 'zendotech' ); }
	public function get_icon()       { return 'eicon-logo'; }
	public function get_categories() { return [ 'zendotech' ]; }

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => __( 'Brand Logos', 'zendotech' ),
		] );

		$this->add_control( 'brands', [
			'label'       => __( 'Brands', 'zendotech' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => [
				[
					'name'    => 'name',
					'label'   => __( 'Brand Name (text logo)', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => 'BOSE',
				],
				[
					'name'    => 'logo_image',
					'label'   => __( 'Logo Image (optional)', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => [ 'url' => '' ],
				],
				[
					'name'    => 'font_size',
					'label'   => __( 'Font Size (px)', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 18,
				],
				[
					'name'    => 'font_weight',
					'label'   => __( 'Font Weight', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => '800',
					'options' => [
						'400' => '400', '500' => '500', '600' => '600',
						'700' => '700', '800' => '800', '900' => '900',
					],
				],
				[
					'name'    => 'color',
					'label'   => __( 'Color', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::COLOR,
					'default' => '#333333',
				],
				[
					'name'    => 'letter_spacing',
					'label'   => __( 'Letter Spacing (px)', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 2,
				],
				[
					'name'    => 'italic',
					'label'   => __( 'Italic', 'zendotech' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
				],
				[
					'name'        => 'font_family',
					'label'       => __( 'Font Family', 'zendotech' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'e.g. serif, monospace. Leave blank for default.', 'zendotech' ),
				],
			],
			'default'     => [
				[ 'name' => 'BOSE',       'font_size' => 18, 'font_weight' => '800', 'color' => '#333333', 'letter_spacing' => 2, 'italic' => '', 'font_family' => '' ],
				[ 'name' => 'Sennheiser', 'font_size' => 18, 'font_weight' => '700', 'color' => '#333333', 'letter_spacing' => 1, 'italic' => 'yes', 'font_family' => '' ],
				[ 'name' => 'SONY',       'font_size' => 20, 'font_weight' => '800', 'color' => '#333333', 'letter_spacing' => 1, 'italic' => '', 'font_family' => '' ],
				[ 'name' => 'Marshall',   'font_size' => 18, 'font_weight' => '700', 'color' => '#C41E3A', 'letter_spacing' => 2, 'italic' => '', 'font_family' => 'serif' ],
				[ 'name' => 'JBL',        'font_size' => 18, 'font_weight' => '700', 'color' => '#333333', 'letter_spacing' => 2, 'italic' => '', 'font_family' => '' ],
				[ 'name' => 'Fender',     'font_size' => 16, 'font_weight' => '700', 'color' => '#C30F1F', 'letter_spacing' => 1, 'italic' => '', 'font_family' => '' ],
			],
			'title_field' => '{{{ name }}}',
		] );

		$this->end_controls_section();

		/* ---- STYLE ---- */
		$this->start_controls_section( 'section_style_images', [
			'label' => __( 'Logo Images', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_responsive_control( 'image_height', [
			'label'     => __( 'Max Height', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'default'   => [ 'size' => 50, 'unit' => 'px' ],
			'range'     => [ 'px' => [ 'min' => 20, 'max' => 200 ] ],
			'selectors' => [ '{{WRAPPER}} .brand-logo img' => 'max-height: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_control( 'image_opacity', [
			'label'     => __( 'Default Opacity', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'max' => 1, 'min' => 0.1, 'step' => 0.05 ] ],
			'selectors' => [ '{{WRAPPER}} .brand-logo' => 'opacity: {{SIZE}};' ],
		] );
		$this->add_control( 'image_opacity_hover', [
			'label'     => __( 'Hover Opacity', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'max' => 1, 'min' => 0.1, 'step' => 0.05 ] ],
			'selectors' => [ '{{WRAPPER}} .brand-logo:hover' => 'opacity: {{SIZE}};' ],
		] );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="brands-section">
			<div class="container">
				<div class="brand-logos">
					<?php foreach ( $s['brands'] as $brand ) :
						$has_image = ! empty( $brand['logo_image']['url'] );

						if ( ! $has_image ) {
							// Build text-logo inline style safely
							$style_parts = [
								'font-size:'      . intval( $brand['font_size'] ) . 'px',
								'font-weight:'    . esc_attr( $brand['font_weight'] ),
								'color:'          . esc_attr( $brand['color'] ?: '#333' ),
								'letter-spacing:' . intval( $brand['letter_spacing'] ) . 'px',
							];
							if ( $brand['italic'] === 'yes' ) $style_parts[] = 'font-style:italic';
							if ( ! empty( $brand['font_family'] ) ) $style_parts[] = 'font-family:' . esc_attr( $brand['font_family'] );
							$style = implode( ';', $style_parts );
						}
						?>
						<div class="brand-logo">
							<?php if ( $has_image ) : ?>
								<img src="<?php echo esc_url( $brand['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( $brand['name'] ); ?>" style="max-height:50px;width:auto;object-fit:contain;">
							<?php else : ?>
								<span style="<?php echo $style; ?>"><?php echo esc_html( $brand['name'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Brand_Logos_Widget() );
} );
