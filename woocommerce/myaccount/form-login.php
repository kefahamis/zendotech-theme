<?php
/**
 * Login Form - WooCommerce Template Override
 * 
 * This template is intentionally empty because the Zendotech theme handles
 * the login/register forms directly in page-my-account.php with custom
 * themed markup while still using WooCommerce form actions and nonces.
 * 
 * @see page-my-account.php for the themed login/register forms
 */

// If somehow this template is called outside of our page template, 
// fall back to the default WooCommerce behavior
if (!is_page_template('page-my-account.php')) {
    wc_get_template('myaccount/form-login-default.php');
}
