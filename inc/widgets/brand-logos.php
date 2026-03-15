<?php
/**
 * Zendotech Brand Logos Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Brand_Logos_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_brand_logos';
    }
    public function get_title()
    {
        return __('Brand Logos', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-logo';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Brand Logos', 'zendotech'),
        ]);

        $this->add_control('brands', [
            'label' => __('Brands', 'zendotech'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'name',
                    'label' => __('Brand Name', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'BOSE',
                ],
                [
                    'name' => 'font_size',
                    'label' => __('Font Size (px)', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 18,
                ],
                [
                    'name' => 'font_weight',
                    'label' => __('Font Weight', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '800',
                    'options' => [
                        '400' => '400',
                        '500' => '500',
                        '600' => '600',
                        '700' => '700',
                        '800' => '800',
                        '900' => '900',
                    ],
                ],
                [
                    'name' => 'color',
                    'label' => __('Color', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#333',
                ],
                [
                    'name' => 'letter_spacing',
                    'label' => __('Letter Spacing (px)', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 2,
                ],
                [
                    'name' => 'italic',
                    'label' => __('Italic', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => '',
                ],
                [
                    'name' => 'font_family',
                    'label' => __('Font Family', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                    'description' => __('e.g. serif, monospace. Leave blank for default.', 'zendotech'),
                ],
                [
                    'name' => 'logo_image',
                    'label' => __('Logo Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => ['url' => ''],
                    'description' => __('Upload a brand logo image. If set, this replaces the text logo.', 'zendotech'),
                ],
            ],
            'default' => [
                [
                    'name' => 'BOSE',
                    'font_size' => 18,
                    'font_weight' => '800',
                    'color' => '#333',
                    'letter_spacing' => 2,
                    'italic' => '',
                    'font_family' => '',
                ],
                [
                    'name' => 'Sennheiser',
                    'font_size' => 18,
                    'font_weight' => '700',
                    'color' => '#333',
                    'letter_spacing' => 1,
                    'italic' => 'yes',
                    'font_family' => '',
                ],
                [
                    'name' => 'SONY',
                    'font_size' => 20,
                    'font_weight' => '800',
                    'color' => '#333',
                    'letter_spacing' => 1,
                    'italic' => '',
                    'font_family' => '',
                ],
                [
                    'name' => 'Marshall',
                    'font_size' => 18,
                    'font_weight' => '700',
                    'color' => '#C41E3A',
                    'letter_spacing' => 2,
                    'italic' => '',
                    'font_family' => 'serif',
                ],
                [
                    'name' => 'JBL',
                    'font_size' => 18,
                    'font_weight' => '700',
                    'color' => '#333',
                    'letter_spacing' => 2,
                    'italic' => '',
                    'font_family' => '',
                ],
                [
                    'name' => 'Fender',
                    'font_size' => 16,
                    'font_weight' => '700',
                    'color' => '#C30F1F',
                    'letter_spacing' => 1,
                    'italic' => '',
                    'font_family' => '',
                ],
            ],
            'title_field' => '{{{ name }}}',
        ]);

        $this->end_controls_section();

        // ----------------- STYLE TAB ----------------- //
        $this->start_controls_section('section_style_images', [
            'label' => __('Images', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('image_height', [
            'label' => __('Max Height', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'default' => [
                'size' => 50,
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 20,
                    'max' => 200,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-logo img' => 'max-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->start_controls_tabs('tabs_image_style');

        $this->start_controls_tab('tab_image_normal', [
            'label' => __('Normal', 'zendotech'),
        ]);

        $this->add_control('image_opacity', [
            'label' => __('Opacity', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-logo img' => 'opacity: {{SIZE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters',
                'selector' => '{{WRAPPER}} .brand-logo img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('tab_image_hover', [
            'label' => __('Hover', 'zendotech'),
        ]);

        $this->add_control('image_opacity_hover', [
            'label' => __('Opacity', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-logo img:hover' => 'opacity: {{SIZE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters_hover',
                'selector' => '{{WRAPPER}} .brand-logo img:hover',
            ]
        );

        $this->add_control('hover_transition', [
            'label' => __('Transition Duration', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 3,
                    'step' => 0.1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-logo img' => 'transition-duration: {{SIZE}}s',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('image_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .brand-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="brands-section">
            <div class="container">
                <div class="brand-logos">
                    <?php foreach ($s['brands'] as $brand):
                        $has_image = !empty($brand['logo_image']) && !empty($brand['logo_image']['url']);
                        $style = 'font-size:' . intval($brand['font_size']) . 'px;'
                            . 'font-weight:' . esc_attr($brand['font_weight']) . ';'
                            . 'color:' . esc_attr($brand['color']) . ';'
                            . 'letter-spacing:' . intval($brand['letter_spacing']) . 'px';
                        if ($brand['italic'] === 'yes')
                            $style .= ';font-style:italic';
                        if (!empty($brand['font_family']))
                            $style .= ';font-family:' . esc_attr($brand['font_family']);
                        ?>
                        <div class="brand-logo">
                            <?php if ($has_image): ?>
                                <img src="<?php echo esc_url($brand['logo_image']['url']); ?>" alt="<?php echo esc_attr($brand['name']); ?>" style="width:auto;object-fit:contain;transition:all 0.3s;">
                            <?php else: ?>
                                <span style="<?php echo $style; ?>">
                                    <?php echo esc_html($brand['name']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Brand_Logos_Widget());
