<?php
/**
 * Zendotech Product Grid Widget
 */

if (!defined('ABSPATH'))
    exit;

class Zendotech_Product_Grid_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'zendotech_product_grid';
    }
    public function get_title()
    {
        return __('Product Grid', 'zendotech');
    }
    public function get_icon()
    {
        return 'eicon-products';
    }
    public function get_categories()
    {
        return ['zendotech'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Product Grid', 'zendotech'),
        ]);

        $this->add_control('section_title', [
            'label' => __('Section Title', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Deals of the Day',
        ]);

        $this->add_control('product_source', [
            'label' => __('Product Source', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'on_sale',
            'options' => [
                'on_sale' => __('On Sale', 'zendotech'),
                'popular' => __('Popular', 'zendotech'),
                'latest' => __('Latest', 'zendotech'),
                'featured' => __('Featured', 'zendotech'),
                'category' => __('From Category', 'zendotech'),
            ],
        ]);

        // Dynamic category selector
        $cat_options = ['' => __('Select Category', 'zendotech')];
        if (taxonomy_exists('product_cat')) {
            $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => [get_option('default_product_cat')]]);
            if (!is_wp_error($cats)) {
                foreach ($cats as $cat) {
                    $cat_options[(string)$cat->term_id] = $cat->name . ' (' . $cat->count . ')';
                }
            }
        }

        $this->add_control('product_category', [
            'label' => __('Select Category', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => $cat_options,
            'default' => [],
            'multiple' => true,
            'label_block' => true,
            'condition' => ['product_source' => 'category'],
            'description' => __('Choose which category to display products from.', 'zendotech'),
        ]);

        $this->add_control('product_count', [
            'label' => __('Number of Products', 'zendotech'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 5,
            'min' => 1,
            'max' => 12,
        ]);

        $this->add_control('grid_columns', [
            'label' => __('Columns', 'zendotech'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 5,
            'min' => 1,
            'max' => 6,
        ]);

        $this->add_control('show_countdown', [
            'label' => __('Show Countdown', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('countdown_hours', [
            'label' => __('Hours', 'zendotech'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 4,
            'condition' => ['show_countdown' => 'yes'],
        ]);
        $this->add_control('countdown_minutes', [
            'label' => __('Minutes', 'zendotech'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 32,
            'condition' => ['show_countdown' => 'yes'],
        ]);
        $this->add_control('countdown_seconds', [
            'label' => __('Seconds', 'zendotech'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 18,
            'condition' => ['show_countdown' => 'yes'],
        ]);

        $this->add_control('show_view_all', [
            'label' => __('Show "View All" Link', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('view_all_text', [
            'label' => __('Link Text', 'zendotech'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'View All Deals',
            'condition' => ['show_view_all' => 'yes'],
        ]);

        $this->add_control('view_all_url', [
            'label' => __('Link URL', 'zendotech'),
            'type' => \Elementor\Controls_Manager::URL,
            'default' => ['url' => '#'],
            'condition' => ['show_view_all' => 'yes'],
        ]);

        $this->add_control('show_tab_filters', [
            'label' => __('Show Category Tabs', 'zendotech'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'description' => __('Show category filter tabs above the products.', 'zendotech'),
        ]);

        $this->end_controls_section();

        // ----------------- STYLE TAB ----------------- //

        // --- Container Style ---
        $this->start_controls_section('section_style_container', [
            'label' => __('Section Container', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'section_background',
                'label' => __('Background', 'zendotech'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .product-grid-wrap',
            ]
        );

        $this->add_control('section_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .product-grid-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Title Style ---
        $this->start_controls_section('section_style_title', [
            'label' => __('Section Title', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label' => __('Title Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .section-head h2' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .section-head h2',
            ]
        );

        $this->end_controls_section();

        // --- Tabs Style ---
        $this->start_controls_section('section_style_tabs', [
            'label' => __('Category Tabs', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_tab_filters' => 'yes',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tabs_typography',
                'selector' => '{{WRAPPER}} .tab-filters button',
            ]
        );

        $this->start_controls_tabs('tabs_tabs_style');

        // Normal State
        $this->start_controls_tab('tab_tabs_normal', [
            'label' => __('Normal', 'zendotech'),
        ]);

        $this->add_control('tabs_text_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tab-filters button' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tabs_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tab-filters button' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tabs_border_color', [
            'label' => __('Border Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tab-filters button' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        // Hover & Active State
        $this->start_controls_tab('tab_tabs_hover_active', [
            'label' => __('Hover/Active', 'zendotech'),
        ]);

        $this->add_control('tabs_hover_text_color', [
            'label' => __('Text Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tab-filters button:hover, {{WRAPPER}} .tab-filters button.active' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tabs_hover_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tab-filters button:hover, {{WRAPPER}} .tab-filters button.active' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tabs_hover_border_color', [
            'label' => __('Border Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .tab-filters button:hover, {{WRAPPER}} .tab-filters button.active' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('tabs_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .tab-filters button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]);

        $this->add_control('tabs_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .tab-filters button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Product Card Style ---
        $this->start_controls_section('section_style_product_card', [
            'label' => __('Product Card', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg_color', [
            'label' => __('Background Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .product-card' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_border_radius', [
            'label' => __('Border Radius', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('card_padding', [
            'label' => __('Padding', 'zendotech'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .product-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'zendotech'),
                'selector' => '{{WRAPPER}} .product-card',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_hover_box_shadow',
                'label' => __('Hover Box Shadow', 'zendotech'),
                'selector' => '{{WRAPPER}} .product-card:hover',
            ]
        );

        $this->end_controls_section();

        // --- View All Link Style ---
        $this->start_controls_section('section_style_view_all', [
            'label' => __('View All Link', 'zendotech'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_view_all' => 'yes',
            ],
        ]);

        $this->add_control('view_all_color', [
            'label' => __('Link Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .view-all' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('view_all_hover_color', [
            'label' => __('Link Hover Color', 'zendotech'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .view-all:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'view_all_typography',
                'selector' => '{{WRAPPER}} .view-all',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $s = $this->get_settings_for_display();
        $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '#';
        ?>
        <section class="products-section product-grid-wrap">
            <div class="container">
                <div class="section-head">
                    <div class="sh-left">
                        <h2>
                            <?php echo esc_html($s['section_title']); ?>
                        </h2>
                        <?php if ($s['show_countdown'] === 'yes'): ?>
                            <div class="countdown">
                                <span class="cd-label">Ends in:</span>
                                <div class="cd-box"><span>
                                        <?php echo str_pad($s['countdown_hours'], 2, '0', STR_PAD_LEFT); ?>
                                    </span><small>hrs</small></div>
                                <div class="cd-sep">:</div>
                                <div class="cd-box"><span>
                                        <?php echo str_pad($s['countdown_minutes'], 2, '0', STR_PAD_LEFT); ?>
                                    </span><small>min</small></div>
                                <div class="cd-sep">:</div>
                                <div class="cd-box"><span>
                                        <?php echo str_pad($s['countdown_seconds'], 2, '0', STR_PAD_LEFT); ?>
                                    </span><small>sec</small></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($s['show_view_all'] === 'yes'): ?>
                        <a href="<?php echo esc_url($s['view_all_url']['url'] ?? $shop_url); ?>" class="link-arrow">
                            <?php echo esc_html($s['view_all_text']); ?> <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($s['show_tab_filters'] === 'yes'): ?>
                    <div class="section-head">
                        <div class="tab-filters zendotech-grid-tabs">
                            <button class="active" data-filter="*">All</button>
                            <?php
                            if (taxonomy_exists('product_cat')) {
                                $filter_terms = [];
                                
                                // If source is category and specific categories are selected
                                if ($s['product_source'] === 'category' && !empty($s['product_category'])) {
                                    $selected_ids = array_map('intval', (array) $s['product_category']);
                                    
                                    // Try to get children of selected categories
                                    $children = [];
                                    foreach ($selected_ids as $pid) {
                                        $child_terms = get_terms([
                                            'taxonomy' => 'product_cat',
                                            'hide_empty' => true,
                                            'parent' => $pid,
                                        ]);
                                        if (!is_wp_error($child_terms) && !empty($child_terms)) {
                                            $children = array_merge($children, $child_terms);
                                        }
                                    }
                                    
                                    // Fallback to simpler query if no children found
                                    if (empty($children)) {
                                        $children = get_terms([
                                            'taxonomy' => 'product_cat',
                                            'hide_empty' => true,
                                            'include' => $selected_ids,
                                        ]);
                                    }
                                    
                                    if (!is_wp_error($children) && !empty($children)) {
                                        $filter_terms = $children;
                                    }
                                } else {
                                    // Default fallback to 4 top categories
                                    $filter_terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 4, 'exclude' => [get_option('default_product_cat')]]);
                                }
                                
                                if (!empty($filter_terms) && !is_wp_error($filter_terms)) {
                                    // Limit to a reasonable number of tabs
                                    $filter_terms = array_slice($filter_terms, 0, 6);
                                    foreach ($filter_terms as $ft) {
                                        echo '<button data-filter=".product_cat-' . esc_attr($ft->slug) . '">' . esc_html($ft->name) . '</button>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php 
                $columns = !empty($s['grid_columns']) ? intval($s['grid_columns']) : 5; 
                ?>
                <div class="products-row" style="grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr);">
                    <?php
                    if (!function_exists('wc_get_products'))
                        return;

                    $args = ['limit' => $s['product_count'], 'status' => 'publish'];

                    switch ($s['product_source']) {
                        case 'on_sale':
                            $sale_ids = wc_get_product_ids_on_sale();
                            if (!empty($sale_ids)) {
                                $q = new WP_Query(['post_type' => 'product', 'posts_per_page' => $s['product_count'], 'post__in' => $sale_ids, 'orderby' => 'rand']);
                                while ($q->have_posts()) {
                                    $q->the_post();
                                    zendotech_product_card(wc_get_product(get_the_ID()));
                                }
                                wp_reset_postdata();
                            } else {
                                $prods = wc_get_products(['limit' => $s['product_count'], 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish']);
                                foreach ($prods as $p)
                                    zendotech_product_card($p);
                            }
                            break;
                        case 'popular':
                            $prods = wc_get_products(['limit' => $s['product_count'], 'orderby' => 'popularity', 'order' => 'DESC', 'status' => 'publish']);
                            if (empty($prods))
                                $prods = wc_get_products(['limit' => $s['product_count'], 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish']);
                            foreach ($prods as $p)
                                zendotech_product_card($p);
                            break;
                        case 'featured':
                            $prods = wc_get_products(['limit' => $s['product_count'], 'featured' => true, 'status' => 'publish']);
                            if (empty($prods))
                                $prods = wc_get_products(['limit' => $s['product_count'], 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish']);
                            foreach ($prods as $p)
                                zendotech_product_card($p);
                            break;
                        case 'category':
                            $cat_ids = !empty($s['product_category']) ? array_map('intval', (array) $s['product_category']) : [];
                            if (!empty($cat_ids)) {
                                $q = new WP_Query([
                                    'post_type' => 'product',
                                    'posts_per_page' => $s['product_count'],
                                    'post_status' => 'publish',
                                    'tax_query' => [[
                                        'taxonomy' => 'product_cat',
                                        'field' => 'term_id',
                                        'terms' => $cat_ids,
                                        'operator' => 'IN'
                                    ]],
                                ]);
                                while ($q->have_posts()) {
                                    $q->the_post();
                                    $prod_classes = implode(' ', wc_get_product_class('', get_the_ID()));
                                    // We need to inject the classes into the card if zendotech_product_card doesn't wrap it properly...
                                    // But assuming the standard template handles post classes, it should have .product_cat-{slug}
                                    echo '<div class="z-grid-item ' . esc_attr($prod_classes) . '">';
                                    zendotech_product_card(wc_get_product(get_the_ID()));
                                    echo '</div>';
                                }
                                wp_reset_postdata();
                            } else {
                                $prods = wc_get_products(['limit' => $s['product_count'], 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish']);
                                foreach ($prods as $p) {
                                    $prod_classes = implode(' ', wc_get_product_class('', $p->get_id()));
                                    echo '<div class="z-grid-item ' . esc_attr($prod_classes) . '">';
                                    zendotech_product_card($p);
                                    echo '</div>';
                                }
                            }
                            break;
                        default: // latest
                            $prods = wc_get_products(['limit' => $s['product_count'], 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish']);
                            foreach ($prods as $p)
                                zendotech_product_card($p);
                    }
                    ?>
                </div>
            </div>
        </section>

        <?php if ($s['show_tab_filters'] === 'yes'): ?>
        <script>
            (function($){
                $(document).ready(function(){
                    var grid = $('.products-row');
                    $('.zendotech-grid-tabs button').on('click', function(e){
                        e.preventDefault();
                        $('.zendotech-grid-tabs button').removeClass('active');
                        $(this).addClass('active');

                        var filterValue = $(this).attr('data-filter');
                        
                        if(filterValue === '*') {
                            grid.find('.z-grid-item').show(300);
                        } else {
                            grid.find('.z-grid-item').hide();
                            grid.find('.z-grid-item' + filterValue).show(300);
                        }
                    });
                });
            })(jQuery);
        </script>
        <?php endif; ?>
        <?php
    }
}

$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
$widgets_manager->register(new Zendotech_Product_Grid_Widget());
