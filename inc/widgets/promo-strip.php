<?php
/**
 * Zendotech Promo Strip Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Promo_Strip_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_promo_strip';
    }
    public function get_title()
    {
        return __('Promo Strip', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-call-to-action';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Promo Strip', 'zendotech'),
        ]);

        $this->add_control('icon', [
            'label' => __('Icon Class', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'fa-solid fa-music',
        ]);

        $this->add_control('text', [
            'label' => __('Promo Text', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Free guitar strings pack with every instrument order — limited time only!',
        ]);

        $this->add_control('btn_text', [
            'label' => __('Button Text', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Grab Yours',
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
                'selector' => '{{WRAPPER}} .promo-strip',
            ]
        );

        $this->add_control('container_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .promo-strip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'label' => __('Border', 'zendotech'),
                'selector' => '{{WRAPPER}} .promo-strip',
            ]
        );

        $this->add_control('container_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .promo-strip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Content Style ---
        $this->start_controls_section('section_style_content', [
            'label' => __('Content', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('icon_heading', [
            'label' => __('Icon', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('icon_color', [
            'label' => __('Icon Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-left i' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('icon_size', [
            'label' => __('Icon Size', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .promo-left i' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('text_heading', [
            'label' => __('Text', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('text_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-left h3' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .promo-left h3',
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
                'name' => 'btn_typography',
                'selector' => '{{WRAPPER}} .promo-strip .btn',
            ]
        );

        $this->start_controls_tabs('tabs_btn_style');

        $this->start_controls_tab('tab_btn_normal', [
            'label' => __('Normal', 'zendotech'),
        ]);

        $this->add_control('btn_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_border_color', [
            'label' => __('Border Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_btn_hover', [
            'label' => __('Hover', 'zendotech'),
        ]);

        $this->add_control('btn_hover_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_hover_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_hover_border_color', [
            'label' => __('Border Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn:hover' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('btn_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]);

        $this->add_control('btn_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .promo-strip .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="promo-strip">
            <div class="container">
                <div class="promo-inner">
                    <div class="promo-left">
                        <i class="<?php echo esc_attr($s['icon']); ?>"></i>
                        <div>
                            <h3>
                                <?php echo esc_html($s['text']); ?>
                            </h3>
                        </div>
                    </div>
                    <a href="<?php echo esc_url($s['btn_url']['url'] ?? '#'); ?>" class="btn btn-white">
                        <?php echo esc_html($s['btn_text']); ?> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Promo_Strip_Widget());
