<?php
/**
 * Zendotech Tri Banners Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Tri_Banners_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_tri_banners';
    }
    public function get_title()
    {
        return __('Tri Banners', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-posts-grid';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Tri Banners', 'zendotech'),
        ]);

        $this->add_control('cards', [
            'label' => __('Banner Cards', 'zendotech'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'tag',
                    'label' => __('Tag', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Best Sellers',
                ],
                [
                    'name' => 'title',
                    'label' => __('Title', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => 'Wireless Earbuds',
                ],
                [
                    'name' => 'link_url',
                    'label' => __('Link URL', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::URL,
                    'default' => ['url' => '#'],
                ],
                [
                    'name' => 'image',
                    'label' => __('Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => [
                        'url' => 'https://images.unsplash.com/photo-1590658268037-6bf12f032f55?w=200&h=200&fit=crop',
                    ],
                ],
                [
                    'name' => 'color',
                    'label' => __('Color Theme', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'tri-pink',
                    'options' => [
                        'tri-pink' => __('Pink', 'zendotech'),
                        'tri-gold' => __('Gold', 'zendotech'),
                        'tri-cyan' => __('Cyan', 'zendotech'),
                        'none' => __('None (Use Custom Below)', 'zendotech'),
                    ],
                ],
                [
                    'name' => 'bg_color',
                    'label' => __('Custom Background Color', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'condition' => [
                        'color' => 'none',
                    ],
                ],
                [
                    'name' => 'bg_image',
                    'label' => __('Custom Background Image', 'zendotech'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'condition' => [
                        'color' => 'none',
                    ],
                ],
            ],
            'default' => [
                [
                    'tag' => 'Best Sellers',
                    'title' => 'Wireless Earbuds',
                    'link_url' => ['url' => '#'],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1590658268037-6bf12f032f55?w=200&h=200&fit=crop'],
                    'color' => 'tri-pink',
                ],
                [
                    'tag' => 'Top Rated',
                    'title' => 'Vinyl & Records',
                    'link_url' => ['url' => '#'],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=200&h=200&fit=crop'],
                    'color' => 'tri-gold',
                ],
                [
                    'tag' => 'New Arrivals',
                    'title' => 'DJ Equipment',
                    'link_url' => ['url' => '#'],
                    'image' => ['url' => 'https://images.unsplash.com/photo-1571330735066-03aaa9429d89?w=200&h=200&fit=crop'],
                    'color' => 'tri-cyan',
                ],
            ],
            'title_field' => '{{{ tag }}} — {{{ title }}}',
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="section pt-0">
            <div class="container">
                <div class="tri-grid">
                    <?php foreach ($s['cards'] as $card): 
                        $bg_style = '';
                        $classes = 'tri-card';
                        
                        if (!empty($card['color']) && $card['color'] !== 'none') {
                            $classes .= ' ' . esc_attr($card['color']);
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
                            <div class="tri-info">
                                <span>
                                    <?php echo esc_html($card['tag']); ?>
                                </span>
                                <h3>
                                    <?php echo esc_html($card['title']); ?>
                                </h3>
                                <a href="<?php echo esc_url($card['link_url']['url'] ?? '#'); ?>">Shop Now <i
                                        class="fa-solid fa-arrow-right"></i></a>
                            </div>
                            <?php if (!empty($card['image']['url'])): ?>
                                <img src="<?php echo esc_url($card['image']['url']); ?>" alt="<?php echo esc_attr($card['title']); ?>">
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
$widgets_manager->register(new Zendotech_Tri_Banners_Widget());
