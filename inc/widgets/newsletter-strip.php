<?php
/**
 * Zendotech Newsletter Strip Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Newsletter_Strip_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_newsletter_strip';
    }
    public function get_title()
    {
        return __('Newsletter Strip', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-email-field';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Newsletter Strip', 'zendotech'),
        ]);

        $this->add_control('image', [
            'label' => __('Image', 'zendotech'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=120&h=120&fit=crop',
            ],
        ]);

        $this->add_control('heading', [
            'label' => __('Heading', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Exclusive deals for audiophiles — up to 40% off premium audio gear!',
        ]);

        $this->add_control('subtitle', [
            'label' => __('Subtitle', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Subscribe and never miss a beat.',
        ]);

        $this->add_control('btn_text', [
            'label' => __('Button Text', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'See All Deals',
        ]);

        $this->add_control('btn_url', [
            'label' => __('Button URL', 'zendotech'),
            'type' => \Elementor\Controls_Manager::URL,
            'default' => ['url' => '#'],
        ]);

        $this->end_controls_section();

        // ----------------- STYLE TAB ----------------- //

        // --- Container Style ---
        $this->start_controls_section('section_style_container', [
            'label' => __('Container', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'container_background',
                'label' => __('Background', 'zendotech'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ns-inner',
            ]
        );

        $this->add_control('container_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .ns-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('container_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .ns-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'label' => __('Border', 'zendotech'),
                'selector' => '{{WRAPPER}} .ns-inner',
            ]
        );

        $this->end_controls_section();

        // --- Heading Style ---
        $this->start_controls_section('section_style_heading', [
            'label' => __('Heading', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('heading_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ns-content h3' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .ns-content h3',
            ]
        );
        
        $this->add_control('heading_margin', [
            'label' => __('Margin', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .ns-content h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Subtitle Style ---
        $this->start_controls_section('section_style_subtitle', [
            'label' => __('Subtitle', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('subtitle_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ns-content p' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .ns-content p',
            ]
        );

        $this->end_controls_section();

        // --- Button Style ---
        $this->start_controls_section('section_style_button', [
            'label' => __('Button', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .btn-primary',
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        // Normal State
        $this->start_controls_tab('tab_button_normal', [
            'label' => __('Normal', 'zendotech'),
        ]);

        $this->add_control('button_text_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .btn-primary' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .btn-primary' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        // Hover State
        $this->start_controls_tab('tab_button_hover', [
            'label' => __('Hover', 'zendotech'),
        ]);

        $this->add_control('button_hover_text_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .btn-primary:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_hover_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .btn-primary:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('button_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .btn-primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]);

        $this->add_control('button_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .btn-primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="newsletter-strip">
            <div class="container">
                <div class="ns-inner">
                    <div class="ns-content">
                        <?php if (!empty($s['image']['url'])): ?>
                            <img src="<?php echo esc_url($s['image']['url']); ?>" alt="Newsletter" class="ns-img">
                        <?php endif; ?>
                        <div>
                            <h3>
                                <?php echo esc_html($s['heading']); ?>
                            </h3>
                            <p>
                                <?php echo esc_html($s['subtitle']); ?>
                            </p>
                        </div>
                    </div>
                    <a href="<?php echo esc_url($s['btn_url']['url'] ?? '#'); ?>" class="btn btn-primary">
                        <?php echo esc_html($s['btn_text']); ?>
                    </a>
                </div>
            </div>
        </section>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Newsletter_Strip_Widget());
