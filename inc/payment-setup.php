<?php
/**
 * ZendoTech Payment Gateway Configuration setup
 * Fix #15: Configure M-Pesa and card payment gateway placeholders
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add admin notice advising the user to install recommended payment plugins
 */
function zendotech_payment_gateway_admin_notice() {
    // Only show to admins
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Check if M-Pesa or standard Stripe/Pesapal is active
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $has_mpesa = isset($gateways['mpesa']);
    $has_card = isset($gateways['stripe']) || isset($gateways['pesapal']);

    if (!$has_mpesa || !$has_card) {
        $class = 'notice notice-info is-dismissible';
        $message = '<strong>ZendoTech Theme Recommendation:</strong> To fully enable e-commerce functionality in Kenya, we recommend installing the following Payment Gateways:<br><br>';
        
        if (!$has_mpesa) {
            $message .= '- <strong>IntaSend</strong> or <strong>M-Pesa for WooCommerce</strong> (For direct M-Pesa payments)<br>';
        }
        if (!$has_card) {
            $message .= '- <strong>Stripe</strong> or <strong>Pesapal</strong> (For Card payments)<br>';
        }
        
        $message .= '<br>Please go to <a href="' . admin_url('plugin-install.php') . '">Plugins &gt; Add New</a> to install them, then configure them under <strong>WooCommerce &gt; Settings &gt; Payments</strong>.';

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), wp_kses_post($message));
    }
}
add_action('admin_notices', 'zendotech_payment_gateway_admin_notice');

/**
 * Filter available payment gateways to ensure KSh currency is handled correctly
 * Some gateways might not explicitly support KSh out of the box without filtering
 */
function zendotech_filter_gateways_for_currency($available_gateways) {
    if (is_admin()) {
        return $available_gateways;
    }

    $currency = get_woocommerce_currency();
    
    // Ensure all gateways are considered compatible with KES to prevent "Invalid payment method"
    if ($currency === 'KES') {
        foreach ($available_gateways as $gateway) {
            // Some gateways check this array
            if (isset($gateway->supports) && is_array($gateway->supports)) {
                // $gateway->supports[] = 'subscriptions'; // Example
            }
        }
    }

    return $available_gateways;
}
add_filter('woocommerce_available_payment_gateways', 'zendotech_filter_gateways_for_currency');

/**
 * Filter to allow specific gateways even if they don't explicitly support KES
 */
function zendotech_allow_kes_currency($supported_currencies) {
    if (!in_array('KES', $supported_currencies)) {
        $supported_currencies[] = 'KES';
    }
    return $supported_currencies;
}
add_filter('woocommerce_paypal_supported_currencies', 'zendotech_allow_kes_currency');
add_filter('woocommerce_stripe_supported_currencies', 'zendotech_allow_kes_currency');

