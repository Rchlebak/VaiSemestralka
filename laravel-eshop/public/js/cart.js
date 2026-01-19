/**
 * Cart Module - Správa nákupného košíka
 * Používa localStorage pre perzistenciu dát
 * OOP prístup - modul s privátnym stavom
 */
window.Cart = (function () {
    'use strict';

    // Privátne premenné
    const STORAGE_KEY = 'eshop_cart_v2';
    let cart = { items: [] };

    /**
     * Uloží košík do localStorage
     */
    function save() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        } catch (e) {
            console.error('Cart save error:', e);
        }
        render();
    }

    /**
     * Načíta košík z localStorage
     */
    function load() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (raw) {
                cart = JSON.parse(raw);
            }
        } catch (e) {
            console.error('Cart load error:', e);
            cart = { items: [] };
        }
        render();
    }

    /**
     * Renderuje obsah košíka v DOM
     */
    function render() {
        const count = cart.items.reduce((sum, item) => sum + item.qty, 0);
        const subtotal = cart.items.reduce((sum, item) => sum + (item.price * item.qty), 0);

        // Aktualizuj počítadlo
        const countEl = document.getElementById('cart-count');
        if (countEl) {
            countEl.textContent = count;
        }

        // Aktualizuj medzisúčet
        const subtotalEl = document.getElementById('cart-subtotal');
        if (subtotalEl) {
            subtotalEl.textContent = subtotal.toFixed(2) + ' €';
        }

        // Renderuj položky
        const itemsEl = document.getElementById('cart-items');
        if (!itemsEl) return;

        if (cart.items.length === 0) {
            itemsEl.innerHTML = `
                <div class="text-center py-4">
                    <div style="font-size:3rem;opacity:0.3">🛒</div>
                    <div class="text-muted mt-2">Váš košík je prázdny</div>
                </div>
            `;
            return;
        }

        let html = '';
        cart.items.forEach((item, idx) => {
            const itemTotal = (item.price * item.qty).toFixed(2);
            const variantText = item.variant
                ? `${item.variant.color || ''} / ${item.variant.size || ''}`
                : '';

            html += `
                <div class="cart-item d-flex align-items-center gap-2 mb-3 p-2 border rounded">
                    <img src="${item.image || 'https://via.placeholder.com/60'}"
                         style="width:50px;height:50px;object-fit:cover;border-radius:6px"
                         onerror="this.src='https://via.placeholder.com/60'">
                    <div class="flex-grow-1">
                        <div class="fw-bold small">${escapeHtml(item.name)}</div>
                        ${variantText ? `<small class="text-muted">${escapeHtml(variantText)}</small>` : ''}
                        <div class="text-primary small">${item.price.toFixed(2)} €</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary btn-cart-decrease" data-idx="${idx}">−</button>
                        <span class="px-1">${item.qty}</span>
                        <button class="btn btn-sm btn-outline-secondary btn-cart-increase" data-idx="${idx}">+</button>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">${itemTotal} €</div>
                        <button class="btn btn-sm btn-link text-danger p-0 btn-cart-remove" data-idx="${idx}">
                            <small>×</small>
                        </button>
                    </div>
                </div>
            `;
        });

        itemsEl.innerHTML = html;
        bindCartButtons();
    }

    /**
     * Pripojí event listenery na tlačidlá v košíku
     */
    function bindCartButtons() {
        document.querySelectorAll('.btn-cart-decrease').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.idx, 10);
                updateQty(idx, cart.items[idx].qty - 1);
            });
        });

        document.querySelectorAll('.btn-cart-increase').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.idx, 10);
                updateQty(idx, cart.items[idx].qty + 1);
            });
        });

        document.querySelectorAll('.btn-cart-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.idx, 10);
                removeItem(idx);
            });
        });
    }

    /**
     * Escapuje HTML pre bezpečnosť
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Pridá položku do košíka
     */
    function addItem(item) {
        // Validácia vstupu
        if (!item.productId || !item.name || typeof item.price !== 'number') {
            console.error('Invalid cart item:', item);
            return;
        }

        const key = item.variantId
            ? `${item.productId}-${item.variantId}`
            : String(item.productId);

        const existing = cart.items.find(i => {
            const iKey = i.variantId
                ? `${i.productId}-${i.variantId}`
                : String(i.productId);
            return iKey === key;
        });

        const currentQty = existing ? existing.qty : 0;
        const newQty = currentQty + (item.qty || 1);
        const maxStock = item.maxStock || 1000; // Default ak nie je zadané

        if (newQty > maxStock) {
            showNotification(`Nie je možné pridať viac kusov. Na sklade je len ${maxStock} ks.`, 'error');
            return;
        }

        if (existing) {
            existing.qty = newQty;
            // Aktualizujeme maxStock ak sa zmenil (napr. doplnenie skladu)
            if (item.maxStock !== undefined) {
                existing.maxStock = item.maxStock;
            }
        } else {
            cart.items.push({
                productId: item.productId,
                variantId: item.variantId || null,
                name: item.name,
                price: item.price,
                qty: item.qty || 1,
                image: item.image || null,
                variant: item.variant || null,
                maxStock: item.maxStock || 1000
            });
        }

        save();
        showNotification(`${item.name} bol pridaný do košíka`);
        openCartDrawer();
    }

    /**
     * Aktualizuje množstvo položky
     */
    function updateQty(idx, qty) {
        const item = cart.items[idx];
        const maxStock = item.maxStock || 1000;

        if (qty <= 0) {
            removeItem(idx);
        } else if (qty > maxStock) {
            showNotification(`Max. dostupné množstvo je ${maxStock} ks`, 'warning');
            cart.items[idx].qty = maxStock;
            save(); // Uložíme max. možnú hodnotu
        } else if (qty <= 99) {
            cart.items[idx].qty = qty;
            save();
        }
    }

    /**
     * Odstráni položku z košíka
     */
    function removeItem(idx) {
        cart.items.splice(idx, 1);
        save();
    }

    /**
     * Vyčistí celý košík
     */
    function clear() {
        cart.items = [];
        save();
    }

    /**
     * Získa celkovú sumu
     */
    function getTotal() {
        return cart.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
    }

    /**
     * Získa počet položiek
     */
    function getItemCount() {
        return cart.items.reduce((sum, i) => sum + i.qty, 0);
    }

    /**
     * Získa všetky položky
     */
    function getItems() {
        return [...cart.items];
    }

    /**
     * Zobrazí notifikáciu
     */
    /**
     * Zobrazí notifikáciu
     */
    function showNotification(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';

        // Farba podľa typu
        if (type === 'error') {
            toast.style.backgroundColor = '#dc3545';
        } else if (type === 'warning') {
            toast.style.backgroundColor = '#ffc107';
            toast.style.color = '#000';
        }

        const icon = type === 'error' ? 'bi-x-circle' : (type === 'warning' ? 'bi-exclamation-triangle' : 'bi-check-circle');

        toast.innerHTML = `<i class="bi ${icon} me-2"></i>${escapeHtml(message)}`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    /**
     * Otvorí košík drawer
     */
    function openCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-overlay');
        if (drawer) {
            drawer.classList.add('open');
        }
        if (overlay) {
            overlay.classList.add('open');
        }
    }

    /**
     * Zatvorí košík drawer
     */
    function closeCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-overlay');
        if (drawer) {
            drawer.classList.remove('open');
        }
        if (overlay) {
            overlay.classList.remove('open');
        }
    }

    /**
     * Inicializácia modulu
     */
    function init() {
        load();

        // Tlačidlo otvoriť košík
        document.getElementById('open-cart')?.addEventListener('click', (e) => {
            e.preventDefault();
            openCartDrawer();
        });

        // Tlačidlo zavrieť košík
        document.getElementById('close-cart')?.addEventListener('click', (e) => {
            e.preventDefault();
            closeCartDrawer();
        });

        // Overlay klik zavrieť
        document.getElementById('cart-overlay')?.addEventListener('click', closeCartDrawer);
    }

    // Verejné API
    return {
        init,
        addItem,
        updateQty,
        removeItem,
        clear,
        getTotal,
        getItemCount,
        getItems,
        render
    };
})();

