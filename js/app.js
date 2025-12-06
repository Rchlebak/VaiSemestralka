// App initialization and small utilities
document.addEventListener('DOMContentLoaded', () => {
    // Open/close cart drawer
    const openCartBtn = document.getElementById('open-cart');
    const closeCartBtn = document.getElementById('close-cart');
    const cartDrawer = document.getElementById('cart-drawer');

    if(openCartBtn){
        openCartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            cartDrawer.classList.add('open');
        });
    }
    if(closeCartBtn){
        closeCartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            cartDrawer.classList.remove('open');
        });
    }

    // Init products and cart modules if available
    if(window.Products){
        window.Products.init();
    }
    if(window.Cart){
        window.Cart.init();
    }
});

// Simple fetch helper
window.apiFetch = async function(path, opts = {}){
    try{
        const res = await fetch(path, opts);
        const json = await res.json();
        return json;
    }catch(err){
        console.error('API fetch error', err);
        return { ok:false, error: 'Network error' };
    }
};

