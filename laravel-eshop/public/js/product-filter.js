/**
 * ProductFilter Module - AJAX filtrovanie produktov
 * Zmysluplné AJAX volanie č.1 - filtrovanie bez reload stránky
 * 
 * Funkcie:
 * - Live vyhľadávanie (debounced)
 * - Filtrovanie podľa ceny, veľkosti, farby
 * - Zoradenie produktov
 * - Dynamické načítanie produktov bez refresh
 */
window.ProductFilter = (function() {
    'use strict';

    // Konfigurácia
    const API_URL = '/api/products';
    const DEBOUNCE_DELAY = 300;

    // State
    let filters = {
        q: '',
        minPrice: null,
        maxPrice: null,
        sizes: [],
        colors: [],
        sort: 'default'
    };

    let debounceTimer = null;
    let isLoading = false;

    /**
     * Inicializácia modulu
     */
    function init() {
        // Načítaj existujúce filtre z URL
        loadFiltersFromUrl();
        
        // Event listenery
        bindEvents();
        
        console.log('ProductFilter initialized');
    }

    /**
     * Načíta filtre z URL parametrov
     */
    function loadFiltersFromUrl() {
        const params = new URLSearchParams(window.location.search);
        
        filters.q = params.get('q') || '';
        filters.minPrice = params.get('min_price') || null;
        filters.maxPrice = params.get('max_price') || null;
        filters.sort = params.get('sort') || 'default';
        
        if (params.get('sizes')) {
            filters.sizes = params.get('sizes').split(',');
        }
        if (params.get('colors')) {
            filters.colors = params.get('colors').split(',');
        }
    }

    /**
     * Pripojí event listenery
     */
    function bindEvents() {
        // Live vyhľadávanie
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filters.q = this.value;
                debouncedFetch();
            });
        }

        // Cenové filtre
        const minPrice = document.getElementById('min-price');
        const maxPrice = document.getElementById('max-price');
        
        if (minPrice) {
            minPrice.addEventListener('change', function() {
                filters.minPrice = this.value || null;
                fetchProducts();
            });
        }
        
        if (maxPrice) {
            maxPrice.addEventListener('change', function() {
                filters.maxPrice = this.value || null;
                fetchProducts();
            });
        }

        // Veľkosti - AJAX toggle
        document.querySelectorAll('.size-filter-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleFilter('sizes', this.dataset.size, this);
            });
        });

        // Farby - AJAX toggle
        document.querySelectorAll('.color-filter-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleFilter('colors', this.dataset.color, this);
            });
        });

        // Zoradenie
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                filters.sort = this.value;
                fetchProducts();
            });
        }

        // AJAX tlačidlo filtrov (prepíše štandardné odoslanie)
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchProducts();
            });
        }

        // Vymazať filtre
        const clearBtn = document.querySelector('a[href*="route(\'home\')"], .btn-clear-filters');
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                clearFilters();
            });
        }
    }

    /**
     * Toggle filter hodnoty
     */
    function toggleFilter(type, value, btn) {
        const index = filters[type].indexOf(value);
        
        if (index > -1) {
            filters[type].splice(index, 1);
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-outline-secondary');
        } else {
            filters[type].push(value);
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        }
        
        fetchProducts();
    }

    /**
     * Debounced fetch pre live search
     */
    function debouncedFetch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchProducts, DEBOUNCE_DELAY);
    }

    /**
     * Získa produkty cez AJAX
     */
    async function fetchProducts() {
        if (isLoading) return;
        
        isLoading = true;
        showLoading();

        try {
            const params = new URLSearchParams();
            
            if (filters.q) params.set('q', filters.q);
            if (filters.minPrice) params.set('min_price', filters.minPrice);
            if (filters.maxPrice) params.set('max_price', filters.maxPrice);
            if (filters.sizes.length) params.set('sizes', filters.sizes.join(','));
            if (filters.colors.length) params.set('colors', filters.colors.join(','));
            if (filters.sort !== 'default') params.set('sort', filters.sort);

            const url = `${API_URL}?${params.toString()}`;
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.ok && result.data) {
                renderProducts(result.data);
                updateUrl(params);
                showToast(`Nájdených ${result.data.length} produktov`, 'success');
            } else {
                showToast('Chyba pri načítaní produktov', 'error');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            showToast('Chyba siete', 'error');
        } finally {
            isLoading = false;
            hideLoading();
        }
    }

    /**
     * Renderuje produkty do gridu
     */
    function renderProducts(products) {
        const grid = document.getElementById('products-grid');
        if (!grid) return;

        if (products.length === 0) {
            grid.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Žiadne produkty nenájdené.
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        products.forEach(product => {
            const sizes = product.sizes || [];
            const colors = product.colors || [];
            const image = product.image || `https://picsum.photos/seed/p${product.id}/400/300`;

            html += `
                <div class="col">
                    <div class="card h-100 product-card">
                        <div class="product-image-wrapper">
                            <img src="${image}" 
                                 class="card-img-top product-img" 
                                 alt="${escapeHtml(product.name)}"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">${escapeHtml(product.name)}</h5>
                            <p class="text-muted small mb-2">${escapeHtml(product.brand || '')}</p>
                            
                            ${colors.length ? `
                                <div class="mb-2">
                                    ${colors.map(c => `<span class="color-dot" title="${c}" style="background:${c};display:inline-block;width:14px;height:14px;border-radius:50%;margin-right:4px;border:1px solid #ccc"></span>`).join('')}
                                </div>
                            ` : ''}
                            
                            ${sizes.length ? `
                                <div class="mb-2">
                                    ${sizes.map(s => `<span class="badge bg-secondary">${s}</span>`).join(' ')}
                                </div>
                            ` : ''}
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">${product.price.toFixed(2)} €</span>
                                    <div>
                                        <a href="/product/${product.id}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-primary btn-add-to-cart"
                                                data-product-id="${product.id}"
                                                data-product-name="${escapeHtml(product.name)}"
                                                data-product-price="${product.price}"
                                                data-product-image="${image}">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
        
        // Znovu pripoj cart listenery
        bindCartButtons();
    }

    /**
     * Pripojí event listenery na cart tlačidlá
     */
    function bindCartButtons() {
        document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                const product = {
                    productId: this.dataset.productId,
                    name: this.dataset.productName,
                    price: parseFloat(this.dataset.productPrice),
                    image: this.dataset.productImage,
                    qty: 1
                };

                if (window.Cart) {
                    window.Cart.addItem(product);
                }
            });
        });
    }

    /**
     * Aktualizuje URL bez reload
     */
    function updateUrl(params) {
        const url = new URL(window.location);
        url.search = params.toString();
        window.history.replaceState({}, '', url);
    }

    /**
     * Vymaže všetky filtre
     */
    function clearFilters() {
        filters = {
            q: '',
            minPrice: null,
            maxPrice: null,
            sizes: [],
            colors: [],
            sort: 'default'
        };

        // Reset inputov
        const searchInput = document.getElementById('search-input');
        if (searchInput) searchInput.value = '';
        
        const minPrice = document.getElementById('min-price');
        if (minPrice) minPrice.value = '';
        
        const maxPrice = document.getElementById('max-price');
        if (maxPrice) maxPrice.value = '';
        
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) sortSelect.value = 'default';

        // Reset tlačidiel
        document.querySelectorAll('.size-filter-btn, .color-filter-btn').forEach(btn => {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-outline-secondary');
        });

        fetchProducts();
    }

    /**
     * Zobrazí loading state
     */
    function showLoading() {
        const grid = document.getElementById('products-grid');
        if (grid) {
            grid.classList.add('loading');
        }
    }

    /**
     * Skryje loading state
     */
    function hideLoading() {
        const grid = document.getElementById('products-grid');
        if (grid) {
            grid.classList.remove('loading');
        }
    }

    /**
     * Zobrazí toast notifikáciu
     */
    function showToast(message, type = 'success') {
        if (window.showToast) {
            window.showToast(message, type);
        }
    }

    /**
     * Escapuje HTML
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Verejné API
    return {
        init,
        fetchProducts,
        clearFilters,
        getFilters: () => ({...filters})
    };
})();

// Auto-inicializácia
document.addEventListener('DOMContentLoaded', function() {
    // Inicializuj len na stránke s produktami
    if (document.getElementById('products-grid')) {
        window.ProductFilter.init();
    }
});
