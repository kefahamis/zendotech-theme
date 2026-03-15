<?php
/**
 * Zendotech Footer Builder
 * 
 * Custom admin page under Appearance > Footer Builder
 * Allows editing all footer sections dynamically without touching code.
 */

if (!defined('ABSPATH'))
    exit;

/* ============================================
   1. REGISTER ADMIN MENU
   ============================================ */
function zendotech_footer_builder_menu()
{
    add_theme_page(
        'Footer Builder',
        'Footer Builder',
        'manage_options',
        'zendotech-footer-builder',
        'zendotech_footer_builder_page'
    );
}
add_action('admin_menu', 'zendotech_footer_builder_menu');

/**
 * Handle save before page load to allow redirects
 */
function zendotech_footer_builder_init()
{
    // Check if this is our save request
    if (isset($_POST['zendotech_footer_nonce']) && wp_verify_nonce($_POST['zendotech_footer_nonce'], 'zendotech_save_footer')) {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        // Handle reset to defaults
        if (!empty($_POST['reset_defaults']) && $_POST['reset_defaults'] === '1') {
            update_option('zendotech_footer_options', zendotech_footer_defaults());
            $active_tab = sanitize_text_field($_POST['active_tab'] ?? 'newsletter');
            wp_redirect(admin_url('themes.php?page=zendotech-footer-builder&saved=1&tab=' . $active_tab));
            exit;
        }

        $data = array();

        // 1. Newsletter
        $data['newsletter_enabled'] = isset($_POST['newsletter_enabled']) ? '1' : '0';
        $data['newsletter_icon'] = sanitize_text_field($_POST['newsletter_icon'] ?? '');
        $data['newsletter_heading'] = sanitize_text_field($_POST['newsletter_heading'] ?? '');
        $data['newsletter_text'] = wp_kses_post($_POST['newsletter_text'] ?? '');
        $data['newsletter_placeholder'] = sanitize_text_field($_POST['newsletter_placeholder'] ?? '');
        $data['newsletter_btn_text'] = sanitize_text_field($_POST['newsletter_btn_text'] ?? '');

        // 2. Brand
        $data['brand_description'] = wp_kses_post($_POST['brand_description'] ?? '');
        $data['contact_label'] = sanitize_text_field($_POST['contact_label'] ?? '');
        $data['contact_phone'] = sanitize_text_field($_POST['contact_phone'] ?? '');
        $data['contact_address'] = sanitize_text_field($_POST['contact_address'] ?? '');

        // 3. Columns
        $data['col2_title'] = sanitize_text_field($_POST['col2_title'] ?? '');
        $data['col2_source'] = sanitize_text_field($_POST['col2_source'] ?? 'auto');
        $data['col2_menu'] = sanitize_text_field($_POST['col2_menu'] ?? '');
        $data['col2_links'] = zendotech_sanitize_links($_POST['col2_links'] ?? array());

        $data['col3_title'] = sanitize_text_field($_POST['col3_title'] ?? '');
        $data['col3_source'] = sanitize_text_field($_POST['col3_source'] ?? 'menu');
        $data['col3_menu'] = sanitize_text_field($_POST['col3_menu'] ?? '');
        $data['col3_links'] = zendotech_sanitize_links($_POST['col3_links'] ?? array());

        // 4. Social
        $data['social_title'] = sanitize_text_field($_POST['social_title'] ?? '');
        $social_links = array();
        if (!empty($_POST['social_icon']) && is_array($_POST['social_icon'])) {
            foreach ($_POST['social_icon'] as $i => $icon) {
                $url = $_POST['social_url'][$i] ?? '#';
                if (!empty(trim($icon))) {
                    $social_links[] = array(
                        'icon' => sanitize_text_field($icon),
                        'url' => esc_url_raw($url),
                    );
                }
            }
        }
        $data['social_links'] = $social_links;

        // 5. Payment
        $data['payment_title'] = sanitize_text_field($_POST['payment_title'] ?? '');
        $payment_icons = array();
        if (!empty($_POST['payment_icons']) && is_array($_POST['payment_icons'])) {
            foreach ($_POST['payment_icons'] as $icon) {
                if (!empty(trim($icon))) {
                    $payment_icons[] = sanitize_text_field($icon);
                }
            }
        }
        $data['payment_icons'] = $payment_icons;

        // 6. Bottom
        $data['copyright_text'] = wp_kses_post($_POST['copyright_text'] ?? '');
        $data['bottom_links'] = zendotech_sanitize_links($_POST['bottom_links'] ?? array());

        // Final Save
        update_option('zendotech_footer_options', $data);
        wp_cache_delete('zendotech_footer_options', 'options');

        $active_tab = sanitize_text_field($_POST['active_tab'] ?? 'newsletter');
        nocache_headers();
        wp_redirect(admin_url('themes.php?page=zendotech-footer-builder&saved=1&tab=' . $active_tab));
        exit;
    }
}
add_action('admin_init', 'zendotech_footer_builder_init');

