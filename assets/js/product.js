/* ============================================
   ZENDOTECH AUDIO — PRODUCT PAGE JS
   Gallery, Tabs, Qty, Options, Cart, Reviews
   ============================================ */

(function () {
    'use strict';

    const getAppStores = () => window.ZendotechApp || {};
    const getCartStore = () => getAppStores().CartStore;
    const getWishlistStore = () => getAppStores().WishlistStore;
    const getToast = () => getAppStores().Toast;

    /* -------------------------------------------
       1. IMAGE GALLERY
       ------------------------------------------- */
    function initGallery() {
        const thumbs = document.querySelectorAll('.thumb');
        const mainImg = document.getElementById('mainImage');
        if (!thumbs.length || !mainImg) return;

        thumbs.forEach(t => {
            t.addEventListener('click', () => {
                thumbs.forEach(th => th.classList.remove('active'));
                t.classList.add('active');
                mainImg.style.opacity = '0.3';
                setTimeout(() => {
                    mainImg.src = t.dataset.img;
                    mainImg.style.opacity = '1';
                }, 200);
            });
        });

        // Image transition
        mainImg.style.transition = 'opacity 0.2s ease';

        // Zoom lightbox
        const zoomBtn = document.querySelector('.gallery-zoom');
        if (zoomBtn) {
            zoomBtn.addEventListener('click', () => openLightbox(mainImg.src));
        }

        // Click main image to zoom
        mainImg.style.cursor = 'zoom-in';
        mainImg.addEventListener('click', () => openLightbox(mainImg.src));
    }

    function openLightbox(src) {
        const overlay = document.createElement('div');
        overlay.className = 'lightbox-overlay';
        overlay.innerHTML = `
            <div class="lightbox-content">
                <button class="lightbox-close"><i class="fa-solid fa-xmark"></i></button>
                <img src="${src}" alt="Zoomed product image">
            </div>
        `;
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => overlay.classList.add('open'));

        const close = () => {
            overlay.classList.remove('open');
            overlay.addEventListener('transitionend', () => {
                overlay.remove();
                document.body.style.overflow = '';
            }, { once: true });
        };

        overlay.querySelector('.lightbox-close').addEventListener('click', close);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) close();
        });
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                close();
                document.removeEventListener('keydown', escHandler);
            }
        });
    }

    /* -------------------------------------------
       2. QUANTITY CONTROLS
       ------------------------------------------- */
    function initQuantity() {
        const qtyInput = document.getElementById('qtyInput');
        const minusBtn = document.getElementById('qtyMinus');
        const plusBtn = document.getElementById('qtyPlus');
        if (!qtyInput) return;

        const update = (delta) => {
            let val = parseInt(qtyInput.value) || 1;
            val = Math.max(1, Math.min(val + delta, 10));
            qtyInput.value = val;
        };

        if (minusBtn) minusBtn.addEventListener('click', () => update(-1));
        if (plusBtn) plusBtn.addEventListener('click', () => update(1));

        qtyInput.addEventListener('change', () => {
            let val = parseInt(qtyInput.value) || 1;
            qtyInput.value = Math.max(1, Math.min(val, 10));
        });
    }

    /* -------------------------------------------
       3. COLOR SWATCHES
       ------------------------------------------- */
    function initSwatches() {
        const swatches = document.querySelectorAll('.swatch');
        const colorLabel = document.querySelector('.option-label strong');
        if (!swatches.length) return;

        swatches.forEach(s => {
            s.addEventListener('click', () => {
                swatches.forEach(sw => sw.classList.remove('active'));
                s.classList.add('active');
                if (colorLabel && s.title) {
                    colorLabel.textContent = s.title;
                }
            });
        });
    }

    /* -------------------------------------------
       4. VARIANT BUTTONS
       ------------------------------------------- */
    function initVariants() {
        const varBtns = document.querySelectorAll('.var-btn');
        if (!varBtns.length) return;

        varBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                varBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    }

    /* -------------------------------------------
       5. PRODUCT TABS
       ------------------------------------------- */
    function initTabs() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');
        if (!tabBtns.length) return;

        const switchTab = (tabId) => {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));

            const btn = [...tabBtns].find(b => b.dataset.tab === tabId);
            const panel = document.getElementById('tab-' + tabId);

            if (btn) btn.classList.add('active');
            if (panel) panel.classList.add('active');
        };

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                switchTab(btn.dataset.tab);
                // Update URL hash without scrolling
                history.replaceState(null, '', `#${btn.dataset.tab}`);
            });
        });

        // Check URL hash on load
        const hash = window.location.hash.replace('#', '');
        if (hash && [...tabBtns].some(b => b.dataset.tab === hash)) {
            switchTab(hash);
        }

        // Handle review link clicks
        document.querySelectorAll('a[href="#reviews"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                switchTab('reviews');
                document.querySelector('.product-tabs')?.scrollIntoView({ behavior: 'smooth' });
            });
        });
    }

    /* -------------------------------------------
       6. ADD TO CART (Product Page)
       ------------------------------------------- */
    function initProductCart() {
        const addBtn = document.querySelector('.add-to-cart-btn, .single_add_to_cart_button');
        const buyBtn = document.querySelector('.btn-buy-now, .buy-now-btn');
        if (!addBtn && !buyBtn) return;

        const getProductId = () => {
            return addBtn?.value || addBtn?.dataset.productId || document.querySelector('input[name="product_id"]')?.value || document.querySelector('input[name="add-to-cart"]')?.value || '';
        };

        const getQuantity = () => {
            return parseInt(document.querySelector('input.qty')?.value || document.getElementById('qtyInput')?.value) || 1;
        };

        /**
         * Add product to WooCommerce cart via AJAX.
         * @param {Function|null} onSuccess - callback after successful add
         */
        const wcAddToCart = (btn, onSuccess) => {
            const productId = getProductId();
            const quantity = getQuantity();
            const Toast = getToast();

            if (!productId) {
                if (Toast) Toast.show('Product ID not found.', 'error');
                return;
            }

            if (typeof jQuery === 'undefined' || typeof zendotechData === 'undefined') {
                if (Toast) Toast.show('Unable to add to cart. Try reloading the page.', 'error');
                return;
            }

            // Button loading state
            const origHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';
            btn.style.pointerEvents = 'none';

            let dataPayload = {
                action: 'zendotech_add_to_cart',
                product_id: productId,
                quantity: quantity,
                nonce: zendotechData.nonce
            };

            const form = btn.closest('form.cart');
            if (form) {
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    if (key !== 'action' && key !== 'add-to-cart') {
                        dataPayload[key] = value;
                    }
                }
            }

            jQuery.ajax({
                url: zendotechData.ajaxUrl,
                type: 'POST',
                data: dataPayload,
                success: function (response) {
                    // Handle WP nonce failure which returns `-1` or plain -1
                    if (response === -1 || response === '-1') {
                        console.error('Zendotech Product ATC Fatal: nonce/permission check failed (returned -1)');
                        btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                        if (Toast) Toast.show('Security check failed. Please reload the page and try again.', 'error');
                        setTimeout(() => {
                            btn.innerHTML = origHTML;
                            btn.style.pointerEvents = '';
                        }, 2000);
                        return;
                    }
                    if (response.success) {
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Added to Cart!';
                        if (Toast) Toast.show('Product added to cart!', 'success');

                        // Trigger WooCommerce cart fragment update
                        const fragments = response.data?.fragments;
                        const cartHash = response.data?.cart_hash;
                        jQuery(document.body).trigger('added_to_cart', [fragments, cartHash, jQuery(btn)]);

                        if (onSuccess) onSuccess();
                    } else {
                        btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                        if (Toast) Toast.show(response.data?.message || 'Could not add to cart', 'error');
                    }
                    if (!onSuccess) {
                        setTimeout(() => {
                            btn.innerHTML = origHTML;
                            btn.style.pointerEvents = '';
                        }, 2000);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Zendotech Product ATC Fatal: AJAX error', jqXHR.status, textStatus, errorThrown, jqXHR.responseText);
                    btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                    const Toast = getToast();
                    if (Toast) Toast.show('Server error. See console for details.', 'error');
                    setTimeout(() => {
                        btn.innerHTML = origHTML;
                        btn.style.pointerEvents = '';
                    }, 2000);
                }
            });
        };

        if (addBtn) {
            addBtn.addEventListener('click', (e) => {
                // If it's a variable product, we also need to check variations, but for simple product:
                e.preventDefault();
                wcAddToCart(addBtn, null);
            });
        }

        if (buyBtn) {
            buyBtn.addEventListener('click', (e) => {
                e.preventDefault();
        
                const productId = getProductId();
                const quantity  = getQuantity();
                const Toast     = getToast();
        
                if (!productId) {
                    if (Toast) Toast.show('Product not found.', 'error');
                    return;
                }
        
                if (typeof jQuery === 'undefined' || typeof zendotechData === 'undefined') {
                    window.location.href = (typeof zendotechData !== 'undefined' && zendotechData.checkoutUrl)
                        ? zendotechData.checkoutUrl : '/checkout/';
                    return;
                }
        
                const origHTML = buyBtn.innerHTML;
                buyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Please wait...';
                buyBtn.style.pointerEvents = 'none';
        
                let dataPayload = {
                    action:     'zendotech_add_to_cart',
                    product_id: productId,
                    quantity:   quantity,
                    nonce:      zendotechData.nonce
                };
        
                // Include variation data for variable products
                const form = buyBtn.closest('form.woocommerce-cart-form');
                if (form) {
                    const formData = new FormData(form);
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'action' && key !== 'add-to-cart') {
                            dataPayload[key] = value;
                        }
                    }
                }
        
                jQuery.ajax({
                    url:     zendotechData.ajaxUrl,
                    type:    'POST',
                    data:    dataPayload,
                    success: function (response) {
                        if (response && response.success) {
                            // Added to cart — redirect straight to checkout
                            window.location.href = zendotechData.checkoutUrl || '/checkout/';
                        } else {
                            buyBtn.innerHTML = origHTML;
                            buyBtn.style.pointerEvents = '';
                            const msg = (response && response.data && response.data.message)
                                ? response.data.message : 'Could not process. Please try again.';
                            if (Toast) Toast.show(msg, 'error');
                        }
                    },
                    error: function () {
                        buyBtn.innerHTML = origHTML;
                        buyBtn.style.pointerEvents = '';
                        if (Toast) Toast.show('Connection error. Please try again.', 'error');
                    }
                });
            });
        }
    }

    /* -------------------------------------------
       7. PRODUCT WISHLIST TOGGLE
       ------------------------------------------- */
    function initProductWishlist() {
        const wishBtn = document.querySelector('.pi-share button[title="Wishlist"]');
        const WishlistStore = getWishlistStore();
        if (!wishBtn || !WishlistStore) return;

        const title = document.querySelector('.pi-title')?.textContent?.trim() || 'Product';
        const priceText = document.querySelector('.pi-price')?.textContent?.trim() || '$0';
        const price = parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;
        const image = document.getElementById('mainImage')?.src || '';
        const id = title.toLowerCase().replace(/\s+/g, '-');

        // Check initial state
        if (WishlistStore.has(id)) {
            const icon = wishBtn.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-heart';
                icon.style.color = 'var(--cta)';
            }
        }

        wishBtn.addEventListener('click', () => {
            const added = WishlistStore.toggle({ id, name: title, price, image });
            const icon = wishBtn.querySelector('i');
            if (icon) {
                icon.className = added ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
                icon.style.color = added ? 'var(--cta)' : '';
            }
        });
    }

    /* -------------------------------------------
       8. REVIEW HELPFUL VOTING
       ------------------------------------------- */
    function initReviewVoting() {
        document.querySelectorAll('.rc-helpful button').forEach(btn => {
            btn.addEventListener('click', () => {
                const isUp = btn.querySelector('.fa-thumbs-up');
                const currentCount = parseInt(btn.textContent.replace(/\D/g, '')) || 0;

                if (btn.classList.contains('voted')) {
                    btn.classList.remove('voted');
                    btn.innerHTML = isUp
                        ? `<i class="fa-regular fa-thumbs-up"></i> ${currentCount - 1}`
                        : `<i class="fa-regular fa-thumbs-down"></i> ${currentCount - 1}`;
                } else {
                    // Remove vote from sibling button
                    const sibling = btn.parentElement.querySelector(btn === btn.parentElement.children[1] ? 'button:nth-child(3)' : 'button:nth-child(2)');
                    if (sibling?.classList.contains('voted')) {
                        const sibCount = parseInt(sibling.textContent.replace(/\D/g, '')) || 0;
                        sibling.classList.remove('voted');
                        const sibIcon = sibling.querySelector('i');
                        if (sibIcon) sibIcon.className = sibIcon.className.replace('fa-solid', 'fa-regular');
                        sibling.innerHTML = sibling.querySelector('.fa-thumbs-up')
                            ? `<i class="fa-regular fa-thumbs-up"></i> ${sibCount - 1}`
                            : `<i class="fa-regular fa-thumbs-down"></i> ${sibCount - 1}`;
                    }

                    btn.classList.add('voted');
                    const icon = btn.querySelector('i');
                    btn.innerHTML = isUp
                        ? `<i class="fa-solid fa-thumbs-up"></i> ${currentCount + 1}`
                        : `<i class="fa-solid fa-thumbs-down"></i> ${currentCount + 1}`;
                    btn.style.color = isUp ? 'var(--primary)' : 'var(--cta)';
                }
            });
        });
    }

    /* -------------------------------------------
       9. SHARE BUTTON
       ------------------------------------------- */
    function initShare() {
        const shareBtn = document.querySelector('.pi-share button[title="Share"]');
        if (!shareBtn) return;

        shareBtn.addEventListener('click', async () => {
            const title = document.querySelector('.pi-title')?.textContent?.trim() || 'Product';
            const Toast = getToast();

            if (navigator.share) {
                try {
                    await navigator.share({ title, url: window.location.href });
                } catch { /* user cancelled */ }
            } else {
                // Fallback: copy URL
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    if (Toast) Toast.show('Link copied to clipboard!', 'success');
                } catch {
                    if (Toast) Toast.show('Could not copy link', 'error');
                }
            }
        });
    }

    /* -------------------------------------------
       INITIALIZATION
       ------------------------------------------- */
    function init() {
        initGallery();
        initQuantity();
        initSwatches();
        initVariants();
        initTabs();
        initProductCart();
        initProductWishlist();
        initReviewVoting();
        initShare();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
