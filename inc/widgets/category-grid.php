<?php
/**
 * Zendotech Category Grid Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Category_Grid_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_category_grid';
    }
    public function get_title()
    {
        return __('Category Grid', 'zendotech');
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
            'label' => __('Category Grid', 'zendotech'),
        ]);

        $this->add_control('section_title', [
            'label' => __('Section Title', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Shop by Category',
        ]);

        $this->add_control('cat_count', [
            'label' => __('Number of Categories', 'zendotech'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 6,
            'min' => 2,
            'max' => 12,
        ]);

        $this->add_control('fallback_image', [
            'label' => __('Fallback Image', 'zendotech'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop',
            ],
            'description' => __('Used when a category has no thumbnail.', 'zendotech'),
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        ?>
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <h2>
                        <?php echo esc_html($s['section_title']); ?>
                    </h2>
                </div>
                <div class="categories-grid">
                    <?php
                    if (taxonomy_exists('product_cat')) {
                        $cats = get_terms([
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                            'number' => $s['cat_count'],
                            'exclude' => [get_option('default_product_cat')],
                        ]);
                        if (!is_wp_error($cats)) {
                            $fallback = $s['fallback_image']['url'] ?? 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=100&h=100&fit=crop';
                            foreach ($cats as $hc) {
                                $thumb_id = get_term_meta($hc->term_id, 'thumbnail_id', true);
                                $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : $fallback;
                                echo '<a href="' . esc_url(get_term_link($hc)) . '" class="cat-card">';
                                echo '<div class="cat-icon"><img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($hc->name) . '"></div>';
                                echo '<span>' . esc_html($hc->name) . '</span>';
                                echo '</a>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Category_Grid_Widget());
