// Products module: fetches products (from API or sample data), renders grid, handles filters and sorting
window.Products = (function(){
    const state = {
        products: [],
        filters: {
            q: '', sizes: [], colors: [], minPrice: null, maxPrice: null
        },
        sort: 'default'
    };

    function sampleData(){
        return [
            { id:1, name:'Runner Pro', brand:'Nova', price:89.99, old_price:109.99, colors:['Black','White'], sizes:['40','41','42'], image:'https://picsum.photos/seed/p1/400/300' },
            { id:2, name:'Street Flex', brand:'Urban', price:69.50, colors:['Red','Black'], sizes:['39','40','42'], image:'https://picsum.photos/seed/p2/400/300' },
            { id:3, name:'Trail Max', brand:'Outdoor', price:120.00, colors:['Green','Black'], sizes:['42','43','44'], image:'https://picsum.photos/seed/p3/400/300' }
        ];
    }

    function renderProducts(list){
        const grid = document.getElementById('products-grid');
        if(!grid) return;
        grid.innerHTML = '';
        list.forEach(p =>{
            const card = document.createElement('div');
            card.className = 'product-card';

            // build colors html
            let colorsHtml = '';
            if(p.colors && p.colors.length){
                colorsHtml = '<div class="product-colors mb-2">';
                p.colors.forEach(c=>{
                    colorsHtml += `<span class="color-dot" title="${c}" style="background:${c};display:inline-block;width:14px;height:14px;border-radius:50%;margin-right:6px;border:1px solid #ccc"></span>`;
                });
                colorsHtml += '</div>';
            }

            // build sizes html
            let sizesHtml = '';
            if(p.sizes && p.sizes.length){
                sizesHtml = '<div class="product-sizes mb-2">';
                p.sizes.forEach(s=>{
                    sizesHtml += `<button class="btn btn-sm btn-outline-secondary me-1 size-btn" data-size="${s}">${s}</button>`;
                });
                sizesHtml += '</div>';
            }

            card.innerHTML = `
                ${p.old_price?'<div class="badge-sale">ZĽAVA</div>':''}
                <img src="${p.image}" alt="${p.name}" class="product-img">
                <div>
                    <h5 style="margin:0">${p.name}</h5>
                    <div class="text-muted small">${p.brand}</div>
                </div>
                <div class="mt-2">
                    ${colorsHtml}
                    ${sizesHtml}
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <div class="price">${p.price} €</div>
                        ${p.old_price?`<div class="old-price">${p.old_price} €</div>`:''}
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-sm btn-outline-primary btn-view" data-id="${p.id}">Zobraziť</button>
                        <button class="btn btn-sm btn-primary btn-add" data-id="${p.id}">Pridať</button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });

        // bind buttons
        grid.querySelectorAll('.btn-add').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const id = parseFloat(btn.dataset.id);
                const prod = state.products.find(x=>parseFloat(x.id)===id);
                if(prod){
                    // choose first available variant with stock
                    let variant = null;
                    if(prod.variants && prod.variants.length){
                        variant = prod.variants.find(v => v.stock_qty > 0) || prod.variants[0];
                    }
                    const variantId = variant ? variant.variant_id : null;
                    const variantInfo = variant ? { color: variant.color, size: variant.size_eu } : null;

                    window.Cart.addItem({
                        productId: prod.id,
                        variantId: variantId,
                        name: prod.name,
                        price: prod.price,
                        qty: 1,
                        image: prod.image,
                        variant: variantInfo
                    });
                }
            });
        });
        grid.querySelectorAll('.btn-view').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const id = btn.dataset.id;
                window.location.href = `product.html?id=${id}`;
            });
        });

        // optional: size buttons click toggles selection
        grid.querySelectorAll('.size-btn').forEach(b=>{
            b.addEventListener('click', ()=>{ b.classList.toggle('btn-selected'); });
        });
    }

    function applyFilters(){
        let list = state.products.slice();
        const q = document.getElementById('search-input')?.value?.toLowerCase() || '';
        const min = parseFloat(document.getElementById('min-price')?.value) || null;
        const max = parseFloat(document.getElementById('max-price')?.value) || null;
        if(q){ list = list.filter(p => p.name.toLowerCase().includes(q) || (p.brand && p.brand.toLowerCase().includes(q)) ); }
        if(min !== null){ list = list.filter(p => p.price >= min); }
        if(max !== null){ list = list.filter(p => p.price <= max); }
        // filter by selected sizes/colors
        const selectedSizes = Array.from(document.querySelectorAll('#filter-sizes .btn-selected')).map(x=>x.textContent.trim());
        const selectedColors = Array.from(document.querySelectorAll('#filter-colors .btn-selected')).map(x=>x.textContent.trim());
        if(selectedSizes.length){ list = list.filter(p => (p.sizes || []).some(s => selectedSizes.includes(s))); }
        if(selectedColors.length){ list = list.filter(p => (p.colors || []).some(c => selectedColors.includes(c))); }
        // sort
        const sort = document.getElementById('sort-select')?.value || 'default';
        if(sort === 'price-asc') list.sort((a,b)=>a.price-b.price);
        if(sort === 'price-desc') list.sort((a,b)=>b.price-a.price);
        renderProducts(list);
    }

    function showToast(msg){
        // small toast fallback
        const t = document.createElement('div');
        t.className = 'position-fixed bottom-0 end-0 m-3 p-2 bg-dark text-white rounded';
        t.style.zIndex = 2000;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(()=>t.remove(),1500);
    }

    return {
        init: async function(){
            // try fetch from API
            let res;
            try{
                res = await window.apiFetch('/api/products.php');
            }catch(e){ res = null; }
            if(res && res.ok && Array.isArray(res.data)){
                state.products = res.data;
            }else{
                state.products = sampleData();
            }

            // populate filters (sizes/colors)
            const sizesEl = document.getElementById('filter-sizes');
            const colorsEl = document.getElementById('filter-colors');
            const allSizes = new Set(); const allColors = new Set();
            state.products.forEach(p=>{ (p.sizes||[]).forEach(s=>allSizes.add(s)); (p.colors||[]).forEach(c=>allColors.add(c)); });
            if(sizesEl){ allSizes.forEach(s=>{ const btn = document.createElement('button'); btn.className='btn btn-sm btn-outline-secondary'; btn.textContent=s; btn.addEventListener('click', ()=>{ btn.classList.toggle('btn-selected'); applyFilters(); }); sizesEl.appendChild(btn); }); }
            if(colorsEl){ allColors.forEach(c=>{ const btn = document.createElement('button'); btn.className='btn btn-sm btn-outline-secondary'; btn.textContent=c; btn.addEventListener('click', ()=>{ btn.classList.toggle('btn-selected'); applyFilters(); }); colorsEl.appendChild(btn); }); }

            renderProducts(state.products);

            // bind filter events
            document.getElementById('apply-filters')?.addEventListener('click', applyFilters);
            document.getElementById('clear-filters')?.addEventListener('click', ()=>{ document.getElementById('search-input').value=''; document.getElementById('min-price').value=''; document.getElementById('max-price').value=''; applyFilters(); });
            document.getElementById('sort-select')?.addEventListener('change', applyFilters);
        }
    };
})();
