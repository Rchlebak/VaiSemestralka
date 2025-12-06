// Enhanced Cart implementation using localStorage and drawer UI
window.Cart = (function(){
    const KEY = 'eshop_cart_v2';
    let cart = { items: [] };

    function save(){
        localStorage.setItem(KEY, JSON.stringify(cart));
        render();
    }

    function load(){
        const raw = localStorage.getItem(KEY);
        if(raw){
            try{
                cart = JSON.parse(raw);
            } catch(e){
                cart = { items:[] };
            }
        }
        render();
    }

    function render(){
        const count = cart.items.reduce((s,i) => s + i.qty, 0);
        const subtotal = cart.items.reduce((s,i) => s + (i.price * i.qty), 0);

        const countEl = document.getElementById('cart-count');
        const subtotalEl = document.getElementById('cart-subtotal');
        const itemsEl = document.getElementById('cart-items');

        if(countEl) countEl.textContent = count;
        if(subtotalEl) subtotalEl.textContent = subtotal.toFixed(2) + ' €';

        if(!itemsEl) return;

        itemsEl.innerHTML = '';

        if(cart.items.length === 0){
            itemsEl.innerHTML = `
                <div class="text-center py-4">
                    <div style="font-size:3rem;opacity:0.3">🛒</div>
                    <div class="text-muted mt-2">Váš košík je prázdny</div>
                    <a href="/" class="btn btn-sm btn-outline-primary mt-3">Začať nakupovať</a>
                </div>
            `;
            return;
        }

        cart.items.forEach((it, idx) => {
            const row = document.createElement('div');
            row.className = 'cart-item d-flex align-items-center gap-3 mb-3 p-2 border rounded';
            row.innerHTML = `
                <div class="cart-item-img">
                    <img src="${it.image || 'https://picsum.photos/seed/p'+it.productId+'/80/80'}" 
                         style="width:60px;height:60px;object-fit:cover;border-radius:6px" 
                         onerror="this.src='https://via.placeholder.com/60'">
                </div>
                <div class="flex-grow-1">
                    <div style="font-weight:600">${it.name}</div>
                    ${it.variant ? `<div class="small text-muted">${it.variant.color || ''} / ${it.variant.size || ''}</div>` : ''}
                    <div class="small text-primary">${it.price.toFixed(2)} € / ks</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-outline-secondary btn-decrease" data-idx="${idx}">−</button>
                    <span class="px-2" style="min-width:30px;text-align:center">${it.qty}</span>
                    <button class="btn btn-sm btn-outline-secondary btn-increase" data-idx="${idx}">+</button>
                </div>
                <div class="text-end" style="min-width:70px">
                    <div style="font-weight:600">${(it.price * it.qty).toFixed(2)} €</div>
                    <button class="btn btn-sm btn-link text-danger p-0 btn-remove" data-idx="${idx}">
                        <small>Odstrániť</small>
                    </button>
                </div>
            `;
            itemsEl.appendChild(row);
        });

        // Bind event listeners
        itemsEl.querySelectorAll('.btn-decrease').forEach(b =>
            b.addEventListener('click', () => {
                const i = parseInt(b.dataset.idx, 10);
                updateQty(i, cart.items[i].qty - 1);
            })
        );
        itemsEl.querySelectorAll('.btn-increase').forEach(b =>
            b.addEventListener('click', () => {
                const i = parseInt(b.dataset.idx, 10);
                updateQty(i, cart.items[i].qty + 1);
            })
        );
        itemsEl.querySelectorAll('.btn-remove').forEach(b =>
            b.addEventListener('click', () => {
                const i = parseInt(b.dataset.idx, 10);
                removeItem(i);
            })
        );
    }

    function addItem(item){
        // item: { productId, variantId, name, price, qty, image, variant: {color, size} }
        const key = item.variantId ? `${item.productId}-${item.variantId}` : item.productId;
        const existing = cart.items.find(i => {
            const iKey = i.variantId ? `${i.productId}-${i.variantId}` : i.productId;
            return iKey === key;
        });

        if(existing){
            existing.qty += item.qty;
        } else {
            cart.items.push({
                productId: item.productId,
                variantId: item.variantId || null,
                name: item.name,
                price: item.price,
                qty: item.qty,
                image: item.image || null,
                variant: item.variant || null
            });
        }
        save();

        // Show notification
        showNotification(`${item.name} bol pridaný do košíka`);
    }

    function updateQty(idx, qty){
        if(qty <= 0){
            cart.items.splice(idx, 1);
        } else {
            cart.items[idx].qty = qty;
        }
        save();
    }

    function removeItem(idx){
        cart.items.splice(idx, 1);
        save();
    }

    function clear(){
        cart.items = [];
        save();
    }

    function getTotal(){
        return cart.items.reduce((s, i) => s + (i.price * i.qty), 0);
    }

    function getItemCount(){
        return cart.items.reduce((s, i) => s + i.qty, 0);
    }

    function showNotification(message){
        // Create toast notification
        let toast = document.getElementById('cart-toast');
        if(!toast){
            toast = document.createElement('div');
            toast.id = 'cart-toast';
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9999;
                transition: opacity 0.3s, transform 0.3s;
                opacity: 0;
                transform: translateY(20px);
            `;
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 2500);
    }

    // Initialize cart drawer toggle
    function initDrawer(){
        const openBtn = document.getElementById('open-cart');
        const closeBtn = document.getElementById('close-cart');
        const drawer = document.getElementById('cart-drawer');

        if(openBtn && drawer){
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                drawer.classList.add('open');
            });
        }

        if(closeBtn && drawer){
            closeBtn.addEventListener('click', () => {
                drawer.classList.remove('open');
            });
        }

        // Close on click outside
        if(drawer){
            document.addEventListener('click', (e) => {
                if(drawer.classList.contains('open') &&
                   !drawer.contains(e.target) &&
                   e.target !== openBtn &&
                   !openBtn?.contains(e.target)){
                    drawer.classList.remove('open');
                }
            });
        }
    }

    return {
        init: function(){
            load();
            initDrawer();
        },
        addItem,
        updateQty,
        removeItem,
        clear,
        getCart: () => cart,
        getTotal,
        getItemCount
    };
})();

// Auto-initialize when DOM is ready
if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', () => Cart.init());
} else {
    Cart.init();
}
