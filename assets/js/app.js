/* ============================================
   ZENDOTECH AUDIO — SHARED APP MODULE
   Cart, Wishlist, Toast, Mobile Nav, Search,
   Newsletter, Countdown, Back-to-Top
   ============================================ */

(function () {
    'use strict';

    /* -------------------------------------------
       1. CART STORE (localStorage)
       ------------------------------------------- */
    const CartStore = {
        KEY: 'zendotech_cart',

        getAll() {
            try {
                return JSON.parse(localStorage.getItem(this.KEY)) || [];
            } catch { return []; }
        },

        save(items) {
            localStorage.setItem(this.KEY, JSON.stringify(items));
            this.updateUI();
        },

        async add(product) {
            const formData = new FormData();
            formData.append('action', 'zendotech_add_to_cart');
            formData.append('product_id', product.id);
            formData.append('quantity', product.qty || 1);
            formData.append('nonce', zendotechData.nonce);

            try {
                const response = await fetch(zendotechData.ajaxUrl, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const txt = await response.text().catch(() => '');
                    console.error('Zendotech Add to Cart HTTP Error:', response.status, txt);
                    Toast.show('Server error adding to cart. See console for details.', 'error');
                    return;
                }

                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    const txt = await response.text().catch(() => '');
                    console.error('Zendotech Add to Cart: Non-JSON response', txt);
                    Toast.show('Server error adding to cart. See console for details.', 'error');
                    return;
                }

                if (data.success) {
                    const items = this.getAll();
                    const existing = items.find(i =>
                        i.id === product.id &&
                        i.color === (product.color || '') &&
                        i.variant === (product.variant || '')
                    );
                    if (existing) {
                        existing.qty = Math.min(existing.qty + (product.qty || 1), 10);
                    } else {
                        items.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            image: product.image || '',
                            color: product.color || '',
                            variant: product.variant || '',
                            qty: product.qty || 1
                        });
                    }
                    this.save(items);

                    // Trigger WooCommerce cart fragments update if jQuery is available
                    if (typeof jQuery !== 'undefined' && data.data && data.data.fragments) {
                        jQuery(document.body).trigger('added_to_cart', [data.data.fragments, data.data.cart_hash]);
                    }

                    Toast.show(`"${product.name}" added to cart`, 'success');
                } else {
                    console.error('Zendotech Add to Cart returned error:', data);
                    Toast.show((data.data && data.data.message) || 'Error adding to cart', 'error');
                }
            } catch (error) {
                console.error('Zendotech Add to Cart Error:', error);
                Toast.show('Connection error. Try again.', 'error');
            }
        },

        remove(id) {
            const items = this.getAll().filter(i => i.id !== id);
            this.save(items);
        },

        updateQty(id, qty) {
            const items = this.getAll();
            const item = items.find(i => i.id === id);
            if (item) {
                item.qty = Math.max(1, Math.min(qty, 10));
                this.save(items);
            }
        },

        clear() {
            this.save([]);
        },

        getCount() {
            return this.getAll().reduce((sum, i) => sum + i.qty, 0);
        },

        getTotal() {
            return this.getAll().reduce((sum, i) => sum + i.price * i.qty, 0);
        },

        async sync() {
            if (typeof zendotechData === 'undefined' || !zendotechData.ajaxUrl) return;
            try {
                const form = new FormData();
                form.append('action', 'zendotech_get_cart');
                form.append('nonce', zendotechData.nonce);
                const resp = await fetch(zendotechData.ajaxUrl, { method: 'POST', body: form });

                if (!resp.ok) {
                    const txt = await resp.text().catch(() => '');
                    console.error('Zendotech Cart sync HTTP error:', resp.status, txt);
                    return;
                }

                let data;
                try { data = await resp.json(); } catch (e) {
                    const txt = await resp.text().catch(() => '');
                    console.error('Zendotech Cart sync: Non-JSON response', txt);
                    return;
                }

                if (data.success && data.data && Array.isArray(data.data.items)) {
                    const items = data.data.items.map(i => ({
                        id: String(i.id),
                        name: i.name,
                        price: parseFloat(i.price) || 0,
                        image: i.image || '',
                        color: (i.color || ''),
                        variant: (i.variant || ''),
                        qty: parseInt(i.qty, 10) || 1
                    }));
                    this.save(items);
                }
            } catch (err) {
                console.error('Zendotech Cart sync failed:', err);
            }
        },

        updateUI() {
            // Update token/local badge counts just for visual immediacy
            document.querySelectorAll('.cart-action .badge-count:not(.cart-count-fragment)').forEach(el => {
                el.textContent = this.getCount();
            });
            // Update cart label price if it's not the WP fragment one
            document.querySelectorAll('.cart-label:not(.cart-total-fragment)').forEach(el => {
                el.innerHTML = '<bdi><span class="woocommerce-Price-currencySymbol">KSh</span>&nbsp;' + this.getTotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</bdi>';
            });

            // Note: We no longer manually render the mini cart HTML.
            // WooCommerce's 'added_to_cart' and 'removed_from_cart' events will handle updating the
            // .mini-cart-content-fragment via native fragments mechanism.
        }
    };

    /* -------------------------------------------
       2. WISHLIST STORE (localStorage)
       ------------------------------------------- */
    const WishlistStore = {
        KEY: 'zendotech_wishlist',

        getAll() {
            try {
                return JSON.parse(localStorage.getItem(this.KEY)) || [];
            } catch { return []; }
        },

        save(items) {
            localStorage.setItem(this.KEY, JSON.stringify(items));
            this.updateUI();
        },

        toggle(product) {
            const items = this.getAll();
            const idx = items.findIndex(i => i.id === product.id);
            if (idx > -1) {
                items.splice(idx, 1);
                Toast.show(`"${product.name}" removed from wishlist`, 'info');
            } else {
                items.push({ id: product.id, name: product.name, price: product.price, image: product.image || '' });
                Toast.show(`"${product.name}" added to wishlist`, 'success');
            }
            this.save(items);
            return idx === -1; // true = added
        },

        has(id) {
            return this.getAll().some(i => i.id === id);
        },

        getCount() {
            return this.getAll().length;
        },

        updateUI() {
            const count = this.getCount();
            document.querySelectorAll('.h-action[title="Wishlist"] .badge-count, #mobileWishlistBtn .badge-count').forEach(el => {
                el.textContent = count;
            });
        }
    };

    /* -------------------------------------------
       3. COMPARE STORE (localStorage)
       ------------------------------------------- */
    const CompareStore = {
        KEY: 'zendotech_compare',
        MAX: 4,

        getAll() {
            try {
                return JSON.parse(localStorage.getItem(this.KEY)) || [];
            } catch { return []; }
        },

        save(items) {
            localStorage.setItem(this.KEY, JSON.stringify(items));
            this.updateUI();
            renderCompareBar();
        },

        toggle(product) {
            const items = this.getAll();
            const idx = items.findIndex(i => i.id === product.id);
            if (idx > -1) {
                items.splice(idx, 1);
                Toast.show(`"${product.name}" removed from compare`, 'info');
                this.save(items);
                return false;
            } else {
                if (items.length >= this.MAX) {
                    Toast.show(`Compare list is full (max ${this.MAX} products)`, 'warning');
                    return false;
                }
                items.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image || '',
                    category: product.category || '',
                    rating: product.rating || '',
                    oldPrice: product.oldPrice || ''
                });
                Toast.show(`"${product.name}" added to compare`, 'success');
                this.save(items);
                return true;
            }
        },

        has(id) {
            return this.getAll().some(i => i.id === id);
        },

        remove(id) {
            const items = this.getAll().filter(i => i.id !== id);
            this.save(items);
        },

        clear() {
            this.save([]);
        },

        getCount() {
            return this.getAll().length;
        },

        updateUI() {
            const count = this.getCount();
            document.querySelectorAll('.h-action[title="Compare"] .badge-count, #mobileCompareBtn .badge-count').forEach(el => {
                el.textContent = count;
            });
        }
    };

    /* -------------------------------------------
       4. TOAST NOTIFICATIONS
       ------------------------------------------- */
    const Toast = {
        container: null,

        init() {
            if (document.getElementById('toast-container')) {
                this.container = document.getElementById('toast-container');
                return;
            }
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            document.body.appendChild(this.container);
        },

        show(message, type = 'success', duration = 3000) {
            if (!this.container) this.init();

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                info: 'fa-circle-info',
                warning: 'fa-triangle-exclamation'
            };

            toast.innerHTML = `
                <i class="fa-solid ${icons[type] || icons.success}"></i>
                <span>${message}</span>
                <button class="toast-close"><i class="fa-solid fa-xmark"></i></button>
            `;

            this.container.appendChild(toast);

            // Trigger reflow for animation
            requestAnimationFrame(() => toast.classList.add('show'));

            const closeBtn = toast.querySelector('.toast-close');
            const dismiss = () => {
                toast.classList.remove('show');
                toast.addEventListener('transitionend', () => toast.remove(), { once: true });
            };

            closeBtn.addEventListener('click', dismiss);
            setTimeout(dismiss, duration);
        }
    };

    /* -------------------------------------------
       4. PREMIUM MINI-CART DROPDOWN
       ------------------------------------------- */
    function initMiniCart() {
        const cartAction = document.querySelector('.cart-action');
        const panel = document.getElementById('miniCartPanel');
        const wrapper = document.querySelector('.mini-cart-wrapper');

        if (!cartAction || !panel || !wrapper) return;

        // Show mini-cart on hover
        let hoverTimeout;
        wrapper.addEventListener('mouseenter', () => {
            clearTimeout(hoverTimeout);
            panel.classList.add('open');
        });

        wrapper.addEventListener('mouseleave', () => {
            hoverTimeout = setTimeout(() => {
                panel.classList.remove('open');
            }, 300);
        });

        // Close button
        panel.querySelector('.mc-close-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.remove('open');
        });

        // Delegate remove clicks inside the mini-cart panel
        panel.addEventListener('click', (e) => {
            e.stopPropagation();

            // Remove button — rendered by PHP with href pointing to WC remove URL
            const removeBtn = e.target.closest('.mc-item-remove');
            if (removeBtn) {
                e.preventDefault();
                const removeUrl = removeBtn.getAttribute('href');
                if (removeUrl && typeof jQuery !== 'undefined') {
                    // Use AJAX to hit the WC remove URL, then refresh cart fragments
                    jQuery.ajax({
                        url: removeUrl,
                        type: 'GET',
                        success: function () {
                            jQuery(document.body).trigger('wc_fragment_refresh');
                            Toast.show('Item removed from cart', 'info');
                        }
                    });
                } else if (removeUrl) {
                    window.location.href = removeUrl;
                }
                return;
            }
        });

        // Prevent scroll propagation inside mini-cart body
        const mcBody = panel.querySelector('.mc-body');
        if (mcBody) {
            mcBody.addEventListener('wheel', (e) => {
                const { scrollTop, scrollHeight, clientHeight } = mcBody;
                if (
                    (e.deltaY < 0 && scrollTop === 0) ||
                    (e.deltaY > 0 && scrollTop + clientHeight >= scrollHeight)
                ) {
                    e.preventDefault();
                }
            }, { passive: false });
        }
        
        // Shipping checkbox logic
        const shippingCheck = document.getElementById('mcShippingCheck');
        if (shippingCheck) {
            shippingCheck.addEventListener('change', (e) => {
                if(e.target.checked) {
                    Toast.show('Free shipping applied!', 'success', 2000);
                } else {
                    Toast.show('Standard shipping rates will apply', 'info', 2000);
                }
            });
        }
    }

    /* -------------------------------------------
       4b. HEADER WISHLIST & COMPARE CLICKS
       ------------------------------------------- */
    function initHeaderActions() {
        const headerWishlistBtn = document.getElementById('headerWishlistBtn');
        if (headerWishlistBtn) {
            headerWishlistBtn.addEventListener('click', (e) => {
                e.preventDefault();
                // For now, just show a toast if no dedicated wishlist page
                const count = WishlistStore.getCount();
                if(count > 0) {
                     Toast.show(`You have ${count} item(s) in your wishlist. Go to My Account to view.`, 'info');
                     // If you have a wishlist page, you would redirect here:
                     // window.location.href = '/wishlist';
                } else {
                     Toast.show('Your wishlist is empty.', 'info');
                }
            });
        }

        const headerCompareBtn = document.getElementById('headerCompareBtn');
        if (headerCompareBtn) {
            headerCompareBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (CompareStore.getCount() >= 2) {
                    openCompareModal();
                } else {
                    Toast.show('Add at least 2 products to compare', 'warning');
                }
            });
        }
    }

    /* -------------------------------------------
       5. MOBILE DRAWER NAVIGATION
       ------------------------------------------- */
    function initMobileNav() {
        const openBtn = document.getElementById('mobileMenuOpen');
        const closeBtn = document.getElementById('mobileMenuClose');
        const drawer = document.getElementById('mobileDrawer');
        const overlay = document.getElementById('drawerOverlay');

        if (!openBtn || !drawer) return;

        const openDrawer = () => {
            drawer.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeDrawer = () => {
            drawer.classList.remove('active');
            document.body.style.overflow = '';
        };

        openBtn.addEventListener('click', openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (overlay) overlay.addEventListener('click', closeDrawer);

        // Connect Bottom Nav Categories Button
        const bottomCatBtn = document.getElementById('mobileMenuOpenBottom');
        if (bottomCatBtn) {
            bottomCatBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openDrawer();
            });
        }
    }

    /* -------------------------------------------
       6. STICKY HEADER
       ------------------------------------------- */
    function initStickyHeader() {
        const header = document.querySelector('.main-header.is-sticky-enabled');
        if (!header) return;

        let lastScroll = 0;
        const threshold = 200;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > threshold) {
                document.body.classList.add('is-header-sticky');
            } else {
                document.body.classList.remove('is-header-sticky');
            }

            lastScroll = currentScroll;
        }, { passive: true });
    }

    /* -------------------------------------------
       7. CATEGORY DROPDOWN TOGGLE
       ------------------------------------------- */
    /* -------------------------------------------
       7. CATEGORY DROPDOWN (Header & Sidebar)
       ------------------------------------------- */
    function initCategoryDropdown() {
        // Header Category Dropdown
        const headerCatTrigger = document.querySelector('.header-cat-trigger');
        if (headerCatTrigger) {
            headerCatTrigger.addEventListener('click', (e) => {
                // If clicking the dropdown itself (e.g. submenus), don't close
                if (e.target.closest('.header-cat-dropdown')) return;

                headerCatTrigger.classList.toggle('active');
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!headerCatTrigger.contains(e.target)) {
                    headerCatTrigger.classList.remove('active');
                }
            });

            // Toggle sub-menus on mobile/tablet
            const hasChildren = headerCatTrigger.querySelectorAll('.has-children > a');
            hasChildren.forEach(link => {
                link.addEventListener('click', (e) => {
                    if (window.innerWidth <= 991) {
                        e.preventDefault();
                        const li = link.parentElement;
                        li.classList.toggle('active');
                    }
                });
            });

            // View More Categories toggle in Header
            const viewMoreHeaderBtn = headerCatTrigger.querySelector('.view-more-cats a');
            if (viewMoreHeaderBtn) {
                viewMoreHeaderBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const dropdown = viewMoreHeaderBtn.closest('.header-cat-dropdown');
                    if (dropdown) {
                        dropdown.classList.add('show-all');
                    }
                });
            }
        }

        // Homepage Hero Sidebar Trigger
        const heroTrigger = document.querySelector('.categories-trigger');
        const heroSidebar = document.querySelector('.hero-sidebar');
        if (heroTrigger) {
            heroTrigger.addEventListener('click', () => {
                heroTrigger.classList.toggle('active');
                if (heroSidebar) heroSidebar.classList.toggle('open');
            });
        }
    }

    function initHomeSidebarLimit() {
        const sidebarMenu = document.querySelector('.hero-sidebar .sidebar-menu');
        if (!sidebarMenu) return;

        const items = sidebarMenu.querySelectorAll('li:not(.more-link):not(.view-more-cats-sidebar):not(.all-cats-link)');
        if (items.length > 9) {
            items.forEach((item, index) => {
                if (index >= 9) {
                    item.classList.add('extra-cat');
                }
            });
        }
    }

    /* -------------------------------------------
       7. SEARCH BAR
       ------------------------------------------- */
    function initSearchBar() {
        // Category dropdown in search bar — populated from WooCommerce via wp_localize_script
        const searchCat = document.querySelector('.search-category');
        if (searchCat) {
            // Build categories from dynamic data or fallback
            let categories = ['All Categories'];
            if (typeof zendotechData !== 'undefined' && zendotechData.categories && zendotechData.categories.length) {
                zendotechData.categories.forEach(c => categories.push(c.name));
            } else {
                categories.push('Headphones', 'Speakers', 'Earbuds', 'Turntables', 'Guitars', 'Studio Gear', 'Accessories');
            }

            const dropdown = document.createElement('ul');
            dropdown.className = 'search-cat-dropdown';
            categories.forEach(cat => {
                const li = document.createElement('li');
                li.textContent = cat;
                li.addEventListener('click', (e) => {
                    e.stopPropagation();
                    searchCat.querySelector('span').textContent = cat;
                    dropdown.classList.remove('open');

                    // Set hidden category input on the search form
                    const form = searchCat.closest('form');
                    if (form) {
                        let catInput = form.querySelector('input[name="product_cat"]');
                        if (!catInput) {
                            catInput = document.createElement('input');
                            catInput.type = 'hidden';
                            catInput.name = 'product_cat';
                            form.appendChild(catInput);
                        }
                        if (cat === 'All Categories') {
                            catInput.value = '';
                        } else {
                            // Find slug from dynamic data
                            const found = (typeof zendotechData !== 'undefined' && zendotechData.categories)
                                ? zendotechData.categories.find(c => c.name === cat)
                                : null;
                            catInput.value = found ? found.slug : cat.toLowerCase().replace(/\s+/g, '-');
                        }
                    }
                });
                dropdown.appendChild(li);
            });
            searchCat.appendChild(dropdown);

            searchCat.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', () => dropdown.classList.remove('open'));
        }

        // AJAX Autocomplete Logic for Desktop and Mobile Search Bars
        const searchForms = document.querySelectorAll('form.search-bar, .drawer-search form');
        
        searchForms.forEach(form => {
            const input = form.querySelector('input[name="s"]');
            if (!input) return;

            // Create dropdown container for results
            const resultsContainer = document.createElement('div');
            resultsContainer.className = 'ajax-search-results';
            // Styling logic handled by CSS, but ensure positioning context
            form.style.position = 'relative';
            form.appendChild(resultsContainer);

            let debounceTimer;

            input.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                const catInput = form.querySelector('input[name="product_cat"]');
                const catInfo = catInput ? catInput.value : '';

                clearTimeout(debounceTimer);

                if (query.length < 2) {
                    resultsContainer.classList.remove('active');
                    resultsContainer.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    resultsContainer.innerHTML = '<div class="asr-loading"><i class="fa-solid fa-spinner fa-spin"></i> Searching...</div>';
                    resultsContainer.classList.add('active');

                    if (typeof jQuery !== 'undefined' && typeof zendotechData !== 'undefined') {
                        jQuery.ajax({
                            url: zendotechData.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'zendotech_ajax_search',
                                keyword: query,
                                category: catInfo
                            },
                            success: function(response) {
                                if (response.success && response.data.html) {
                                    resultsContainer.innerHTML = response.data.html;
                                } else {
                                    resultsContainer.innerHTML = '<div class="asr-no-results">No products found.</div>';
                                }
                            },
                            error: function() {
                                resultsContainer.innerHTML = '<div class="asr-error">Error fetching results.</div>';
                            }
                        });
                    }
                }, 400); // 400ms debounce
            });

            // Hide results when clicking outside
            document.addEventListener('click', (e) => {
                if (!form.contains(e.target)) {
                    resultsContainer.classList.remove('active');
                }
            });

            // Show results again when clicking input if it has value
            input.addEventListener('focus', () => {
                if (input.value.trim().length >= 2 && resultsContainer.innerHTML !== '') {
                    resultsContainer.classList.add('active');
                }
            });
        });

        // Mobile Search Toggle
        const searchToggle = document.getElementById('mobileSearchToggle');
        const searchBarWrap = document.getElementById('headerSearchBar');
        if (searchToggle && searchBarWrap) {
            searchToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                searchBarWrap.classList.toggle('active');
                if (searchBarWrap.classList.contains('active')) {
                    searchBarWrap.querySelector('input')?.focus();
                }
            });

            // Close search when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchBarWrap.contains(e.target) && !searchToggle.contains(e.target)) {
                    searchBarWrap.classList.remove('active');
                }
            });
        }
    }

    /* -------------------------------------------
       8. NEWSLETTER FORM
       ------------------------------------------- */
    function initNewsletter() {
        const forms = document.querySelectorAll('.fn-form, .ns-inner form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = form.querySelector('input[type="email"]');
                const email = input ? input.value.trim() : '';

                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    Toast.show('Please enter a valid email address', 'error');
                    return;
                }

                Toast.show('Thanks for subscribing! Check your inbox for 15% off 🎵', 'success', 4000);
                if (input) input.value = '';
            });
        });
    }

    /* -------------------------------------------
       9. BACK-TO-TOP BUTTON
       ------------------------------------------- */
    function initBackToTop() {
        const btn = document.createElement('button');
        btn.className = 'back-to-top';
        btn.setAttribute('aria-label', 'Back to top');
        btn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
        document.body.appendChild(btn);

        const toggle = () => {
            btn.classList.toggle('visible', window.scrollY > 400);
        };

        window.addEventListener('scroll', toggle, { passive: true });
        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* -------------------------------------------
       10. COUNTDOWN TIMER
       ------------------------------------------- */
    function initCountdown() {
        const timerContainers = document.querySelectorAll('.fsb-timer');
        if (!timerContainers.length) return;

        timerContainers.forEach(container => {
            const boxes = container.querySelectorAll('.cd-box span');
            if (boxes.length < 3) return;

            // Parse initial time from HTML
            let hours = parseInt(boxes[0].textContent) || 0;
            let minutes = parseInt(boxes[1].textContent) || 0;
            let seconds = parseInt(boxes[2].textContent) || 0;
            let totalSeconds = hours * 3600 + minutes * 60 + seconds;

            const update = () => {
                if (totalSeconds <= 0) {
                    clearInterval(interval);
                    boxes[0].textContent = '00';
                    boxes[1].textContent = '00';
                    boxes[2].textContent = '00';
                    return;
                }
                totalSeconds--;
                const h = Math.floor(totalSeconds / 3600);
                const m = Math.floor((totalSeconds % 3600) / 60);
                const s = totalSeconds % 60;
                boxes[0].textContent = String(h).padStart(2, '0');
                boxes[1].textContent = String(m).padStart(2, '0');
                boxes[2].textContent = String(s).padStart(2, '0');
            };

            const interval = setInterval(update, 1000);
        });
    }

    /* -------------------------------------------
       11. ADD TO CART — WOOCOMMERCE NATIVE AJAX
       WC's add-to-cart.js handles the server-side AJAX.
       We only provide visual feedback + optional mini-cart update.
       ------------------------------------------- */
    function initAddToCartButtons() {
        // --- A. Let WooCommerce handle .ajax_add_to_cart buttons natively ---
        // Do NOT intercept these — WC's add-to-cart.js POSTs to /?wc-ajax=add_to_cart
        // Just add a visual animation when WC fires its jQuery event
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('adding_to_cart', function (e, $btn) {
                if ($btn && $btn.length) {
                    $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Adding...');
                    $btn.css('pointer-events', 'none');
                }
            });

            jQuery(document.body).on('added_to_cart', function (e, fragments, cart_hash, $btn) {
                // Button success animation
                if ($btn && $btn.length) {
                    $btn.html('<i class="fa-solid fa-check"></i> Added!');
                    setTimeout(function () {
                        $btn.html('<i class="fa-solid fa-cart-shopping"></i> Add to Cart');
                        $btn.css('pointer-events', '');
                    }, 1500);
                }

                // Update header cart count, total, and mini-cart from fragments
                if (fragments && typeof fragments === 'object') {
                    Object.keys(fragments).forEach(function (key) {
                        var content = fragments[key];
                        if (content === undefined || content === null) return;

                        var els = document.querySelectorAll(key);
                        els.forEach(function (el) {
                            // Container fragments: update inner content only so the wrapper (and its class) stays in DOM
                            if (key === '.mini-cart-content-fragment' || key === '.mc-subtotal-val-fragment') {
                                el.innerHTML = content;
                                return;
                            }
                            // Plain text fragment (e.g. .mc-count-fragment)
                            if (!content.trim().startsWith('<')) {
                                el.textContent = content;
                                return;
                            }
                            var temp = document.createElement('div');
                            temp.innerHTML = content;
                            if (temp.children.length === 1) {
                                if (el.parentNode) el.outerHTML = content;
                            } else {
                                el.innerHTML = content;
                            }
                        });
                    });
                }

                // Show toast
                Toast.show('Product added to cart!', 'success');
            });
        }

        // --- B. Handle all add-to-cart link clicks via AJAX — run in CAPTURE phase so we run first and always prevent redirect ---
        document.addEventListener('click', function(e) {
            const atcBtn = e.target.closest('.atc-btn, .add_to_cart_button');
            if (!atcBtn) return;

            // Prevent navigation immediately (capture phase = we run before other scripts)
            e.preventDefault();
            e.stopImmediatePropagation();

            const card = atcBtn.closest('.product-card');
            let productId = atcBtn.dataset.product_id || atcBtn.dataset.productId || atcBtn.getAttribute('data-product_id') || atcBtn.getAttribute('data-product-id');
            if (!productId && card) productId = card.dataset.productId || card.getAttribute('data-product-id');
            if (!productId) return;

            // Button animation
            const origHTML = atcBtn.innerHTML;
            atcBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            atcBtn.style.pointerEvents = 'none';

            // Trigger Add to Cart via AJAX and update header cart from response fragments
            if (typeof jQuery === 'undefined' || typeof zendotechData === 'undefined') {
                atcBtn.innerHTML = origHTML;
                atcBtn.style.pointerEvents = '';
                if (typeof Toast !== 'undefined') Toast.show('Unable to add to cart. Please refresh the page.', 'error');
                return;
            }

            var payload = {
                action: 'zendotech_add_to_cart',
                product_id: String(productId).trim(),
                quantity: 1,
                nonce: zendotechData.nonce || ''
            };

            jQuery.ajax({
                url: zendotechData.ajaxUrl,
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(response) {
                    if (response && response.success && response.data) {
                        atcBtn.innerHTML = '<i class="fa-solid fa-check"></i> Added!';
                        var fragments = response.data.fragments;
                        var cartHash = response.data.cart_hash;
                        jQuery(document.body).trigger('added_to_cart', [fragments || {}, cartHash, jQuery(atcBtn)]);
                    } else {
                        atcBtn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                        var msg = (response && response.data && response.data.message) ? response.data.message : 'Could not add to cart.';
                        if (typeof Toast !== 'undefined') Toast.show(msg, 'error');
                    }
                    setTimeout(function() {
                        atcBtn.innerHTML = origHTML;
                        atcBtn.style.pointerEvents = '';
                    }, 2000);
                },
                error: function(xhr, status, err) {
                    atcBtn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                    var msg = 'Could not add to cart. Please refresh the page and try again.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        msg = xhr.responseJSON.data.message;
                    } else if (xhr && xhr.status === 403) {
                        msg = 'Session expired. Please refresh the page and try again.';
                    } else if (xhr && xhr.responseText) {
                        try {
                            var parsed = JSON.parse(xhr.responseText);
                            if (parsed && parsed.data && parsed.data.message) msg = parsed.data.message;
                        } catch (e) {}
                    }
                    if (typeof Toast !== 'undefined') Toast.show(msg, 'error');
                    setTimeout(function() {
                        atcBtn.innerHTML = origHTML;
                        atcBtn.style.pointerEvents = '';
                    }, 2000);
                }
            });
        }, true);
    }

    /* -------------------------------------------
       12. WISHLIST — PRODUCT CARD OVERLAY HEARTS
       ------------------------------------------- */
    function initWishlistButtons() {
        document.addEventListener('click', (e) => {
            const heartBtn = e.target.closest('.pc-overlay button:first-child');
            if (!heartBtn) return;

            const card = heartBtn.closest('.product-card');
            if (!card) return;

            const name = card.querySelector('h4 a')?.textContent?.trim() || 'Product';
            const priceText = card.querySelector('.new-price')?.textContent?.trim() || '$0';
            const price = parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;
            const image = card.querySelector('.pc-img img')?.src || '';
            const id = name.toLowerCase().replace(/\s+/g, '-');

            const added = WishlistStore.toggle({ id, name, price, image });
            const icon = heartBtn.querySelector('i');
            if (icon) {
                icon.className = added ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
                icon.style.color = added ? 'var(--cta)' : '';
            }
        });
    }

    /* -------------------------------------------
       13. COMPARE BUTTON — OVERLAY 
       ------------------------------------------- */
    function initCompareButtons() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.pc-overlay button');
            if (!btn) return;

            // Robust check for compare button (title, icon, or position)
            const isCompare = btn.title === 'Compare' ||
                btn.querySelector('.fa-arrow-right-arrow-left') ||
                btn.matches(':last-child');

            if (!isCompare) return;

            const compareBtn = btn;
            const card = compareBtn.closest('.product-card');
            if (!card) return;

            const name = card.querySelector('h4 a')?.textContent?.trim() || 'Product';
            const priceText = card.querySelector('.new-price')?.textContent?.trim() || '$0';
            const price = parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;
            const oldPriceText = card.querySelector('.old-price')?.textContent?.trim() || '';
            const image = card.querySelector('.pc-img img')?.src || '';
            const category = card.querySelector('.pc-cat')?.textContent?.trim() || '';
            const ratingEl = card.querySelector('.pc-stars span');
            const rating = ratingEl ? ratingEl.textContent.trim() : '';
            const id = name.toLowerCase().replace(/\s+/g, '-');
            const wcId = card.dataset.productId || ''; // Real WooCommerce product ID

            const added = CompareStore.toggle({ id, wcId, name, price, image, category, rating, oldPrice: oldPriceText });
            const icon = compareBtn.querySelector('i');
            if (icon) {
                icon.style.color = added ? 'var(--cta)' : '';
            }
        });
    }

    /* -------------------------------------------
       COMPARE BAR (floating bottom bar)
       ------------------------------------------- */
    let compareBarEl = null;

    function initCompareBar() {
        compareBarEl = document.createElement('div');
        compareBarEl.className = 'compare-bar';
        compareBarEl.id = 'compareBar';
        document.body.appendChild(compareBarEl);
        renderCompareBar();
    }

    function renderCompareBar() {
        if (!compareBarEl) return;
        const items = CompareStore.getAll();

        if (items.length === 0) {
            compareBarEl.classList.remove('visible');
            return;
        }

        compareBarEl.classList.add('visible');

        const emptySlots = CompareStore.MAX - items.length;
        let slotsHTML = items.map(item => `
            <div class="cb-slot filled">
                <img src="${item.image || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22%3E%3Crect width=%2260%22 height=%2260%22 fill=%22%23eee%22/%3E%3C/svg%3E'}" alt="${item.name}">
                <span class="cb-slot-name">${item.name}</span>
                <button class="cb-slot-remove" data-id="${item.id}" title="Remove">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');

        for (let i = 0; i < emptySlots; i++) {
            slotsHTML += `<div class="cb-slot empty"><i class="fa-solid fa-plus"></i><span>Add Product</span></div>`;
        }

        compareBarEl.innerHTML = `
            <div class="container cb-inner">
                <div class="cb-slots">${slotsHTML}</div>
                <div class="cb-actions">
                    <button class="cb-compare-btn" ${items.length < 2 ? 'disabled' : ''}>
                        <i class="fa-solid fa-code-compare"></i> Compare Now (${items.length})
                    </button>
                    <button class="cb-clear-btn"><i class="fa-solid fa-trash-can"></i> Clear All</button>
                </div>
            </div>
        `;

        // Events: remove slot
        compareBarEl.querySelectorAll('.cb-slot-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                CompareStore.remove(btn.dataset.id);
            });
        });

        // Events: clear all
        const clearBtn = compareBarEl.querySelector('.cb-clear-btn');
        if (clearBtn) clearBtn.addEventListener('click', () => CompareStore.clear());

        // Events: compare now
        const compareNowBtn = compareBarEl.querySelector('.cb-compare-btn');
        if (compareNowBtn) compareNowBtn.addEventListener('click', () => openCompareModal());
    }

    /* -------------------------------------------
       COMPARE MODAL (side-by-side view)
       ------------------------------------------- */
    function openCompareModal() {
        const items = CompareStore.getAll();
        if (items.length < 2) {
            Toast.show('Add at least 2 products to compare', 'warning');
            return;
        }

        // Remove existing modal if any
        const existing = document.getElementById('compareModal');
        if (existing) existing.remove();

        const modal = document.createElement('div');
        modal.id = 'compareModal';
        modal.className = 'compare-modal';

        const colWidth = 100 / items.length;

        modal.innerHTML = `
            <div class="cm-overlay"></div>
            <div class="cm-content">
                <div class="cm-header">
                    <h3><i class="fa-solid fa-code-compare"></i> Compare Products (${items.length})</h3>
                    <button class="cm-close" title="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="cm-body">
                    <table class="cm-table">
                        <thead>
                            <tr>
                                ${items.map(item => `
                                    <th style="width:${colWidth}%">
                                        <button class="cm-remove" data-id="${item.id}" title="Remove">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <div class="cm-product-img">
                                            <img src="${item.image || ''}" alt="${item.name}">
                                        </div>
                                        <h4 class="cm-product-name">${item.name}</h4>
                                    </th>
                                `).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cm-row">
                                <td colspan="${items.length}" class="cm-row-label">Price</td>
                            </tr>
                            <tr>
                                ${items.map(item => `
                                    <td>
                                        <span class="cm-price">KSh ${item.price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                        ${item.oldPrice ? `<span class="cm-old-price">${item.oldPrice}</span>` : ''}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr class="cm-row">
                                <td colspan="${items.length}" class="cm-row-label">Category</td>
                            </tr>
                            <tr>
                                ${items.map(item => `<td>${item.category || '—'}</td>`).join('')}
                            </tr>
                            <tr class="cm-row">
                                <td colspan="${items.length}" class="cm-row-label">Rating</td>
                            </tr>
                            <tr>
                                ${items.map(item => `<td>${item.rating || '—'}</td>`).join('')}
                            </tr>
                            <tr class="cm-row">
                                <td colspan="${items.length}" class="cm-row-label">Availability</td>
                            </tr>
                            <tr>
                                ${items.map(() => `<td><span class="cm-in-stock"><i class="fa-solid fa-circle-check"></i> In Stock</span></td>`).join('')}
                            </tr>
                            <tr class="cm-row">
                                <td colspan="${items.length}" class="cm-row-label">Actions</td>
                            </tr>
                            <tr>
                                ${items.map(item => `
                                    <td>
                                        <button class="cm-add-cart" data-id="${item.id}" data-wcid="${item.wcId || item.id}" data-name="${item.name}" data-price="${item.price}" data-image="${item.image || ''}">
                                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                                        </button>
                                    </td>
                                `).join('')}
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => modal.classList.add('open'));

        // Close
        const close = () => {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            setTimeout(() => modal.remove(), 300);
        };

        modal.querySelector('.cm-overlay').addEventListener('click', close);
        modal.querySelector('.cm-close').addEventListener('click', close);

        // Remove from compare inside modal
        modal.querySelectorAll('.cm-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                CompareStore.remove(btn.dataset.id);
                if (CompareStore.getCount() < 2) {
                    close();
                } else {
                    close();
                    setTimeout(() => openCompareModal(), 350);
                }
            });
        });

        // Add to cart from compare modal — use wcid (real WC product ID)
        modal.querySelectorAll('.cm-add-cart').forEach(btn => {
            btn.addEventListener('click', () => {
                const wcProductId = btn.dataset.wcid || btn.dataset.id;
                if (typeof jQuery !== 'undefined' && typeof zendotechData !== 'undefined' && zendotechData.ajaxUrl) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';
                    btn.disabled = true;
                    jQuery.ajax({
                        url: zendotechData.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'zendotech_add_to_cart',
                            product_id: wcProductId,
                            quantity: 1,
                            nonce: zendotechData.nonce
                        },
                        success: function (response) {
                            if (response.success) {
                                btn.innerHTML = '<i class="fa-solid fa-check"></i> Added!';
                                const fragments = response.data?.fragments;
                                const cartHash = response.data?.cart_hash;
                                jQuery(document.body).trigger('added_to_cart', [fragments, cartHash]);
                                Toast.show('Product added to cart!', 'success');
                            } else {
                                btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                                btn.disabled = false;
                                Toast.show(response.data?.message || 'Could not add to cart', 'error');
                            }
                        },
                        error: function () {
                            btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                            btn.disabled = false;
                            Toast.show('Connection error. Try again.', 'error');
                        }
                    });
                }
            });
        });
    }

    /* -------------------------------------------
       14. QUICK VIEW — AJAX MODAL
       ------------------------------------------- */
    function openQuickView(productId) {
        console.log('Zendotech: Opening Quick View for ID:', productId);
        let modal = document.getElementById('quickViewModal');

        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'quickViewModal';
            modal.className = 'qv-modal';
            modal.innerHTML = `
                <div class="qv-overlay"></div>
                <div class="qv-content">
                    <button class="qv-close"><i class="fa-solid fa-xmark"></i></button>
                    <div class="qv-body-loader">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            const close = () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            };

            modal.querySelector('.qv-overlay').addEventListener('click', close);
            modal.querySelector('.qv-close').addEventListener('click', close);
        }

        // Show modal & loader
        const content = modal.querySelector('.qv-content');
        const overlay = modal.querySelector('.qv-overlay');
        document.body.style.overflow = 'hidden';
        modal.classList.add('active');

        const qvBody = content.querySelector('.qv-body-loader') || document.createElement('div');
        if (!content.querySelector('.qv-body-loader')) {
            qvBody.className = 'qv-body-loader';
            qvBody.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            // Remove previous content except close button
            Array.from(content.children).forEach(child => {
                if (!child.classList.contains('qv-close')) child.remove();
            });
            content.appendChild(qvBody);
        }

        // Fetch Data via AJAX
        if (typeof zendotechData === 'undefined' || !zendotechData.ajaxUrl) {
            console.error('Zendotech: zendotechData is not defined or missing ajaxUrl');
            const qvBody = content.querySelector('.qv-body-loader');
            if (qvBody) qvBody.innerHTML = '<p>Error: System data missing.</p>';
            return;
        }

        if (typeof jQuery !== 'undefined') {
            jQuery.ajax({
                url: zendotechData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'zendotech_quick_view',
                    product_id: productId
                },
                success: function (response) {
                    console.log('Zendotech: AJAX Response received', response);
                    const loader = content.querySelector('.qv-body-loader');
                    if (response.success) {
                        if (loader) loader.remove();
                        const temp = document.createElement('div');
                        temp.innerHTML = response.data;
                        const grid = temp.firstElementChild;
                        if (grid) content.appendChild(grid);

                        // Init Modal Interactions
                        initQuickViewInteractions(grid);
                    } else {
                        if (loader) loader.innerHTML = '<p>Error loading product.</p>';
                    }
                },
                error: function (xhr, status, err) {
                    const loader = content.querySelector('.qv-body-loader');
                    if (loader) loader.innerHTML = '<p>Could not load product. Please try again.</p>';
                }
            });
        } else {
            const qvBodyErr = content.querySelector('.qv-body-loader');
            if (qvBodyErr) qvBodyErr.innerHTML = '<p>jQuery is required for Quick View.</p>';
        }
    }

    function initQuickViewInteractions(grid) {
        // Thumbnail Switching
        const mainImg = grid.querySelector('#qvMainImg');
        const thumbs = grid.querySelectorAll('.qv-thumb');

        const setMainImg = (url, thumb) => {
            mainImg.src = url;
            thumbs.forEach(t => t.classList.remove('active'));
            if (thumb) thumb.classList.add('active');
        };

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                setMainImg(thumb.dataset.url, thumb);
            });
        });

        // Next/Prev Gallery
        const prev = grid.querySelector('.qv-prev');
        const next = grid.querySelector('.qv-next');
        if (prev && next) {
            const urls = Array.from(thumbs).map(t => t.dataset.url);
            let idx = 0;

            const navigate = (dir) => {
                idx = (idx + dir + urls.length) % urls.length;
                setMainImg(urls[idx], thumbs[idx]);
            };

            prev.addEventListener('click', () => navigate(-1));
            next.addEventListener('click', () => navigate(1));
        }

        // Qty Controls
        const qtyInput = grid.querySelector('#qvQty');
        const plus = grid.querySelector('.qv-qty-plus');
        const minus = grid.querySelector('.qv-qty-minus');

        if (qtyInput && plus && minus) {
            plus.addEventListener('click', () => qtyInput.stepUp());
            minus.addEventListener('click', () => qtyInput.stepDown());
        }

        // ATC button logic
        const atc = grid.querySelector('.qv-add-btn');
        if (atc && qtyInput) {
            atc.addEventListener('click', () => {
                const productId = atc.dataset.productId || atc.dataset.product_id;
                if (!productId) return;

                atc.disabled = true;
                const origHTML = atc.innerHTML;
                atc.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';

                if (typeof jQuery !== 'undefined' && typeof zendotechData !== 'undefined') {
                    jQuery.ajax({
                        url: zendotechData.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'zendotech_add_to_cart',
                            product_id: productId,
                            quantity: parseInt(qtyInput.value) || 1,
                            nonce: zendotechData.nonce
                        },
                        success: function(response) {
                            atc.disabled = false;
                            if (response && response.success && response.data) {
                                atc.innerHTML = '<i class="fa-solid fa-check"></i> Added!';
                                var fragments = response.data.fragments || {};
                                var cartHash = response.data.cart_hash;
                                jQuery(document.body).trigger('added_to_cart', [fragments, cartHash, jQuery(atc)]);
                            } else {
                                atc.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                            }
                            setTimeout(() => {
                                atc.innerHTML = origHTML;
                            }, 2000);
                        },
                        error: function() {
                            atc.disabled = false;
                            atc.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                            setTimeout(() => {
                                atc.innerHTML = origHTML;
                            }, 2000);
                        }
                    });
                }
            });
        }
    }

    function initQuickViewButtons() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.pc-overlay button');
            if (!btn) return;

            const isQuickView = btn.title === 'Quick View' || btn.querySelector('.fa-eye') || btn.querySelector('.fa-regular.fa-eye');
            if (!isQuickView) return;

            e.preventDefault();
            console.log('Zendotech: Quick View Button Clicked', btn);

            const card = btn.closest('.product-card');
            if (!card) {
                console.error('Zendotech: Could not find .product-card parent');
                return;
            }

            // Try to find the product ID from multiple sources
            let productId = card.dataset.productId || card.getAttribute('data-product-id');
            
            if (!productId) {
                // Try to find it on the quick view button itself
                productId = btn.dataset.productId || btn.getAttribute('data-product-id');
            }

            if (!productId) {
                // Fallback: Try to find an add to cart button within the card and grab its product ID
                const atcBtn = card.querySelector('.add_to_cart_button') || card.querySelector('.ajax_add_to_cart') || card.querySelector('.atc-btn');
                if (atcBtn) {
                    productId = atcBtn.dataset.product_id || atcBtn.getAttribute('data-product_id');
                }
            }

            console.log('Zendotech: Product ID from card:', productId);

            if (productId) {
                openQuickView(productId);
            } else {
                console.error('Zendotech: Cannot find product ID on card or inner buttons', card);
            }
        });
    }

    /* -------------------------------------------
       15. HERO SLIDER
       ------------------------------------------- */
    function initHeroSlider() {
        if (typeof Swiper === 'undefined') {
            console.warn('Zendotech: Swiper is not defined. Hero slider will not initialize.');
            return;
        }

        const sliderEl = document.querySelector('.heroSlider');
        if (!sliderEl) return;

        const delay = parseInt(sliderEl.dataset.autoplayDelay) || 6000;
        const speed = parseInt(sliderEl.dataset.speed) || 1200;
        const pauseOnHover = sliderEl.dataset.pauseOnHover === 'yes';
        const effect = sliderEl.dataset.effect || 'fade';

        // Build Swiper config
        const swiperConfig = {
            loop: true,
            speed: speed,
            autoplay: {
                delay: delay,
                disableOnInteraction: false,
                pauseOnMouseEnter: pauseOnHover,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            watchSlidesProgress: true,
            on: {
                init: function () {
                    // Force refresh for animations
                }
            }
        };

        // Apply effect
        if (effect === 'fade') {
            swiperConfig.effect = 'fade';
            swiperConfig.fadeEffect = { crossFade: true };
        } else if (effect === 'creative') {
            swiperConfig.effect = 'creative';
            swiperConfig.creativeEffect = {
                prev: {
                    shadow: true,
                    translate: [0, 0, -400],
                    opacity: 0,
                },
                next: {
                    translate: ['100%', 0, 0],
                    opacity: 0,
                },
            };
        } else {
            // Default 'slide' with parallax
            swiperConfig.parallax = true;
        }

        new Swiper('.heroSlider', swiperConfig);
    }

    /* -------------------------------------------
       16. POPULAR PRODUCTS TABS
       ------------------------------------------- */
    function initPopularTabs() {
        const filterWrap = document.querySelector('.tab-filters');
        if (!filterWrap) return;

        const container = filterWrap.closest('.section')?.querySelector('.products-row');
        if (!container) return;

        const tabs = filterWrap.querySelectorAll('button');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const category = tab.textContent.trim();

                // Show loading state
                container.style.opacity = '0.5';

                if (typeof jQuery !== 'undefined' && typeof zendotechData !== 'undefined') {
                    jQuery.ajax({
                        url: zendotechData.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'zendotech_ajax_shop_filter',
                            category: category.toLowerCase() === 'all' ? '' : category,
                            orderby: 'popularity',
                            limit: 5
                        },
                        success: function (response) {
                            container.style.opacity = '1';
                            if (response.success) {
                                container.innerHTML = response.data.grid;
                                // Need to re-init dynamic features for new cards
                                initQuickViewButtons();
                                initAddToCartButtons();
                                initWishlistButtons();
                                initCompareButtons();
                            }
                        }
                    });
                }
            });
        });
    }

    /* -------------------------------------------
       17. FAQ ACCORDION — Already defined or simple toggle
       ------------------------------------------- */
    function initFAQ() {
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const header = item.querySelector('.faq-h');
            if (header) {
                header.addEventListener('click', () => {
                    item.classList.toggle('active');
                });
            }
        });
    }

    /* -------------------------------------------
       16. SHOP AJAX FILTERS
       ------------------------------------------- */
    function initShopFilters() {
        const shopMain = document.getElementById('shopMainContent');
        if (!shopMain) return;

        const sidebar = document.getElementById('shopSidebar');
        const grid = document.getElementById('shopGrid');
        const sortBy = document.getElementById('sortBy');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const btnFilterPrice = document.getElementById('btnFilterPrice');

        let currentData = {
            action: 'zendotech_ajax_shop_filter',
            category: '',
            brands: [],
            status: [],
            rating: [],
            min_price: 0,
            max_price: 5000,
            orderby: sortBy ? sortBy.value : 'default',
            paged: 1
        };

        const updateShop = (resetPage = true) => {
            if (resetPage) currentData.paged = 1;

            // Show Loading
            grid.classList.add('loading');
            grid.style.opacity = '0.5';

            console.log('Zendotech: Updating shop with data:', currentData);

            if (typeof jQuery !== 'undefined') {
                jQuery.ajax({
                    url: zendotechData.ajaxUrl,
                    type: 'POST',
                    data: currentData,
                    success: function (response) {
                        grid.classList.remove('loading');
                        grid.style.opacity = '1';

                        if (response.success) {
                            // Update Grid
                            grid.innerHTML = response.data.grid;

                            // Update Pagination
                            const paginationWrap = shopMain.querySelector('.shop-pagination');
                            if (paginationWrap) {
                                paginationWrap.innerHTML = response.data.pagination;
                            } else if (response.data.pagination) {
                                const newPagination = document.createElement('div');
                                newPagination.className = 'shop-pagination';
                                newPagination.innerHTML = response.data.pagination;
                                grid.after(newPagination);
                            }

                            // Update Count Text
                            const countEl = shopMain.querySelector('.results-count');
                            if (countEl) countEl.innerHTML = response.data.countText;

                            // Smooth Scroll to Top of Grid
                            shopMain.scrollIntoView({ behavior: 'smooth', block: 'start' });

                            // Re-init components for new cards
                            initQuickViewButtons();
                            initAddToCartButtons();
                            initWishlistButtons();
                            initCompareButtons();
                        } else {
                            grid.innerHTML = '<div class="no-products"><p>Error loading products.</p></div>';
                        }
                    },
                    error: function () {
                        grid.classList.remove('loading');
                        grid.style.opacity = '1';
                        grid.innerHTML = '<div class="no-products"><p>Server error. Please try again.</p></div>';
                    }
                });
            }
        };

        // Category Clicks
        sidebar.querySelectorAll('.category-list a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                sidebar.querySelectorAll('.category-list a').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                currentData.category = link.dataset.id;
                updateShop();
            });
        });

        // Checkbox Changes (Brands, Status, Rating)
        sidebar.querySelectorAll('.filter-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const type = cb.dataset.type;
                const val = cb.dataset.value;
                const isChecked = cb.checked;

                if (type === 'brand') {
                    if (isChecked) currentData.brands.push(val);
                    else currentData.brands = currentData.brands.filter(b => b !== val);
                } else if (type === 'status') {
                    if (isChecked) currentData.status.push(val);
                    else currentData.status = currentData.status.filter(s => s !== val);
                } else if (type === 'rating') {
                    if (isChecked) currentData.rating.push(val);
                    else currentData.rating = currentData.rating.filter(r => r !== val);
                }
                updateShop();
            });
        });

        // Price Filter
        if (btnFilterPrice) {
            btnFilterPrice.addEventListener('click', () => {
                currentData.min_price = minPrice.value || 0;
                currentData.max_price = maxPrice.value || 5000;
                updateShop();
            });
        }

        // Sort Dropdown
        if (sortBy) {
            sortBy.addEventListener('change', () => {
                currentData.orderby = sortBy.value;
                updateShop();
            });
        }

        // Pagination delegation
        shopMain.addEventListener('click', (e) => {
            const pageLink = e.target.closest('.shop-pagination a');
            if (pageLink) {
                e.preventDefault();
                // Extract page number from URL or text
                const url = new URL(pageLink.href);
                const paged = url.searchParams.get('paged') || pageLink.textContent;
                currentData.paged = parseInt(paged);
                updateShop(false);
            }
        });
    }

    /* -------------------------------------------
       INITIALIZATION
       ------------------------------------------- */
    async function init() {
        Toast.init();
        // Sync authoritative cart from server before rendering UI
        await CartStore.sync();
        initMiniCart();
        initCompareBar();
        CartStore.updateUI();
        WishlistStore.updateUI();
        CompareStore.updateUI();

        initHeaderActions();
        initMobileNav();
        initStickyHeader();
        initCategoryDropdown();
        initHomeSidebarLimit();
        initSearchBar();
        initNewsletter();
        initBackToTop();
        initCountdown();
        initAddToCartButtons();
        initWishlistButtons();
        initCompareButtons();
        initQuickViewButtons();
        initHeroSlider();
        initPopularTabs();
        initFAQ();
        initShopFilters();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose stores globally for page-specific scripts
    window.ZendotechApp = { CartStore, WishlistStore, CompareStore, Toast };

})();
