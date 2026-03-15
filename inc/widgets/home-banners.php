<?php
/**
 * Zendotech Home Banners Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Home_Banners_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_home_banners';
    }

    public function get_title()
    {
        return __('Home Banners', 'zendotech');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Content', 'zendotech'),
        ]);

        $this->add_control('banners', [
            'label' => __('Banners', 'zendotech'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'subtitle',
                    'label' => __('Subtitle', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'UP TO 30% OFF',
                ],
                [
                    'name' => 'title',
                    'label' => __('Title', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'default' => 'Table &<br> SMARTPHONE',
                ],
                [
                    'name' => 'image',
                    'label' => __('Banner Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                ],
                [
                    'name' => 'link',
                    'label' => __('Link', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::URL,
                    'default' => [
                        'url' => '#',
                    ],
                ],
                [
                    'name' => 'button_text',
                    'label' => __('Button Text', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Shop now',
                ],
                [
                    'name' => 'bg_color',
                    'label' => __('Background Color', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                ],
                [
                    'name' => 'bg_image',
                    'label' => __('Background Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                ],
            ],
            'default' => [
                [
                    'subtitle' => 'UP TO 30% OFF',
                    'title' => 'Table &<br> SMARTPHONE',
                    'bg_color' => '#f7f7f7',
                ],
                [
                    'subtitle' => 'UP TO 30% OFF',
                    'title' => 'Camera & <br>Video',
                    'bg_color' => '#f1f1f1',
                ],
                [
                    'subtitle' => 'UP TO 30% OFF',
                    'title' => 'TELEVISION<br> & HOME THEATER',
                    'bg_color' => '#ebebeb',
                ],
            ],
            'title_field' => '{{{ title }}}',
        ]);

        $this->end_controls_section();

        // --- STYLE TAB ---

        // Card Styling
        $this->start_controls_section('section_style_card', [
            'label' => __('Card', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('card_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .bwp-widget-banner.layout-1 .bg-banner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('card_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .bwp-widget-banner.layout-1' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .bwp-widget-banner.layout-1' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'card_box_shadow',
            'selector' => '{{WRAPPER}} .bwp-widget-banner.layout-1',
        ]);

        $this->end_controls_section();

        // Subtitle Styling
        $this->start_controls_section('section_style_subtitle', [
            'label' => __('Subtitle', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('subtitle_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .bwp-image-subtitle' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'subtitle_typography',
            'selector' => '{{WRAPPER}} .bwp-image-subtitle',
        ]);

        $this->add_responsive_control('subtitle_spacing', [
            'label' => __('Spacing', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'selectors' => [
                '{{WRAPPER}} .bwp-image-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // Title Styling
        $this->start_controls_section('section_style_title', [
            'label' => __('Title', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label' => __('Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .title-banner' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .title-banner',
        ]);

        $this->add_responsive_control('title_spacing', [
            'label' => __('Spacing', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'selectors' => [
                '{{WRAPPER}} .title-banner' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // Button Styling
        $this->start_controls_section('section_style_button', [
            'label' => __('Button', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('button_text_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .button' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_icon_bg', [
            'label' => __('Icon Circle Background', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .button::after' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_icon_color', [
            'label' => __('Icon Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .button::after' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'button_typography',
            'selector' => '{{WRAPPER}} .button',
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['banners'])) {
            return;
        }
        ?>
        <div class="zendotech-home-banners-wrap section pt-0">
            <div class="container">
                <div class="elementor-container elementor-column-gap-default">
                <?php foreach ($settings['banners'] as $item) : 
                    $target = !empty($item['link']['is_external']) ? ' target="_blank"' : '';
                    $nofollow = !empty($item['link']['nofollow']) ? ' rel="nofollow"' : '';
                    $url = isset($item['link']['url']) ? $item['link']['url'] : '#';
                    
                    $card_style = '';
                    if (!empty($item['bg_color'])) {
                        $card_style .= 'background-color: ' . esc_attr($item['bg_color']) . ' !important;';
                    }
                    if (!empty($item['bg_image']['url'])) {
                        $card_style .= 'background-image: url(\'' . esc_url($item['bg_image']['url']) . '\') !important; background-size: cover !important; background-position: center !important;';
                    }
                ?>
                    <div class="elementor-column elementor-col-33 elementor-top-column elementor-element">
                        <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-widget elementor-widget-bwp_image">
                                <div class="elementor-widget-container">
                                    <div class="bwp-widget-banner layout-1" style="<?php echo $card_style; ?>">
                                        <div class="bg-banner">		
                                            <div class="banner-wrapper banners">
                                                <div class="bwp-image">
                                                    <a href="<?php echo esc_url($url); ?>"<?php echo $target . $nofollow; ?>>
                                                        <?php if (!empty($item['image']['url'])) : ?>
                                                            <img decoding="async" src="<?php echo esc_url($item['image']['url']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <div class="banner-wrapper-infor">
                                                    <div class="info">
                                                        <div class="content">
                                                            <?php if (!empty($item['subtitle'])) : ?>
                                                                <div class="bwp-image-subtitle">
                                                                    <?php echo esc_html($item['subtitle']); ?>
                                                                </div>	
                                                            <?php endif; ?>
                                                            <?php if (!empty($item['title'])) : ?>
                                                                <h3 class="title-banner"><?php echo wp_kses_post($item['title']); ?></h3>
                                                            <?php endif; ?>
                                                            <a class="button" href="<?php echo esc_url($url); ?>"<?php echo $target . $nofollow; ?>>
                                                                <?php echo esc_html($item['button_text']); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Home_Banners_Widget());