/* ============================================
   2. ENQUEUE ADMIN ASSETS
   ============================================ */
function zendotech_footer_builder_assets($hook)
{
    if ($hook !== 'appearance_page_zendotech-footer-builder')
        return;

    wp_enqueue_media(); // For media uploader
    wp_enqueue_style(
        'zendotech-footer-builder-css',
        get_template_directory_uri() . '/assets/css/admin-footer-builder.css',
        array(),
        '1.0.0'
    );
    wp_enqueue_script(
        'zendotech-footer-builder-js',
        get_template_directory_uri() . '/assets/js/admin-footer-builder.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'zendotech_footer_builder_assets');

/* ============================================
   3. AUTO-SEED DEFAULTS ON FIRST LOAD
   ============================================ */
function zendotech_footer_seed_defaults()
{
    $existing = get_option('zendotech_footer_options', false);
    if ($existing === false) {
        // First time: save all defaults to the database
        update_option('zendotech_footer_options', zendotech_footer_defaults());
    }
}
add_action('after_setup_theme', 'zendotech_footer_seed_defaults');

/* ============================================
   4. DEFAULT FOOTER OPTIONS
   ============================================ */
function zendotech_footer_defaults()
{
    return array(
        // Newsletter
        'newsletter_enabled' => '1',
        'newsletter_icon' => 'fa-solid fa-music',
        'newsletter_heading' => 'Join the {site_name} Community',
        'newsletter_text' => 'Get <strong>15% off</strong> your first order + exclusive drops',
        'newsletter_placeholder' => 'Enter your email address',
        'newsletter_btn_text' => 'Subscribe',

        // Brand Column
        'brand_description' => 'Your premium destination for high-fidelity audio gear. We bring the studio to your home with world-class headphones, speakers, and instruments.',
        'contact_label' => 'Need help? Talk to an expert',
        'contact_phone' => '(+1) 800-967-4488',
        'contact_address' => '42 Music Row, Nashville, TN 37203, USA',

        // Column 2 — Links
        'col2_title' => 'Shop by Category',
        'col2_source' => 'auto', // 'auto' = WooCommerce categories, 'menu' = WP menu, 'custom' = custom links
        'col2_menu' => '',
        'col2_links' => array(),

        // Column 3 — Links
        'col3_title' => 'Customer Care',
        'col3_source' => 'custom', // custom links pre-populated
        'col3_menu' => '',
        'col3_links' => array(
            array('text' => 'My Account', 'url' => '#'),
            array('text' => 'Order Tracking', 'url' => '#'),
            array('text' => 'Wish List', 'url' => '#'),
            array('text' => 'Returns & Exchanges', 'url' => '#'),
            array('text' => 'Warranty Info', 'url' => '#'),
            array('text' => 'FAQs', 'url' => '#'),
        ),

        // Column 4 — Social & Payment
        'social_title' => 'Follow Us',
        'social_links' => array(
            array('icon' => 'fa-brands fa-spotify', 'url' => '#'),
            array('icon' => 'fa-brands fa-instagram', 'url' => '#'),
            array('icon' => 'fa-brands fa-youtube', 'url' => '#'),
            array('icon' => 'fa-brands fa-tiktok', 'url' => '#'),
        ),
        'payment_title' => 'We Accept',
        'payment_icons' => array(
            'fa-brands fa-cc-visa',
            'fa-brands fa-cc-mastercard',
            'fa-brands fa-cc-paypal',
            'fa-brands fa-cc-apple-pay',
            'fa-solid fa-mobile-screen-button', // Mpesa / Mobile Payment
        ),

        // Bottom Bar
        'copyright_text' => '&copy; {year} {site_name}. All Rights Reserved.',
        'bottom_links' => array(
            array('text' => 'Privacy Policy', 'url' => '#'),
            array('text' => 'Terms of Service', 'url' => '#'),
            array('text' => 'Cookie Settings', 'url' => '#'),
        ),
    );
}

/* ============================================
   4. GET FOOTER OPTION (with defaults)
   ============================================ */
function zendotech_get_footer_option($key)
{
    $saved = get_option('zendotech_footer_options', array());
    $defaults = zendotech_footer_defaults();

    if (isset($saved[$key])) {
        return $saved[$key];
    }

    return isset($defaults[$key]) ? $defaults[$key] : '';
}

/**
 * Process template tags in footer text
 */
function zendotech_footer_parse($text)
{
    $text = str_replace('{site_name}', get_bloginfo('name'), $text);
    $text = str_replace('{year}', date('Y'), $text);
    return $text;
}

/* ============================================
   6. SANITIZE LINKS HELPER
   ============================================ */

function zendotech_sanitize_links($links_data)
{
    $clean = array();
    if (!empty($links_data['text']) && is_array($links_data['text'])) {
        foreach ($links_data['text'] as $i => $text) {
            $url = $links_data['url'][$i] ?? '#';
            if (!empty(trim($text))) {
                $clean[] = array(
                    'text' => sanitize_text_field($text),
                    'url' => esc_url_raw($url),
                );
            }
        }
    }
    return $clean;
}

/* ============================================
   6. ADMIN PAGE RENDER
   ============================================ */
function zendotech_footer_builder_page()
{
    $saved_msg = '';
    $status = isset($_GET['saved']) ? $_GET['saved'] : '';
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'newsletter';

    if ($status === '1') {
        $saved_msg = 'Footer settings saved successfully!';
    } elseif ($status === '0') {
        $saved_msg = 'Error: Failed to save settings (Invalid nonce or permission).';
    }

    // Get all menus for dropdown
    $menus = wp_get_nav_menus();
    ?>
    <div class="wrap zfb-wrap">
        <div class="zfb-header">
            <div class="zfb-header-left">
                <h1><i class="dashicons dashicons-layout"></i> Footer Builder</h1>
                <p>Customize every section of your site footer without touching code.</p>
            </div>
            <div class="zfb-header-right">
                <span class="zfb-theme-badge">Zendotech Audio Theme</span>
            </div>
        </div>

        <?php if ($saved_msg): ?>
            <div class="notice <?php echo $status === '1' ? 'notice-success' : 'notice-error'; ?> is-dismissible zfb-notice">
                <p><strong><?php echo esc_html($saved_msg); ?></strong></p>
            </div>
        <?php endif; ?>

        <form method="post" class="zfb-form"
            action="<?php echo esc_url(admin_url('themes.php?page=zendotech-footer-builder')); ?>" novalidate>
            <?php wp_nonce_field('zendotech_save_footer', 'zendotech_footer_nonce'); ?>
            <input type="hidden" name="active_tab" id="zfb_active_tab" value="<?php echo esc_attr($active_tab); ?>" />

            <!-- Tab Navigation -->
            <div class="zfb-tabs">
                <button type="button" class="zfb-tab active" data-tab="newsletter"><i
                        class="dashicons dashicons-email-alt"></i> Newsletter</button>
                <button type="button" class="zfb-tab" data-tab="brand"><i class="dashicons dashicons-store"></i> Brand &
                    Contact</button>
                <button type="button" class="zfb-tab" data-tab="columns"><i class="dashicons dashicons-columns"></i> Link
                    Columns</button>
                <button type="button" class="zfb-tab" data-tab="social"><i class="dashicons dashicons-share"></i> Social &
                    Payment</button>
                <button type="button" class="zfb-tab" data-tab="bottom"><i class="dashicons dashicons-admin-generic"></i>
                    Bottom Bar</button>
            </div>

            <!-- ======= NEWSLETTER TAB ======= -->
            <div class="zfb-panel active" id="tab-newsletter">
                <div class="zfb-panel-header">
                    <h2>Newsletter Section</h2>
                    <p>Configure the email subscription banner shown on the homepage.</p>
                </div>

                <div class="zfb-field">
                    <label class="zfb-toggle-label">
                        <input type="checkbox" name="newsletter_enabled" value="1" <?php checked(zendotech_get_footer_option('newsletter_enabled'), '1'); ?> />
                        <span class="zfb-toggle-switch"></span>
                        <span>Enable Newsletter Bar</span>
                    </label>
                    <p class="zfb-help">Show newsletter subscription on the homepage footer.</p>
                </div>

                <div class="zfb-field-row">
                    <div class="zfb-field">
                        <label>Icon Class <span class="zfb-hint">(Font Awesome)</span></label>
                        <input type="text" name="newsletter_icon"
                            value="<?php echo esc_attr(zendotech_get_footer_option('newsletter_icon')); ?>"
                            placeholder="fa-solid fa-music" />
                    </div>
                    <div class="zfb-field">
                        <label>Button Text</label>
                        <input type="text" name="newsletter_btn_text"
                            value="<?php echo esc_attr(zendotech_get_footer_option('newsletter_btn_text')); ?>"
                            placeholder="Subscribe" />
                    </div>
                </div>

                <div class="zfb-field">
                    <label>Heading <span class="zfb-hint">Use {site_name} for dynamic site name</span></label>
                    <input type="text" name="newsletter_heading"
                        value="<?php echo esc_attr(zendotech_get_footer_option('newsletter_heading')); ?>"
                        placeholder="Join the {site_name} Community" />
                </div>

                <div class="zfb-field">
                    <label>Subtitle Text <span class="zfb-hint">HTML allowed</span></label>
                    <input type="text" name="newsletter_text"
                        value="<?php echo esc_attr(zendotech_get_footer_option('newsletter_text')); ?>"
                        placeholder="Get 15% off your first order" />
                </div>

                <div class="zfb-field">
                    <label>Input Placeholder</label>
                    <input type="text" name="newsletter_placeholder"
                        value="<?php echo esc_attr(zendotech_get_footer_option('newsletter_placeholder')); ?>"
                        placeholder="Enter your email address" />
                </div>
            </div>

            <!-- ======= BRAND TAB ======= -->
            <div class="zfb-panel" id="tab-brand">
                <div class="zfb-panel-header">
                    <h2>Brand & Contact Info</h2>
                    <p>Edit the brand description and contact details shown in the first footer column.</p>
                </div>

                <div class="zfb-field">
                    <label>Brand Description</label>
                    <textarea name="brand_description"
                        rows="4"><?php echo esc_textarea(zendotech_get_footer_option('brand_description')); ?></textarea>
                    <p class="zfb-help">Displayed below the footer logo.</p>
                </div>

                <div class="zfb-field">
                    <label>Support Label</label>
                    <input type="text" name="contact_label"
                        value="<?php echo esc_attr(zendotech_get_footer_option('contact_label')); ?>"
                        placeholder="Need help? Talk to an expert" />
                </div>

                <div class="zfb-field-row">
                    <div class="zfb-field">
                        <label>Phone Number</label>
                        <input type="text" name="contact_phone"
                            value="<?php echo esc_attr(zendotech_get_footer_option('contact_phone')); ?>"
                            placeholder="(+1) 800-967-4488" />
                    </div>
                    <div class="zfb-field">
                        <label>Address</label>
                        <input type="text" name="contact_address"
                            value="<?php echo esc_attr(zendotech_get_footer_option('contact_address')); ?>"
                            placeholder="42 Music Row, Nashville, TN" />
                    </div>
                </div>
            </div>

            <!-- ======= COLUMNS TAB ======= -->
            <div class="zfb-panel" id="tab-columns">
                <div class="zfb-panel-header">
                    <h2>Link Columns</h2>
                    <p>Configure the two middle footer columns. Each can pull from WooCommerce categories, a WordPress menu,
                        or custom links.</p>
                </div>

                <?php for ($col = 2; $col <= 3; $col++): ?>
                    <div class="zfb-section-card">
                        <h3>Column <?php echo $col - 1; ?> —
                            <?php echo esc_html(zendotech_get_footer_option("col{$col}_title")); ?>
                        </h3>

                        <div class="zfb-field">
                            <label>Column Title</label>
                            <input type="text" name="col<?php echo $col; ?>_title"
                                value="<?php echo esc_attr(zendotech_get_footer_option("col{$col}_title")); ?>" />
                        </div>

                        <div class="zfb-field">
                            <label>Link Source</label>
                            <select name="col<?php echo $col; ?>_source" class="zfb-source-select"
                                data-col="<?php echo $col; ?>">
                                <option value="auto" <?php selected(zendotech_get_footer_option("col{$col}_source"), 'auto'); ?>>Auto — WooCommerce Categories</option>
                                <option value="menu" <?php selected(zendotech_get_footer_option("col{$col}_source"), 'menu'); ?>>WordPress Menu</option>
                                <option value="custom" <?php selected(zendotech_get_footer_option("col{$col}_source"), 'custom'); ?>>Custom Links</option>
                            </select>
                        </div>

                        <!-- Menu Selector -->
                        <div class="zfb-field zfb-source-field zfb-source-menu-<?php echo $col; ?>"
                            style="<?php echo zendotech_get_footer_option("col{$col}_source") === 'menu' ? '' : 'display:none'; ?>">
                            <label>Select Menu</label>
                            <select name="col<?php echo $col; ?>_menu">
                                <option value="">— Choose a menu —</option>
                                <?php foreach ($menus as $menu): ?>
                                    <option value="<?php echo esc_attr($menu->slug); ?>" <?php selected(zendotech_get_footer_option("col{$col}_menu"), $menu->slug); ?>>
                                        <?php echo esc_html($menu->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Custom Links Repeater -->
                        <div class="zfb-field zfb-source-field zfb-source-custom-<?php echo $col; ?>"
                            style="<?php echo zendotech_get_footer_option("col{$col}_source") === 'custom' ? '' : 'display:none'; ?>">
                            <label>Custom Links</label>
                            <div class="zfb-repeater" data-name="col<?php echo $col; ?>_links">
                                <?php
                                $links = zendotech_get_footer_option("col{$col}_links");
                                if (!empty($links)):
                                    foreach ($links as $link):
                                        ?>
                                        <div class="zfb-repeater-row">
                                            <input type="text" name="col<?php echo $col; ?>_links[text][]"
                                                value="<?php echo esc_attr($link['text']); ?>" placeholder="Link Text" />
                                            <input type="text" name="col<?php echo $col; ?>_links[url][]"
                                                value="<?php echo esc_url($link['url']); ?>" placeholder="URL" />
                                            <button type="button" class="zfb-remove-row" title="Remove"><i
                                                    class="dashicons dashicons-no-alt"></i></button>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                            <button type="button" class="button zfb-add-link" data-col="<?php echo $col; ?>"><i
                                    class="dashicons dashicons-plus-alt2"></i> Add Link</button>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- ======= SOCIAL TAB ======= -->
            <div class="zfb-panel" id="tab-social">
                <div class="zfb-panel-header">
                    <h2>Social Media & Payment</h2>
                    <p>Manage social media links and accepted payment method icons.</p>
                </div>

                <div class="zfb-section-card">
                    <h3><i class="dashicons dashicons-share"></i> Social Links</h3>

                    <div class="zfb-field">
                        <label>Section Title</label>
                        <input type="text" name="social_title"
                            value="<?php echo esc_attr(zendotech_get_footer_option('social_title')); ?>"
                            placeholder="Follow Us" />
                    </div>

                    <div class="zfb-repeater" id="social-repeater">
                        <?php
                        $social = zendotech_get_footer_option('social_links');
                        if (!empty($social)):
                            foreach ($social as $s):
                                ?>
                                <div class="zfb-repeater-row">
                                    <input type="text" name="social_icon[]" value="<?php echo esc_attr($s['icon']); ?>"
                                        placeholder="fa-brands fa-instagram" />
                                    <input type="text" name="social_url[]" value="<?php echo esc_url($s['url']); ?>"
                                        placeholder="https://instagram.com/..." />
                                    <span class="zfb-icon-preview"><i class="<?php echo esc_attr($s['icon']); ?>"></i></span>
                                    <button type="button" class="zfb-remove-row" title="Remove"><i
                                            class="dashicons dashicons-no-alt"></i></button>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <button type="button" class="button zfb-add-social"><i class="dashicons dashicons-plus-alt2"></i> Add
                        Social Link</button>

                    <div class="zfb-help-box">
                        <strong>Common icon classes:</strong>
                        <code>fa-brands fa-facebook-f</code>
                        <code>fa-brands fa-instagram</code>
                        <code>fa-brands fa-twitter</code>
                        <code>fa-brands fa-youtube</code>
                        <code>fa-brands fa-tiktok</code>
                        <code>fa-brands fa-spotify</code>
                        <code>fa-brands fa-linkedin-in</code>
                        <code>fa-brands fa-pinterest</code>
                    </div>
                </div>

                <div class="zfb-section-card">
                    <h3><i class="dashicons dashicons-money-alt"></i> Payment Methods</h3>

                    <div class="zfb-field">
                        <label>Section Title</label>
                        <input type="text" name="payment_title"
                            value="<?php echo esc_attr(zendotech_get_footer_option('payment_title')); ?>"
                            placeholder="We Accept" />
                    </div>

                    <div class="zfb-repeater" id="payment-repeater">
                        <?php
                        $payments = zendotech_get_footer_option('payment_icons');
                        if (!empty($payments)):
                            foreach ($payments as $icon):
                                ?>
                                <div class="zfb-repeater-row">
                                    <input type="text" name="payment_icons[]" value="<?php echo esc_attr($icon); ?>"
                                        placeholder="fa-brands fa-cc-visa" />
                                    <span class="zfb-icon-preview"><i class="<?php echo esc_attr($icon); ?>"></i></span>
                                    <button type="button" class="zfb-remove-row" title="Remove"><i
                                            class="dashicons dashicons-no-alt"></i></button>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <button type="button" class="button zfb-add-payment"><i class="dashicons dashicons-plus-alt2"></i> Add
                        Payment Icon</button>

                    <div class="zfb-help-box">
                        <strong>Common payment icons:</strong>
                        <code>fa-brands fa-cc-visa</code>
                        <code>fa-brands fa-cc-mastercard</code>
                        <code>fa-brands fa-cc-paypal</code>
                        <code>fa-brands fa-cc-apple-pay</code>
                        <code>fa-brands fa-cc-amex</code>
                        <code>fa-brands fa-cc-stripe</code>
                        <code>fa-solid fa-mobile-screen-button</code> (Mpesa)
                    </div>
                </div>
            </div>

            <!-- ======= BOTTOM BAR TAB ======= -->
            <div class="zfb-panel" id="tab-bottom">
                <div class="zfb-panel-header">
                    <h2>Bottom Bar</h2>
                    <p>Edit copyright text and bottom navigation links.</p>
                </div>

                <div class="zfb-field">
                    <label>Copyright Text <span class="zfb-hint">Use {year} and {site_name} as dynamic
                            placeholders</span></label>
                    <input type="text" name="copyright_text"
                        value="<?php echo esc_attr(zendotech_get_footer_option('copyright_text')); ?>"
                        placeholder="&copy; {year} {site_name}. All Rights Reserved." />
                </div>

                <div class="zfb-field">
                    <label>Bottom Links</label>
                    <div class="zfb-repeater" id="bottom-links-repeater">
                        <?php
                        $bottom_links = zendotech_get_footer_option('bottom_links');
                        if (!empty($bottom_links)):
                            foreach ($bottom_links as $bl):
                                ?>
                                <div class="zfb-repeater-row">
                                    <input type="text" name="bottom_links[text][]" value="<?php echo esc_attr($bl['text']); ?>"
                                        placeholder="Link Text" />
                                    <input type="text" name="bottom_links[url][]" value="<?php echo esc_url($bl['url']); ?>"
                                        placeholder="URL" />
                                    <button type="button" class="zfb-remove-row" title="Remove"><i
                                            class="dashicons dashicons-no-alt"></i></button>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <button type="button" class="button zfb-add-bottom-link"><i class="dashicons dashicons-plus-alt2"></i>
                        Add Link</button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="zfb-save-bar">
                <button type="submit" class="button button-primary button-hero zfb-save-btn">
                    <i class="dashicons dashicons-saved"></i> Save Footer Settings
                </button>
                <button type="button" class="button zfb-reset-btn"
                    onclick="if(confirm('Reset all footer settings to defaults?')){document.querySelector('[name=reset_defaults]').value='1';this.form.submit();}">
                    <i class="dashicons dashicons-image-rotate"></i> Reset to Defaults
                </button>
                <input type="hidden" name="reset_defaults" value="0" />
            </div>
        </form>
    </div>

    <script>
        // Inline script to handle tab persistence if JS fails to load fast enough
        document.addEventListener('DOMContentLoaded', function () {
            const activeTab = '<?php echo esc_js($active_tab); ?>';
            if (activeTab && activeTab !== 'newsletter') {
                const tabBtn = document.querySelector(`.zfb-tab[data-tab="${activeTab}"]`);
                if (tabBtn) tabBtn.click();
            }
        });
    </script>
    <?php
}
