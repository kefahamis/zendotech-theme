<?php
/**
 * Zendotech Header Builder
 * 
 * Custom admin page under Header Builder
 * Allows editing all header sections dynamically without touching code.
 */

if (!defined('ABSPATH'))
    exit;

/* ============================================
   1. REGISTER ADMIN MENU
   ============================================ */
function zendotech_header_builder_menu()
{
    add_menu_page(
        'Header Builder',
        'Header Builder',
        'manage_options',
        'zendotech-header-builder',
        'zendotech_header_builder_page',
        'dashicons-layout',
        57
    );
}
add_action('admin_menu', 'zendotech_header_builder_menu');

/**
 * Handle save before page load to allow redirects
 */
function zendotech_header_builder_init()
{
    if (isset($_GET['page']) && $_GET['page'] === 'zendotech-header-builder') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zendotech_header_nonce'])) {
            $active_tab = sanitize_text_field($_POST['active_tab'] ?? 'topbar');
            if (zendotech_header_builder_save()) {
                wp_redirect(admin_url('admin.php?page=zendotech-header-builder&saved=1&tab=' . $active_tab));
                exit;
            } else {
                wp_redirect(admin_url('admin.php?page=zendotech-header-builder&saved=0&tab=' . $active_tab));
                exit;
            }
        }
    }
}
add_action('admin_init', 'zendotech_header_builder_init');

/* ============================================
   2. ENQUEUE ADMIN ASSETS
   ============================================ */
