/* ============================================
   ZENDOTECH AUDIO — SHOP PAGE JS
   Filters, Sort, View Toggle, Pagination
   ============================================ */

(function () {
    'use strict';

    /* -------------------------------------------
       1. SIDEBAR FILTER TOGGLE (Mobile)
       ------------------------------------------- */
    function initFilterSidebar() {
        const sidebar = document.querySelector('.shop-sidebar');
        const toggleBtn = document.querySelector('.filter-toggle-btn');
        const closeBtn = document.querySelector('.sidebar-close');
        const overlay = document.querySelector('.sidebar-overlay');

        if (!sidebar) return;

        const toggle = (open) => {
            sidebar.classList.toggle('open', open);
            document.body.style.overflow = open ? 'hidden' : '';
        };

        if (toggleBtn) toggleBtn.addEventListener('click', () => toggle(true));
        if (closeBtn) closeBtn.addEventListener('click', () => toggle(false));
        if (overlay) overlay.addEventListener('click', () => toggle(false));
    }

    /* -------------------------------------------
       2. GRID / LIST VIEW TOGGLE
       ------------------------------------------- */
    function initViewToggle() {
        const viewBtns = document.querySelectorAll('.view-btn');
        const grid = document.querySelector('.shop-grid');
        if (!viewBtns.length || !grid) return;

        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const icon = btn.querySelector('i');
                if (!icon) return;

                if (icon.classList.contains('fa-list')) {
                    grid.classList.add('list-view');
                } else {
                    grid.classList.remove('list-view');
                }
            });
        });
    }

    /* -------------------------------------------
       3. SORT DROPDOWN
       ------------------------------------------- */
    function initSort() {
        const select = document.querySelector('.sort-dropdown select');
        const grid = document.querySelector('.shop-grid');
        if (!select || !grid) return;

        select.addEventListener('change', () => {
            const cards = [...grid.querySelectorAll('.product-card')];
            const sortBy = select.value;

            cards.sort((a, b) => {
                const getPrice = (el) => {
                    const priceEl = el.querySelector('.new-price');
                    return priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.]/g, '')) : 0;
                };
                const getName = (el) => {
                    const nameEl = el.querySelector('h4 a');
                    return nameEl ? nameEl.textContent.trim().toLowerCase() : '';
                };

                switch (sortBy) {
                    case 'price-low':
                        return getPrice(a) - getPrice(b);
                    case 'price-high':
                        return getPrice(b) - getPrice(a);
                    case 'name-asc':
                        return getName(a).localeCompare(getName(b));
                    case 'name-desc':
                        return getName(b).localeCompare(getName(a));
                    default:
                        return 0;
                }
            });

            // Re-append sorted cards
            cards.forEach(card => grid.appendChild(card));
        });
    }

    /* -------------------------------------------
       5. BRAND / STATUS CHECKBOX FILTERS
       ------------------------------------------- */
    function initCheckboxFilters() {
        const brandChecks = document.querySelectorAll('.brand-filter-list input[type="checkbox"]');
        const statusChecks = document.querySelectorAll('.status-filter-list input[type="checkbox"]');
        const afTags = document.querySelector('.af-tags');
        const activeFiltersBar = document.querySelector('.active-filters');
        const clearBtn = document.querySelector('.af-clear');

        const updateActiveFilters = () => {
            if (!afTags || !activeFiltersBar) return;

            afTags.innerHTML = '';
            let hasFilters = false;
            const selectedBrands = [];
            const selectedStatus = [];

            document.querySelectorAll('.brand-filter-list input:checked').forEach(input => {
                selectedBrands.push(input.dataset.value.toLowerCase());
            });
            document.querySelectorAll('.status-filter-list input:checked').forEach(input => {
                selectedStatus.push(input.dataset.value.toLowerCase());
            });

            const checkedInputs = document.querySelectorAll('.brand-filter-list input:checked, .status-filter-list input:checked');
            checkedInputs.forEach(input => {
                hasFilters = true;
                const label = input.closest('.check-label');
                const name = label ? label.querySelector('.bl-name')?.textContent?.trim() || label.textContent.trim() : '';

                const tag = document.createElement('span');
                tag.className = 'af-tag';
                tag.innerHTML = `${name} <button><i class="fa-solid fa-xmark"></i></button>`;
                tag.querySelector('button').addEventListener('click', () => {
                    input.checked = false;
                    updateActiveFilters();
                });
                afTags.appendChild(tag);
            });

            activeFiltersBar.style.display = hasFilters ? '' : 'none';

            // Actual Filtering
            const grid = document.querySelector('.shop-grid');
            if (!grid) return;
            const cards = grid.querySelectorAll('.product-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardName = card.querySelector('h4 a')?.textContent?.toLowerCase() || '';
                const cardCat = card.querySelector('.pc-cat')?.textContent?.toLowerCase() || '';

                let matchesBrand = selectedBrands.length === 0;
                if (!matchesBrand) {
                    matchesBrand = selectedBrands.some(brand => cardName.includes(brand) || cardCat.includes(brand));
                }

                let matchesStatus = selectedStatus.length === 0;
                if (!matchesStatus) {
                    const onSale = card.querySelector('.sale-tag');
                    const isNew = card.querySelector('.new-tag');
                    if (selectedStatus.includes('sale') && onSale) matchesStatus = true;
                    if (selectedStatus.includes('new') && isNew) matchesStatus = true;
                }

                if (matchesBrand && matchesStatus) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const resultsCount = document.querySelector('.results-count strong');
            if (resultsCount) resultsCount.textContent = visibleCount;
        };

        [...brandChecks, ...statusChecks].forEach(check => {
            check.addEventListener('change', updateActiveFilters);
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                [...brandChecks, ...statusChecks].forEach(c => c.checked = false);
                updateActiveFilters();
            });
        }

        // Initially hide if no filters active
        if (activeFiltersBar) activeFiltersBar.style.display = 'none';
    }

    /* -------------------------------------------
       6. PRICE RANGE — Dual Range Slider
       ------------------------------------------- */
    function initPriceRange() {
        const rangeMin = document.getElementById('rangeMin');
        const rangeMax = document.getElementById('rangeMax');
        const minInput = document.getElementById('minPrice');
        const maxInput = document.getElementById('maxPrice');
        const rangeFill = document.getElementById('rangeFill');

        if (!rangeMin || !rangeMax || !minInput || !maxInput || !rangeFill) return;

        const GAP = 50; // minimum gap between min and max

        function updateFill() {
            const min = parseInt(rangeMin.value);
            const max = parseInt(rangeMax.value);
            const total = parseInt(rangeMin.max);
            const leftPct = (min / total) * 100;
            const rightPct = ((total - max) / total) * 100;
            rangeFill.style.left = leftPct + '%';
            rangeFill.style.right = rightPct + '%';
            rangeFill.style.width = 'auto';
        }

        rangeMin.addEventListener('input', function () {
            let minVal = parseInt(rangeMin.value);
            let maxVal = parseInt(rangeMax.value);
            if (minVal > maxVal - GAP) {
                minVal = maxVal - GAP;
                rangeMin.value = minVal;
            }
            minInput.value = minVal;
            updateFill();
        });

        rangeMax.addEventListener('input', function () {
            let minVal = parseInt(rangeMin.value);
            let maxVal = parseInt(rangeMax.value);
            if (maxVal < minVal + GAP) {
                maxVal = minVal + GAP;
                rangeMax.value = maxVal;
            }
            maxInput.value = maxVal;
            updateFill();
        });

        minInput.addEventListener('change', function () {
            let val = parseInt(minInput.value) || 0;
            val = Math.max(0, Math.min(val, parseInt(rangeMax.value) - GAP));
            minInput.value = val;
            rangeMin.value = val;
            updateFill();
        });

        maxInput.addEventListener('change', function () {
            let val = parseInt(maxInput.value) || 5000;
            val = Math.min(5000, Math.max(val, parseInt(rangeMin.value) + GAP));
            maxInput.value = val;
            rangeMax.value = val;
            updateFill();
        });

        // Initialize fill position
        updateFill();
    }

    /* -------------------------------------------
       7. PAGINATION
       ------------------------------------------- */
    function initPagination() {
        const pgBtns = document.querySelectorAll('.pg-btn:not(.disabled)');
        if (!pgBtns.length) return;

        pgBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                // Navigate buttons (prev/next arrows)
                const icon = btn.querySelector('i');
                if (icon) {
                    const activePg = document.querySelector('.pg-btn.active');
                    if (!activePg) return;
                    const allBtns = [...document.querySelectorAll('.pg-btn:not(.disabled)')].filter(
                        b => !b.querySelector('i')
                    );
                    const currentIdx = allBtns.indexOf(activePg);

                    if (icon.classList.contains('fa-chevron-left') && currentIdx > 0) {
                        allBtns[currentIdx].classList.remove('active');
                        allBtns[currentIdx - 1].classList.add('active');
                    } else if (icon.classList.contains('fa-chevron-right') && currentIdx < allBtns.length - 1) {
                        allBtns[currentIdx].classList.remove('active');
                        allBtns[currentIdx + 1].classList.add('active');
                    }
                    return;
                }

                // Number buttons
                pgBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Scroll to top of shop grid
                const grid = document.querySelector('.shop-grid');
                if (grid) {
                    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    /* -------------------------------------------
       INITIALIZATION
       ------------------------------------------- */
    function init() {
        initFilterSidebar();
        initViewToggle();
        initSort();
        initCheckboxFilters();
        initPriceRange();
        initPagination();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
