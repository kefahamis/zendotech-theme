/* ============================================
   ZENDOTECH AUDIO — MY ACCOUNT PAGE JS
   Forms, Validation, Password Strength
   WooCommerce Integration
   ============================================ */

(function () {
    'use strict';

    const { Toast } = window.ZendotechApp || {};

    /* -------------------------------------------
       1. PASSWORD SHOW/HIDE TOGGLE
       ------------------------------------------- */
    function initPasswordToggles() {
        document.querySelectorAll('.toggle-pass').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.parentElement.querySelector('input');
                if (!input) return;

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                }
            });
        });
    }

    /* -------------------------------------------
       2. PASSWORD STRENGTH METER
       ------------------------------------------- */
    function initPasswordStrength() {
        const regPassword = document.getElementById('reg_password');
        const strengthBar = document.getElementById('psBar');
        const strengthText = document.getElementById('psText');

        if (!regPassword || !strengthBar) return;

        regPassword.addEventListener('input', () => {
            const val = regPassword.value;
            const result = calculateStrength(val);

            strengthBar.style.width = result.percent + '%';
            strengthBar.style.background = result.color;
            if (strengthText) {
                strengthText.textContent = result.label;
                strengthText.style.color = result.color;
            }
        });
    }

    function calculateStrength(password) {
        if (!password) return { percent: 0, color: '#ccc', label: 'Password strength' };

        let score = 0;

        // Length
        if (password.length >= 6) score++;
        if (password.length >= 10) score++;
        if (password.length >= 14) score++;

        // Character variety
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;

        // Common patterns (reduce score)
        if (/^(password|123456|qwerty|admin)/i.test(password)) score = 1;

        if (score <= 2) return { percent: 25, color: '#EF4444', label: 'Weak' };
        if (score <= 4) return { percent: 50, color: '#F59E0B', label: 'Fair' };
        if (score <= 5) return { percent: 75, color: '#3B82F6', label: 'Good' };
        return { percent: 100, color: '#10B981', label: 'Strong' };
    }

    /* -------------------------------------------
       3. INPUT FOCUS ENHANCEMENT
       ------------------------------------------- */
    function initInputFocus() {
        document.querySelectorAll('.input-wrap input').forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.input-wrap')?.classList.add('focused');
            });
            input.addEventListener('blur', () => {
                input.closest('.input-wrap')?.classList.remove('focused');
            });
        });
    }

    /* -------------------------------------------
       4. WOOCOMMERCE ERROR DISPLAY
       ------------------------------------------- */
    function initWooCommerceErrors() {
        // Style WooCommerce error messages
        const errors = document.querySelectorAll('.woocommerce-error');
        errors.forEach(error => {
            error.style.borderRadius = '12px';
            error.style.padding = '16px 20px';
        });
    }

    /* -------------------------------------------
       5. LOADING STATE ON FORM SUBMIT
       ------------------------------------------- */
    function initFormSubmitLoading() {
        // Login form
        const loginForm = document.querySelector('.woocommerce-form-login');
        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                const btn = loginForm.querySelector('.btn-auth');
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing in...';
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.8';
                }
            });
        }

        // Register form
        const registerForm = document.querySelector('.woocommerce-form-register');
        if (registerForm) {
            registerForm.addEventListener('submit', function () {
                const btn = registerForm.querySelector('.btn-auth');
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating account...';
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.8';
                }
            });
        }
    }

    /* -------------------------------------------
       6. DASHBOARD NAVIGATION ACTIVE STATE
       ------------------------------------------- */
    function initDashboardNav() {
        // Ensure proper active state styling
        const navItems = document.querySelectorAll('.woocommerce-MyAccount-navigation-link');
        navItems.forEach(item => {
            const link = item.querySelector('a');
            if (link) {
                link.addEventListener('mousedown', () => {
                    link.style.transform = 'scale(0.97)';
                });
                link.addEventListener('mouseup', () => {
                    link.style.transform = '';
                });
            }
        });
    }

    /* -------------------------------------------
       INITIALIZATION
       ------------------------------------------- */
    function init() {
        initPasswordToggles();
        initPasswordStrength();
        initInputFocus();
        initWooCommerceErrors();
        initFormSubmitLoading();
        initDashboardNav();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
