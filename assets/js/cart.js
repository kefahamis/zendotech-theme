/* ============================================
   ZENDOTECH AUDIO — CART PAGE MODULE
   Renders cart items from localStorage,
   handles qty changes, removal, coupon,
   shipping, and totals computation.
   ============================================ */

(function () {
    'use strict';

    const { CartStore, Toast } = window.ZendotechApp || {};
    if (!CartStore) {
        console.warn('CartStore not found. Ensure app.js loads before cart.js.');
        return;
    }

    /* -------------------------------------------
       CONSTANTS
       ------------------------------------------- */
    const FREE_SHIPPING_THRESHOLD = 75;
    const SHIPPING_RATES = { free: 0, flat: 10, express: 25 };

    /* -------------------------------------------
       DOM REFERENCES
       ------------------------------------------- */
    const cartContent = document.getElementById('cartContent');
    const cartEmpty = document.getElementById('cartEmpty');
    const cartItems = document.getElementById('cartItems');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('cartTotal');
    const shippingProgress = document.getElementById('shippingProgress');
    const shippingText = document.getElementById('shippingText');
    const couponInput = document.getElementById('couponInput');
    const applyCouponBtn = document.getElementById('applyCoupon');
    const updateCartBtn = document.getElementById('updateCart');
    const clearCartBtn = document.getElementById('clearCart');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // WooCommerce cart page uses [woocommerce_cart] and does not have these IDs — skip custom cart UI
    if (!cartContent || !cartItems) {
        return;
    }

    let appliedCoupon = null;

    /* -------------------------------------------
       RENDER CART ITEMS
       ------------------------------------------- */
    function renderCart() {
        const items = CartStore.getAll();

        // Toggle empty / content states
        if (items.length === 0) {
            cartContent.style.display = 'none';
            cartEmpty.style.display = 'block';
            return;
        }

        cartContent.style.display = 'block';
        cartEmpty.style.display = 'none';

        // Render items
        cartItems.innerHTML = items.map(item => `
            <div class="ct-item" data-id="${item.id}">
                <div class="ct-product">
                    <div class="ct-product-img">
                        <img src="${item.image || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22%3E%3Crect width=%2280%22 height=%2280%22 fill=%22%23f0f0f0%22/%3E%3C/svg%3E'}" alt="${item.name}">
                    </div>
                    <div class="ct-product-info">
                        <h4><a href="product.html">${item.name}</a></h4>
                        <div class="ct-product-meta">
                            ${item.color ? `<span><i class="fa-solid fa-palette"></i> ${item.color}</span>` : ''}
                            ${item.variant ? `<span><i class="fa-solid fa-layer-group"></i> ${item.variant}</span>` : ''}
                        </div>
                    </div>
                </div>
                <div class="ct-price">$${item.price.toFixed(2)}</div>
                <div class="ct-qty">
                    <div class="ct-qty-wrap">
                        <button class="ct-qty-btn" data-action="minus" data-id="${item.id}">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <input type="number" class="ct-qty-input" value="${item.qty}" min="1" max="10" data-id="${item.id}">
                        <button class="ct-qty-btn" data-action="plus" data-id="${item.id}">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="ct-subtotal">$${(item.price * item.qty).toFixed(2)}</div>
                <button class="ct-remove" data-id="${item.id}" title="Remove item">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');

        updateTotals();
        updateProgress();
    }

    /* -------------------------------------------
       UPDATE TOTALS
       ------------------------------------------- */
    function updateTotals() {
        const subtotal = CartStore.getTotal();
        let discount = 0;

        // Apply coupon discount
        if (appliedCoupon) {
            if (appliedCoupon.type === 'percent') {
                discount = subtotal * (appliedCoupon.value / 100);
            } else {
                discount = appliedCoupon.value;
            }
        }

        const discountedSubtotal = Math.max(0, subtotal - discount);

        // Get selected shipping
        const shippingRadio = document.querySelector('input[name="shipping"]:checked');
        const shippingKey = shippingRadio ? shippingRadio.value : 'free';
        let shippingCost = SHIPPING_RATES[shippingKey] || 0;

        // Free shipping if over threshold
        if (subtotal >= FREE_SHIPPING_THRESHOLD && shippingKey === 'free') {
            shippingCost = 0;
        }

        const total = discountedSubtotal + shippingCost;

        cartSubtotal.textContent = '$' + subtotal.toFixed(2);
        cartTotal.textContent = '$' + total.toFixed(2);
    }

    /* -------------------------------------------
       FREE SHIPPING PROGRESS
       ------------------------------------------- */
    function updateProgress() {
        const subtotal = CartStore.getTotal();
        const remaining = Math.max(0, FREE_SHIPPING_THRESHOLD - subtotal);
        const percent = Math.min(100, (subtotal / FREE_SHIPPING_THRESHOLD) * 100);

        shippingProgress.style.width = percent + '%';

        if (remaining <= 0) {
            shippingProgress.classList.add('complete');
            shippingText.innerHTML = `
                <i class="fa-solid fa-circle-check" style="color:#059669"></i>
                <span>🎉 You've unlocked <strong>FREE Shipping!</strong></span>
            `;
        } else {
            shippingProgress.classList.remove('complete');
            shippingText.innerHTML = `
                <i class="fa-solid fa-truck-fast"></i>
                <span>Add <strong>$${remaining.toFixed(2)}</strong> more to get <strong>FREE Shipping!</strong></span>
            `;
        }
    }

    /* -------------------------------------------
       EVENT: QTY BUTTONS & REMOVE
       ------------------------------------------- */
    cartItems.addEventListener('click', (e) => {
        // Qty buttons
        const qtyBtn = e.target.closest('.ct-qty-btn');
        if (qtyBtn) {
            const id = qtyBtn.dataset.id;
            const action = qtyBtn.dataset.action;
            const items = CartStore.getAll();
            const item = items.find(i => i.id === id);
            if (!item) return;

            const newQty = action === 'plus' ? item.qty + 1 : item.qty - 1;
            if (newQty < 1) {
                CartStore.remove(id);
                Toast.show(`"${item.name}" removed from cart`, 'info');
            } else if (newQty <= 10) {
                CartStore.updateQty(id, newQty);
            }
            renderCart();
            return;
        }

        // Remove button
        const removeBtn = e.target.closest('.ct-remove');
        if (removeBtn) {
            const id = removeBtn.dataset.id;
            const items = CartStore.getAll();
            const item = items.find(i => i.id === id);
            // Animate out
            const row = removeBtn.closest('.ct-item');
            if (row) {
                row.style.transition = 'all .3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(30px)';
                setTimeout(() => {
                    CartStore.remove(id);
                    if (item) Toast.show(`"${item.name}" removed from cart`, 'info');
                    renderCart();
                }, 300);
            }
        }
    });

    // Qty input manual change
    cartItems.addEventListener('change', (e) => {
        if (e.target.classList.contains('ct-qty-input')) {
            const id = e.target.dataset.id;
            let val = parseInt(e.target.value) || 1;
            val = Math.max(1, Math.min(10, val));
            CartStore.updateQty(id, val);
            renderCart();
        }
    });

    /* -------------------------------------------
       EVENT: SHIPPING OPTIONS
       ------------------------------------------- */
    document.querySelectorAll('input[name="shipping"]').forEach(radio => {
        radio.addEventListener('change', updateTotals);
    });

    /* -------------------------------------------
       EVENT: COUPON
       ------------------------------------------- */
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', () => {
            const code = couponInput.value.trim().toUpperCase();
            if (!code) {
                Toast.show('Please enter a coupon code', 'error');
                return;
            }

            // Demo coupons
            const validCoupons = {
                'AUDIO15': { type: 'percent', value: 15, label: '15% off' },
                'SAVE10': { type: 'fixed', value: 10, label: '$10 off' },
                'WELCOME20': { type: 'percent', value: 20, label: '20% off' }
            };

            if (validCoupons[code]) {
                appliedCoupon = validCoupons[code];
                Toast.show(`Coupon "${code}" applied! ${appliedCoupon.label}`, 'success');
                applyCouponBtn.textContent = '✓ Applied';
                applyCouponBtn.style.background = '#10B981';
                applyCouponBtn.style.color = '#fff';
                applyCouponBtn.style.borderColor = '#10B981';
                couponInput.disabled = true;
                updateTotals();
            } else {
                Toast.show('Invalid coupon code. Try AUDIO15, SAVE10, or WELCOME20', 'error');
            }
        });
    }

    /* -------------------------------------------
       EVENT: UPDATE CART BUTTON
       ------------------------------------------- */
    if (updateCartBtn) {
        updateCartBtn.addEventListener('click', () => {
            // Re-read all qty inputs and update
            const inputs = cartItems.querySelectorAll('.ct-qty-input');
            inputs.forEach(input => {
                const id = input.dataset.id;
                let val = parseInt(input.value) || 1;
                val = Math.max(1, Math.min(10, val));
                CartStore.updateQty(id, val);
            });
            renderCart();
            Toast.show('Cart updated successfully', 'success');
        });
    }

    /* -------------------------------------------
       EVENT: CLEAR CART
       ------------------------------------------- */
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', () => {
            if (CartStore.getAll().length === 0) return;
            CartStore.clear();
            appliedCoupon = null;
            renderCart();
            Toast.show('Cart cleared', 'info');
        });
    }

    /* -------------------------------------------
       EVENT: CHECKOUT BUTTON
       ------------------------------------------- */
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (CartStore.getAll().length === 0) {
                Toast.show('Your cart is empty', 'error');
                return;
            }
            // Redirect to real checkout. Use localized zendotechData if available.
            const checkoutUrl = (typeof zendotechData !== 'undefined' && zendotechData.checkoutUrl) ? zendotechData.checkoutUrl : '/checkout/';
            window.location.href = checkoutUrl;
        });
    }

    /* -------------------------------------------
       INIT
       ------------------------------------------- */
    renderCart();

})();
