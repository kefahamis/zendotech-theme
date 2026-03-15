(function () {
    'use strict';

    function initCheckout() {
        const itemsContainer = document.getElementById('checkoutItems');
        const subtotalEl = document.getElementById('checkoutSubtotal');
        const totalEl = document.getElementById('checkoutTotal');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const checkoutForm = document.getElementById('checkoutForm');

        if (!itemsContainer || !window.ZendotechApp) return;

        const { CartStore, Toast } = window.ZendotechApp;

        // Render Cart Items
        function renderOrderSummary() {
            const items = CartStore.getAll();
            const total = CartStore.getTotal();

            if (items.length === 0) {
                itemsContainer.innerHTML = '<p class="empty-msg">Your cart is empty.</p>';
                subtotalEl.textContent = 'KSh 0.00';
                totalEl.textContent = 'KSh 0.00';
                placeOrderBtn.disabled = true;
                return;
            }

            itemsContainer.innerHTML = items.map(item => `
                <div class="co-item">
                    <div class="co-img">
                        <img src="${item.image}" alt="${item.name}">
                        <span class="co-qty">${item.qty}</span>
                    </div>
                    <div class="co-info">
                        <h4>${item.name}</h4>
                        <span class="co-price">KSh ${(item.price * item.qty).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </div>
                </div>
            `).join('');

            subtotalEl.textContent = `KSh ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            totalEl.textContent = `KSh ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            placeOrderBtn.disabled = false;
        }

        // Handle Form Submit
        checkoutForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Simple validation is handled by 'required' attributes

            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

            // Simulate API call (only used if custom checkout form with #checkoutForm exists)
            setTimeout(() => {
                Toast.show('Order placed successfully! Redirecting...', 'success');
                CartStore.clear(); // Clear cart

                const homeUrl = (typeof zendotechData !== 'undefined' && zendotechData.homeUrl) ? zendotechData.homeUrl : '/';
                setTimeout(() => {
                    window.location.href = homeUrl;
                }, 2000);
            }, 1500);
        });

        // Initial Render
        renderOrderSummary();

        // Listen for internal cart updates (if any mechanism exists, otherwise rely on page load)
        // Since checkout is a fresh page load, we just render once.
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCheckout);
    } else {
        initCheckout();
    }

})();