function zendotech_header_builder_assets($hook)
{
    if ($hook !== 'toplevel_page_zendotech-header-builder')
        return;

    wp_enqueue_media();
    // Reuse footer builder CSS/JS for consistency in the UI
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
add_action('admin_enqueue_scripts', 'zendotech_header_builder_assets');

/* ============================================
   3. AUTO-SEED DEFAULTS ON FIRST LOAD
   ============================================ */
function zendotech_header_seed_defaults()
{
    $existing = get_option('zendotech_header_options', false);
    if ($existing === false) {
        update_option('zendotech_header_options', zendotech_header_defaults());
    }
}
add_action('after_setup_theme', 'zendotech_header_seed_defaults');

/* ============================================
   4. DEFAULT HEADER OPTIONS
   ============================================ */
function zendotech_header_defaults()
{
    return array(
        // Top Bar
        'topbar_enabled' => '1',
        'topbar_text' => '🎵 Welcome to {site_name} — Feel Every Beat, Hear Every Detail',
        'topbar_links' => array(
            array('text' => 'Find a Store', 'icon' => 'fa-solid fa-location-dot', 'url' => '#'),
            array('text' => 'Track Order', 'icon' => 'fa-solid fa-truck', 'url' => '#'),
            array('text' => 'Studio Pro', 'icon' => 'fa-solid fa-guitar', 'url' => '#'),
        ),

        // Main Header
        'search_placeholder' => 'Search headphones, speakers, instruments...',
        'show_search_categories' => '1',
        'show_wishlist' => '1',
        'show_compare' => '1',
        'sticky_header' => '1',

        // Navigation
        'nav_cat_label' => 'Browse All Categories',
        'nav_cta_text' => 'Expert Advice',
        'nav_cta_url' => '#',
        'nav_cta_icon' => 'fa-solid fa-headset',
    );
}

/* ============================================
   5. GET HEADER OPTION (with defaults)
   ============================================ */
function zendotech_get_header_option($key)
{
    $saved = get_option('zendotech_header_options', array());
    $defaults = zendotech_header_defaults();

    if (isset($saved[$key])) {
        return $saved[$key];
    }

    return isset($defaults[$key]) ? $defaults[$key] : '';
}

function zendotech_header_parse($text)
{
    $text = str_replace('{site_name}', get_bloginfo('name'), $text);
    return $text;
}

/* ============================================
   6. SAVE HANDLER
   ============================================ */
function zendotech_header_builder_save()
{
    if (!isset($_POST['zendotech_header_nonce']) || !wp_verify_nonce($_POST['zendotech_header_nonce'], 'zendotech_save_header')) {
        return false;
    }
    if (!current_user_can('manage_options'))
        return false;

    if (!empty($_POST['reset_defaults']) && $_POST['reset_defaults'] === '1') {
        update_option('zendotech_header_options', zendotech_header_defaults());
        return true;
    }

    $data = array();

    $data['topbar_enabled'] = isset($_POST['topbar_enabled']) ? '1' : '0';
    $data['topbar_text'] = wp_kses_post($_POST['topbar_text'] ?? '');

    $topbar_links = array();
    if (!empty($_POST['topbar_link_text']) && is_array($_POST['topbar_link_text'])) {
        foreach ($_POST['topbar_link_text'] as $i => $text) {
            if (!empty(trim($text))) {
                $topbar_links[] = array(
                    'text' => sanitize_text_field($text),
                    'icon' => sanitize_text_field($_POST['topbar_link_icon'][$i] ?? ''),
                    'url' => esc_url_raw($_POST['topbar_link_url'][$i] ?? '#'),
                );
            }
        }
    }
    $data['topbar_links'] = $topbar_links;

    $data['search_placeholder'] = sanitize_text_field($_POST['search_placeholder'] ?? '');
    $data['show_search_categories'] = isset($_POST['show_search_categories']) ? '1' : '0';
    $data['show_wishlist'] = isset($_POST['show_wishlist']) ? '1' : '0';
    $data['show_compare'] = isset($_POST['show_compare']) ? '1' : '0';
    $data['sticky_header'] = isset($_POST['sticky_header']) ? '1' : '0';

    $data['nav_cat_label'] = sanitize_text_field($_POST['nav_cat_label'] ?? '');
    $data['nav_cta_text'] = sanitize_text_field($_POST['nav_cta_text'] ?? '');
    $data['nav_cta_url'] = esc_url_raw($_POST['nav_cta_url'] ?? '#');
    $data['nav_cta_icon'] = sanitize_text_field($_POST['nav_cta_icon'] ?? '');

    update_option('zendotech_header_options', $data);
    return true;
}

/* ============================================
   7. ADMIN PAGE RENDER
   ============================================ */
function zendotech_header_builder_page()
{
    $saved_msg = '';
    $status = isset($_GET['saved']) ? $_GET['saved'] : '';
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'topbar';

    if ($status === '1') {
        $saved_msg = 'Header settings saved successfully!';
    } elseif ($status === '0') {
        $saved_msg = 'Error: Failed to save settings (Invalid nonce or permission).';
    }
    ?>
    <div class="wrap zfb-wrap">
        <div class="zfb-header">
            <div class="zfb-header-left">
                <h1><i class="dashicons dashicons-layout"></i> Header Builder</h1>
                <p>Manage announcements, search, and navigation bars.</p>
            </div>
        </div>

        <?php if ($saved_msg): ?>
            <div class="notice <?php echo $status === '1' ? 'notice-success' : 'notice-error'; ?> is-dismissible zfb-notice">
                <p><strong><?php echo esc_html($saved_msg); ?></strong></p>
            </div>
        <?php endif; ?>

        <form method="post" class="zfb-form" action="" novalidate>
            <?php wp_nonce_field('zendotech_save_header', 'zendotech_header_nonce'); ?>
            <input type="hidden" name="active_tab" id="zfb_active_tab" value="<?php echo esc_attr($active_tab); ?>" />

            <div class="zfb-tabs">
                <button type="button" class="zfb-tab active" data-tab="topbar">Top Bar</button>
                <button type="button" class="zfb-tab" data-tab="mainheader">Main Header</button>
                <button type="button" class="zfb-tab" data-tab="navigation">Navigation</button>
            </div>

            <!-- Top Bar Panel -->
            <div class="zfb-panel active" id="tab-topbar">
                <div class="zfb-section-card">
                    <div class="zfb-field">
                        <label class="zfb-toggle-label">
                            <input type="checkbox" name="topbar_enabled" value="1" <?php checked(zendotech_get_header_option('topbar_enabled'), '1'); ?> />
                            <span class="zfb-toggle-switch"></span>
                            <span>Enable Top Bar</span>
                        </label>
                    </div>

                    <div class="zfb-field">
                        <label>Welcome Text</label>
                        <input type="text" name="topbar_text"
                            value="<?php echo esc_attr(zendotech_get_header_option('topbar_text')); ?>" />
                    </div>

                    <div class="zfb-field">
                        <label>Utility Links</label>
                        <div class="zfb-repeater">
                            <?php
                            $links = zendotech_get_header_option('topbar_links');
                            if (!empty($links)):
                                foreach ($links as $link): ?>
                                    <div class="zfb-repeater-row">
                                        <input type="text" name="topbar_link_icon[]" value="<?php echo esc_attr($link['icon']); ?>"
                                            placeholder="Icon (fa-solid fa-truck)" />
                                        <input type="text" name="topbar_link_text[]" value="<?php echo esc_attr($link['text']); ?>"
                                            placeholder="Text" />
                                        <input type="text" name="topbar_link_url[]" value="<?php echo esc_attr($link['url']); ?>"
                                            placeholder="URL" />
                                        <button type="button" class="zfb-remove-row"><i
                                                class="dashicons dashicons-no-alt"></i></button>
                                    </div>
                                <?php endforeach;
                            endif; ?>
                        </div>
                        <button type="button" class="button zfb-add-row-manual">Add Link</button>
                    </div>
                </div>
            </div>

            <!-- Main Header Panel -->
            <div class="zfb-panel" id="tab-mainheader">
                <div class="zfb-section-card">
                    <div class="zfb-field">
                        <label>Search Placeholder</label>
                        <input type="text" name="search_placeholder"
                            value="<?php echo esc_attr(zendotech_get_header_option('search_placeholder')); ?>" />
                    </div>
                    <div class="zfb-field">
                        <label class="zfb-toggle-label">
                            <input type="checkbox" name="show_search_categories" value="1" <?php checked(zendotech_get_header_option('show_search_categories'), '1'); ?> />
                            <span class="zfb-toggle-switch"></span>
                            <span>Show All Categories Dropdown in Search</span>
                        </label>
                    </div>
                    <div class="zfb-field">
                        <label class="zfb-toggle-label">
                            <input type="checkbox" name="show_wishlist" value="1" <?php checked(zendotech_get_header_option('show_wishlist'), '1'); ?> />
                            <span class="zfb-toggle-switch"></span>
                            <span>Show Wishlist Badge</span>
                        </label>
                    </div>
                    <div class="zfb-field">
                        <label class="zfb-toggle-label">
                            <input type="checkbox" name="show_compare" value="1" <?php checked(zendotech_get_header_option('show_compare'), '1'); ?> />
                            <span class="zfb-toggle-switch"></span>
                            <span>Show Compare Badge</span>
                        </label>
                    </div>
                    <div class="zfb-field">
                        <label class="zfb-toggle-label">
                            <input type="checkbox" name="sticky_header" value="1" <?php checked(zendotech_get_header_option('sticky_header'), '1'); ?> />
                            <span class="zfb-toggle-switch"></span>
                            <span>Enable Sticky Header</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Navigation Panel -->
            <div class="zfb-panel" id="tab-navigation">
                <div class="zfb-section-card">
                    <div class="zfb-field">
                        <label>Categories Label</label>
                        <input type="text" name="nav_cat_label"
                            value="<?php echo esc_attr(zendotech_get_header_option('nav_cat_label')); ?>" />
                    </div>
                    <hr>
                    <div class="zfb-field">
                        <label>Right CTA Text</label>
                        <input type="text" name="nav_cta_text"
                            value="<?php echo esc_attr(zendotech_get_header_option('nav_cta_text')); ?>" />
                    </div>
                    <div class="zfb-field">
                        <label>Right CTA URL</label>
                        <input type="text" name="nav_cta_url"
                            value="<?php echo esc_attr(zendotech_get_header_option('nav_cta_url')); ?>" />
                    </div>
                    <div class="zfb-field">
                        <label>Right CTA Icon</label>
                        <input type="text" name="nav_cta_icon"
                            value="<?php echo esc_attr(zendotech_get_header_option('nav_cta_icon')); ?>" />
                    </div>
                </div>
            </div>

            <div class="zfb-save-bar">
                <button type="submit" class="button button-primary button-hero zfb-save-btn">
                    <i class="dashicons dashicons-saved"></i> Save Header Settings
                </button>
                <button type="button" class="button zfb-reset-btn"
                    onclick="if(confirm('Reset all header settings to defaults?')){document.querySelector('[name=reset_defaults]').value='1';this.form.submit();}">
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
            if (activeTab && activeTab !== 'topbar') {
                const tabBtn = document.querySelector(`.zfb-tab[data-tab="${activeTab}"]`);
                if (tabBtn) tabBtn.click();
            }
        });
    </script>
    <?php
}
