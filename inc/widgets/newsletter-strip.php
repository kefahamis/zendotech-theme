<?php
/**
 * Zendotech Newsletter Strip Widget
 * Fixed: Background group control selector targets .newsletter-strip (the section)
 *        not .ns-inner so the background renders; null-safe btn_url
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Zendotech_Newsletter_Strip_Widget extends \Elementor\Widget_Base {

	public function get_name()       { return 'zendotech_newsletter_strip'; }
	public function get_title()      { return __( 'Newsletter Strip', 'zendotech' ); }
	public function get_icon()       { return 'eicon-email-field'; }
	public function get_categories() { return [ 'zendotech' ]; }

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => __( 'Newsletter Strip', 'zendotech' ),
		] );

		$this->add_control( 'image', [
			'label'   => __( 'Image', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=120&h=120&fit=crop' ],
		] );
		$this->add_control( 'heading', [
			'label'   => __( 'Heading', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => 'Exclusive deals for audiophiles — up to 40% off premium audio gear!',
		] );
		$this->add_control( 'subtitle', [
			'label'   => __( 'Subtitle', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Subscribe and never miss a beat.',
		] );
		$this->add_control( 'btn_text', [
			'label'   => __( 'Button Text', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'See All Deals',
		] );
		$this->add_control( 'btn_url', [
			'label'   => __( 'Button URL', 'zendotech' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->end_controls_section();

		/* ---- STYLE ---- */

		// Container (targets the full section for background, inner for padding)
		$this->start_controls_section( 'section_style_container', [
			'label' => __( 'Container', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_group_control( \Elementor\Group_Control_Background::get_type(), [
			'name'     => 'container_background',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .newsletter-strip',
		] );
		$this->add_control( 'container_padding', [
			'label'      => __( 'Section Padding', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .newsletter-strip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->add_control( 'inner_padding', [
			'label'      => __( 'Inner Padding', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .ns-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->add_control( 'container_radius', [
			'label'      => __( 'Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .newsletter-strip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->end_controls_section();

		// Heading
		$this->start_controls_section( 'section_style_heading', [
			'label' => __( 'Heading', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'heading_color', [
			'label'     => __( 'Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .ns-content h3' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'heading_typography',
			'selector' => '{{WRAPPER}} .ns-content h3',
		] );
		$this->end_controls_section();

		// Subtitle
		$this->start_controls_section( 'section_style_subtitle', [
			'label' => __( 'Subtitle', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_control( 'subtitle_color', [
			'label'     => __( 'Color', 'zendotech' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .ns-content p' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'subtitle_typography',
			'selector' => '{{WRAPPER}} .ns-content p',
		] );
		$this->end_controls_section();

		// Button
		$this->start_controls_section( 'section_style_button', [
			'label' => __( 'Button', 'zendotech' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'selector' => '{{WRAPPER}} .newsletter-strip .btn',
		] );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'btn_normal', [ 'label' => __( 'Normal', 'zendotech' ) ] );
		$this->add_control( 'btn_color',    [ 'label' => __( 'Text', 'zendotech' ),       'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .newsletter-strip .btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_bg_color', [ 'label' => __( 'Background', 'zendotech' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .newsletter-strip .btn' => 'background-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'btn_hover', [ 'label' => __( 'Hover', 'zendotech' ) ] );
		$this->add_control( 'btn_hover_color',    [ 'label' => __( 'Text', 'zendotech' ),       'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .newsletter-strip .btn:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_hover_bg_color', [ 'label' => __( 'Background', 'zendotech' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .newsletter-strip .btn:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control( 'btn_radius', [
			'label'      => __( 'Border Radius', 'zendotech' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .newsletter-strip .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			'separator'  => 'before',
		] );
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$btn_url = ! empty( $s['btn_url']['url'] ) ? $s['btn_url']['url'] : '#';
		?>
		<section class="newsletter-strip">
			<div class="container">
				<div class="ns-inner">
					<div class="ns-content">
						<?php if ( ! empty( $s['image']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $s['image']['url'] ); ?>" alt="Newsletter" class="ns-img">
						<?php endif; ?>
						<div>
							<h3><?php echo esc_html( $s['heading'] ); ?></h3>
							<p><?php echo esc_html( $s['subtitle'] ); ?></p>
						</div>
					</div>
					<a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn-primary">
						<?php echo esc_html( $s['btn_text'] ); ?>
					</a>
				</div>
			</div>
		</section>
		<?php
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new Zendotech_Newsletter_Strip_Widget() );
} );
