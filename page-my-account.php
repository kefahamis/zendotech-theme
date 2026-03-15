<?php
/**
 * Template Name: My Account Page
 * 
 * Themed My Account page for Zendotech Audio
 * Wraps WooCommerce my-account shortcode with the theme's look and feel
 */
get_header();

wp_enqueue_style('zendotech-account-style', get_template_directory_uri() . '/assets/css/account.css', array(), '1.0.2');
wp_enqueue_script('zendotech-account-js', get_template_directory_uri() . '/assets/js/account.js', array(), '1.0.2', true);
?>

<!-- ===== BREADCRUMB ===== -->
<section class="breadcrumb-bar">
    <div class="container">
        <div class="breadcrumb-inner">
            <ol class="breadcrumb">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li class="separator"><i class="fa-solid fa-chevron-right"></i></li>
                <li class="current">My Account</li>
            </ol>
        </div>
    </div>
</section>

<!-- ===== MY ACCOUNT ===== -->
<section class="account-section">
    <div class="container">
        <!-- Account Header -->
        <div class="account-header">
            <div class="ah-icon"><i class="fa-solid fa-user-circle"></i></div>
            <h1>My Account</h1>
            <?php if (!is_user_logged_in()): ?>
                <p>Sign in to access your orders, wishlist, and exclusive member benefits.</p>
            <?php else: ?>
                <?php $current_user = wp_get_current_user(); ?>
                <p>Welcome back, <strong><?php echo esc_html($current_user->display_name); ?></strong>! Manage your account
                    below.</p>
            <?php endif; ?>
        </div>

        <?php if (!is_user_logged_in()): ?>
            <!-- ===== GUEST: LOGIN / REGISTER FORMS ===== -->
            <div class="account-forms">
                <!-- LOGIN FORM -->
                <div class="auth-card">
                    <div class="ac-header">
                        <div class="ac-icon"><i class="fa-solid fa-right-to-bracket"></i></div>
                        <h3>Login</h3>
                        <p>Welcome back! Sign in to your account.</p>
                    </div>
                    <form class="auth-form woocommerce-form woocommerce-form-login login" method="post">
                        <?php do_action('woocommerce_login_form_start'); ?>

                        <div class="form-group">
                            <label for="username">Username or email address <span class="required">*</span></label>
                            <div class="input-wrap">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="text" class="woocommerce-Input input-text" name="username" id="username"
                                    autocomplete="username" placeholder="your@email.com"
                                    value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                                    required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="required">*</span></label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input class="woocommerce-Input input-text" type="password" name="password" id="password"
                                    autocomplete="current-password" placeholder="Enter your password" required />
                                <button type="button" class="toggle-pass" title="Show password"><i
                                        class="fa-regular fa-eye"></i></button>
                            </div>
                        </div>

                        <?php do_action('woocommerce_login_form'); ?>

                        <div class="form-row">
                            <label class="check-label-auth woocommerce-form__label">
                                <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme"
                                    type="checkbox" id="rememberme" value="forever" />
                                <span class="checkmark-auth"></span>
                                Remember me
                            </label>
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-link">Lost your
                                password?</a>
                        </div>

                        <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                        <button type="submit" class="btn btn-primary btn-auth woocommerce-button" name="login"
                            value="<?php esc_attr_e('Log in', 'woocommerce'); ?>">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Log In
                        </button>

                        <?php do_action('woocommerce_login_form_end'); ?>
                    </form>

                    <!-- Social Login -->
                    <div class="social-login">
                        <span class="sl-divider"><span>or sign in with</span></span>
                        <div class="sl-buttons">
                            <button class="sl-btn sl-google"><i class="fa-brands fa-google"></i> Google</button>
                            <button class="sl-btn sl-facebook"><i class="fa-brands fa-facebook-f"></i> Facebook</button>
                            <button class="sl-btn sl-apple"><i class="fa-brands fa-apple"></i> Apple</button>
                        </div>
                    </div>
                </div>

                <!-- REGISTER FORM -->
                <?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')): ?>
                    <div class="auth-card">
                        <div class="ac-header">
                            <div class="ac-icon"><i class="fa-solid fa-user-plus"></i></div>
                            <h3>Register</h3>
                            <p>Create a new account to get started.</p>
                        </div>
                        <form method="post" class="auth-form woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?>>
                            <?php do_action('woocommerce_register_form_start'); ?>

                            <?php if ('no' === get_option('woocommerce_registration_generate_username')): ?>
                                <div class="form-group">
                                    <label for="reg_username">Username <span class="required">*</span></label>
                                    <div class="input-wrap">
                                        <i class="fa-regular fa-user"></i>
                                        <input type="text" class="woocommerce-Input input-text" name="username" id="reg_username"
                                            autocomplete="username" placeholder="Choose a username"
                                            value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                                            required />
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="reg_email">Email address <span class="required">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa-regular fa-envelope"></i>
                                    <input type="email" class="woocommerce-Input input-text" name="email" id="reg_email"
                                        autocomplete="email" placeholder="your@email.com"
                                        value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"
                                        required />
                                </div>
                            </div>

                            <?php if ('no' === get_option('woocommerce_registration_generate_password')): ?>
                                <div class="form-group">
                                    <label for="reg_password">Password <span class="required">*</span></label>
                                    <div class="input-wrap">
                                        <i class="fa-solid fa-lock"></i>
                                        <input type="password" class="woocommerce-Input input-text" name="password"
                                            id="reg_password" autocomplete="new-password" placeholder="Create a password"
                                            required />
                                        <button type="button" class="toggle-pass" title="Show password"><i
                                                class="fa-regular fa-eye"></i></button>
                                    </div>
                                    <div class="password-strength">
                                        <div class="ps-bar">
                                            <div class="ps-fill" id="psBar"></div>
                                        </div>
                                        <span class="ps-text" id="psText">Password strength</span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php do_action('woocommerce_register_form'); ?>

                            <div class="form-group">
                                <label class="check-label-auth">
                                    <input type="checkbox" name="terms_agree" required />
                                    <span class="checkmark-auth"></span>
                                    I agree to the <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy Policy</a>
                                    and <a href="#">Terms of Service</a>
                                </label>
                            </div>

                            <p class="privacy-note">Your personal data will be used to support your experience throughout this
                                website, to manage access to your account, and for other purposes described in our privacy
                                policy.</p>

                            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                            <button type="submit" class="btn btn-primary btn-auth woocommerce-Button" name="register"
                                value="<?php esc_attr_e('Register', 'woocommerce'); ?>">
                                <i class="fa-solid fa-user-plus"></i> Register
                            </button>

                            <?php do_action('woocommerce_register_form_end'); ?>
                        </form>

                        <!-- Member Benefits -->
                        <div class="register-perks">
                            <h5>Member Benefits</h5>
                            <ul>
                                <li><i class="fa-solid fa-check-circle"></i> Exclusive member-only deals</li>
                                <li><i class="fa-solid fa-check-circle"></i> Early access to new arrivals</li>
                                <li><i class="fa-solid fa-check-circle"></i> Order tracking & history</li>
                                <li><i class="fa-solid fa-check-circle"></i> Curated recommendations</li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Registration disabled: Show info card instead -->
                    <div class="auth-card">
                        <div class="ac-header">
                            <div class="ac-icon"><i class="fa-solid fa-user-plus"></i></div>
                            <h3>New Here?</h3>
                            <p>Registration is currently managed by the store admin. Contact us for assistance.</p>
                        </div>
                        <div class="register-perks">
                            <h5>Member Benefits</h5>
                            <ul>
                                <li><i class="fa-solid fa-check-circle"></i> Exclusive member-only deals</li>
                                <li><i class="fa-solid fa-check-circle"></i> Early access to new arrivals</li>
                                <li><i class="fa-solid fa-check-circle"></i> Order tracking & history</li>
                                <li><i class="fa-solid fa-check-circle"></i> Curated recommendations</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- ===== LOGGED IN: WOOCOMMERCE DASHBOARD ===== -->
            <div class="account-dashboard">
                <?php echo do_shortcode('[woocommerce_my_account]'); ?>
            </div>
        <?php endif; ?>

        <!-- Guarantee Strip -->
        <div class="account-guarantees">
            <div class="ag-item">
                <div class="ag-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div>
                    <h4>Free Shipping</h4>
                    <p>Free Shipping for orders over $75</p>
                </div>
            </div>
            <div class="ag-item">
                <div class="ag-icon"><i class="fa-solid fa-rotate-left"></i></div>
                <div>
                    <h4>Money Guarantee</h4>
                    <p>Within 30 days for an exchange</p>
                </div>
            </div>
            <div class="ag-item">
                <div class="ag-icon"><i class="fa-solid fa-credit-card"></i></div>
                <div>
                    <h4>Flexible Payment</h4>
                    <p>Pay with Multiple Credit Cards</p>
                </div>
            </div>
            <div class="ag-item">
                <div class="ag-icon"><i class="fa-solid fa-headset"></i></div>
                <div>
                    <h4>Online Support</h4>
                    <p>24 hours a day, 7 days a week</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>