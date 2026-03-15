<?php
/**
 * Zendotech Features Bar Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Features_Bar_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_features_bar';
    }
    public function get_title()
    {
        return __('Features Bar', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-info-box';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Features Bar', 'zendotech'),
        ]);

        $this->add_control('features', [
            'label' => __('Features', 'zendotech'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'icon_type',
                    'label' => __('Icon Type', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
                    'options' => [
                        'icon' => [
                            'title' => __('Icon', 'zendotech'),
                            'icon' => 'eicon-star',
                        ],
                        'svg' => [
                            'title' => __('SVG', 'zendotech'),
                            'icon' => 'eicon-svg',
                        ],
                        'image' => [
                            'title' => __('Image', 'zendotech'),
                            'icon' => 'eicon-image-bold',
                        ],
                    ],
                    'default' => 'icon',
                    'toggle' => false,
                ],
                [
                    'name' => 'icon_font',
                    'label' => __('Icon', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fas fa-shipping-fast',
                        'library' => 'fa-solid',
                    ],
                    'condition' => [
                        'icon_type' => 'icon',
                    ],
                ],
                [
                    'name' => 'icon_svg',
                    'label' => __('SVG Icon', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'media_types' => ['svg'],
                    'condition' => [
                        'icon_type' => 'svg',
                    ],
                ],
                [
                    'name' => 'icon_image',
                    'label' => __('Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'condition' => [
                        'icon_type' => 'image',
                    ],
                ],
                [
                    'name' => 'title',
                    'label' => __('Title', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Free Shipping',
                ],
                [
                    'name' => 'description',
                    'label' => __('Description', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'On orders over $75',
                ],
            ],
            'default' => [
                [
                    'icon_type' => 'icon',
                    'icon_font' => ['value' => 'fas fa-shipping-fast', 'library' => 'fa-solid'],
                    'title' => 'Free Shipping',
                    'description' => 'On orders over $75',
                ],
                [
                    'icon_type' => 'icon',
                    'icon_font' => ['value' => 'fas fa-undo', 'library' => 'fa-solid'],
                    'title' => '30-Day Returns',
                    'description' => 'Hassle-free returns',
                ],
                [
                    'icon_type' => 'icon',
                    'icon_font' => ['value' => 'fas fa-shield-alt', 'library' => 'fa-solid'],
                    'title' => '2-Year Warranty',
                    'description' => 'On all audio products',
                ],
                [
                    'icon_type' => 'icon',
                    'icon_font' => ['value' => 'fas fa-headset', 'library' => 'fa-solid'],
                    'title' => 'Expert Support',
                    'description' => 'Audio specialists 24/7',
                ],
            ],
            'title_field' => '{{{ title }}}',
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
                'selector' => '{{WRAPPER}} .features-bar .container',
            ]
        );

        $this->add_control('container_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .features-bar .container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'label' => __('Border', 'zendotech'),
                'selector' => '{{WRAPPER}} .features-bar .container',
            ]
        );

        $this->add_control('container_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .features-bar .container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Icon / Image Style ---
        $this->start_controls_section('section_style_icon', [
            'label' => __('Icon / Image', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('icon_color', [
            'label' => __('Icon/SVG Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .feat-icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .feat-icon svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_control('icon_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .feat-icon' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control('icon_size', [
            'label' => __('Icon/SVG Size', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .feat-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .feat-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('image_width', [
            'label' => __('Image Max Width', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 150,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .feat-icon img' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('icon_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .feat-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('icon_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .feat-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Text Style ---
        $this->start_controls_section('section_style_text', [
            'label' => __('Text', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        // Title
        $this->add_control('title_heading', [
            'label' => __('Title', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
        ]);

        $this->add_control('title_color', [
            'label' => __('Title Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .feat-item h4' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .feat-item h4',
            ]
        );

        // Description
        $this->add_control('desc_heading', [
            'label' => __('Description', 'zendotech'),
            'type' => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('desc_color', [
            'label' => __('Description Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .feat-item p' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'selector' => '{{WRAPPER}} .feat-item p',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="features-bar">
            <div class="container">
                <div class="features-row">
                    <?php foreach ($s['features'] as $feat): ?>
                        <div class="feat-item">
                            <div class="feat-icon">
                                <?php if ($feat['icon_type'] === 'svg' && !empty($feat['icon_svg']['url'])): ?>
                                    <img src="<?php echo esc_url($feat['icon_svg']['url']); ?>" alt="SVG Icon" class="svg-icon" style="width: 24px;">
                                <?php elseif ($feat['icon_type'] === 'image' && !empty($feat['icon_image']['url'])): ?>
                                    <img src="<?php echo esc_url($feat['icon_image']['url']); ?>" alt="Feature Image" class="image-icon" style="max-height: 40px; object-fit: contain;">
                                <?php elseif (!empty($feat['icon_font']['value'])): 
                                    // Compatibility mapping for FontAwesome 6 icons that might cause warnings in older Elementor versions
                                    $icon_val = $feat['icon_font']['value'];
                                    $mapping = [
                                        'fa-truck-fast' => 'fa-shipping-fast',
                                        'fa-rotate-left' => 'fa-undo',
                                        'fa-shield-halved' => 'fa-shield-alt',
                                        'fa-magnifying-glass' => 'fa-search',
                                        'fa-house' => 'fa-home',
                                        'fa-gear' => 'fa-cog',
                                    ];
                                    foreach ($mapping as $new => $old) {
                                        if (strpos($icon_val, $new) !== false) {
                                            $feat['icon_font']['value'] = str_replace($new, $old, $icon_val);
                                            break;
                                        }
                                    }
                                    \Elementor\Icons_Manager::render_icon($feat['icon_font'], ['aria-hidden' => 'true']); 
                                ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h4>
                                    <?php echo esc_html($feat['title']); ?>
                                </h4>
                                <p>
                                    <?php echo esc_html($feat['description']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Features_Bar_Widget());
