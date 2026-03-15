<?php
/**
 * Zendotech Brand Banners Widget (Dual)
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Brand_Banners_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_brand_banners';
    }
    public function get_title()
    {
        return __('Brand Banners', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-image-box';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Brand Banners', 'zendotech'),
        ]);

        $this->add_control('cards', [
            'label' => __('Banner Cards', 'zendotech'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'brand',
                    'label' => __('Brand Name', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Bose',
                ],
                [
                    'name' => 'product_name',
                    'label' => __('Product Name', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'QuietComfort Ultra',
                ],
                [
                    'name' => 'description',
                    'label' => __('Description', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'default' => 'Immersive spatial audio. Silence the world.',
                ],
                [
                    'name' => 'btn_text',
                    'label' => __('Button Text', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Shop Now',
                ],
                [
                    'name' => 'btn_url',
                    'label' => __('Button URL', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::URL,
                    'default' => ['url' => '#'],
                ],
                [
                    'name' => 'image',
                    'label' => __('Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => [
                        'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=350&fit=crop',
                    ],
                ],
                [
                    'name' => 'gradient',
                    'label' => __('Predefined Gradient', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'bc-gradient-1',
                    'options' => [
                        'bc-gradient-1' => __('Gradient 1 (Purple)', 'zendotech'),
                        'bc-gradient-2' => __('Gradient 2 (Blue)', 'zendotech'),
                        'none' => __('None (Use Custom Below)', 'zendotech'),
                    ],
                ],
                [
                    'name' => 'bg_color',
                    'label' => __('Custom Background Color', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'condition' => [
                        'gradient' => 'none',
                    ],
                ],
                [
                    'name' => 'bg_image',
                    'label' => __('Custom Background Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'condition' => [
                        'gradient' => 'none',
                    ],
                ],
            ],
            'default' => [
                [
                    'brand' => 'Bose',
                    'product_name' => 'QuietComfort Ultra',
                    'description' => 'Immersive spatial audio. Silence the world.',
                    'btn_text' => 'Shop Now',
                    'btn_url' => ['url' => '#'],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=350&fit=crop'],
                    'gradient' => 'bc-gradient-1',
                ],
                [
                    'brand' => 'Marshall',
                    'product_name' => 'Stanmore III',
                    'description' => 'Iconic design meets modern wireless audio.',
                    'btn_text' => 'Shop Now',
                    'btn_url' => ['url' => '#'],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1507667522877-ad03f0c7b0e0?w=400&h=350&fit=crop'],
                    'gradient' => 'bc-gradient-2',
                ],
            ],
            'title_field' => '{{{ brand }}} — {{{ product_name }}}',
        ]);

        $this->end_controls_section();

        // ----------------- STYLE TAB ----------------- //

        // --- Card Container Style ---
        $this->start_controls_section('section_style_card', [
            'label' => __('Brand Card', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('card_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .brand-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('card_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .brand-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .brand-card',
            ]
        );

        $this->end_controls_section();

        // --- Text Content Style ---
        $this->start_controls_section('section_style_content', [
            'label' => __('Content', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        // Brand
        $this->add_control('brand_heading', [
            'label' => __('Brand Label', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('brand_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .bc-brand' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'brand_typography',
                'selector' => '{{WRAPPER}} .bc-brand',
            ]
        );

        // Product Name
        $this->add_control('product_heading', [
            'label' => __('Product Name', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('product_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .brand-card h3' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'product_typography',
                'selector' => '{{WRAPPER}} .brand-card h3',
            ]
        );

        // Description
        $this->add_control('desc_heading', [
            'label' => __('Description', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('desc_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .brand-card p' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'selector' => '{{WRAPPER}} .brand-card p',
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
                'selector' => '{{WRAPPER}} .brand-card .btn',
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
                '{{WRAPPER}} .brand-card .btn' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .brand-card .btn' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_border_color', [
            'label' => __('Border Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .brand-card .btn' => 'border-color: {{VALUE}};',
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
                '{{WRAPPER}} .brand-card .btn:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_hover_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .brand-card .btn:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_hover_border_color', [
            'label' => __('Border Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .brand-card .btn:hover' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('btn_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .brand-card .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]);

        $this->add_responsive_control('btn_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .brand-card .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Image Style ---
        $this->start_controls_section('section_style_image', [
            'label' => __('Product Image', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('image_width', [
            'label' => __('Width', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => 0, 'max' => 600],
                '%' => ['min' => 0, 'max' => 100],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-card img' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('image_max_height', [
            'label' => __('Max Height', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 0, 'max' => 800],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-card img' => 'max-height: {{SIZE}}{{UNIT}}; height: auto;',
            ],
        ]);

        $this->add_responsive_control('image_offset_x', [
            'label' => __('Offset X', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => -200, 'max' => 200],
                '%' => ['min' => -100, 'max' => 100],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-card img' => 'right: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('image_offset_y', [
            'label' => __('Offset Y', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => -200, 'max' => 200],
                '%' => ['min' => -100, 'max' => 100],
            ],
            'selectors' => [
                '{{WRAPPER}} .brand-card img' => 'bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="section pt-0">
            <div class="container">
                <div class="dual-grid">
                    <?php foreach ($s['cards'] as $card): 
                        $bg_style = '';
                        $classes = 'brand-card';
                        
                        if (!empty($card['gradient']) && $card['gradient'] !== 'none') {
                            $classes .= ' ' . esc_attr($card['gradient']);
                        } else {
                            if (!empty($card['bg_color'])) {
                                $bg_style .= 'background-color: ' . esc_attr($card['bg_color']) . '; ';
                            }
                            if (!empty($card['bg_image']['url'])) {
                                $bg_style .= 'background-image: url(' . esc_url($card['bg_image']['url']) . '); ';
                                $bg_style .= 'background-size: cover; background-position: center; border: 1px solid rgba(255,255,255,0.1); ';
                            }
                        }
                    ?>
                        <div class="<?php echo $classes; ?>" style="<?php echo $bg_style; ?>">
                            <div class="bc-info">
                                <span class="bc-brand">
                                    <?php echo esc_html($card['brand']); ?>
                                </span>
                                <h3>
                                    <?php echo esc_html($card['product_name']); ?>
                                </h3>
                                <p>
                                    <?php echo esc_html($card['description']); ?>
                                </p>
                                <a href="<?php echo esc_url($card['btn_url']['url'] ?? '#'); ?>" class="btn btn-outline-white">
                                    <?php echo esc_html($card['btn_text']); ?>
                                </a>
                            </div>
                            <?php if (!empty($card['image']['url'])): ?>
                                <img src="<?php echo esc_url($card['image']['url']); ?>"
                                    alt="<?php echo esc_attr($card['brand'] . ' ' . $card['product_name']); ?>">
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
$widgets_manager->register(new Zendotech_Brand_Banners_Widget());
