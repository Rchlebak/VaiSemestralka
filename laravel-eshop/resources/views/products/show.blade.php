@extends('layouts.app')

@section('title', $product->name . ' - E-Shop Tenisiek')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Domov</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Galéria obrázkov -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="product-gallery">
                    @php
                        $rawMainImage = $product->main_image ?? null;
                        if ($rawMainImage) {
                            // Use root-relative path for local storage, absolute URL for external
                            $mainImage = Str::startsWith($rawMainImage, 'http')
                                ? $rawMainImage
                                : '/' . ltrim($rawMainImage, '/');
                        } else {
                            $mainImage = 'https://via.placeholder.com/600x500?text=No+Image';
                        }
                    @endphp
                    <img id="main-image" src="{{ $mainImage }}" class="img-fluid rounded" alt="{{ $product->name }}"
                        style="width: 100%; max-height: 500px; object-fit: contain;"
                        onerror="this.src='https://via.placeholder.com/600x500?text=No+Image'">
                </div>

                @if($product->images->count() > 1)
                    <div class="d-flex gap-2 mt-3 p-3 flex-wrap">
                        @foreach($product->images as $image)
                            @php
                                $imgPath = Str::startsWith($image->image_path, 'http')
                                    ? $image->image_path
                                    : '/' . ltrim($image->image_path, '/');
                            @endphp
                            <img src="{{ $imgPath }}" class="thumbnail-img {{ $image->is_main ? 'active' : '' }}"
                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid {{ $image->is_main ? '#0d6efd' : '#ddd' }}; border-radius: 6px;"
                                onclick="document.getElementById('main-image').src='{{ $imgPath }}'; document.querySelectorAll('.thumbnail-img').forEach(i => i.style.borderColor='#ddd'); this.style.borderColor='#0d6efd';"
                                onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Detaily produktu -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="h2 mb-2">{{ $product->name }}</h1>

                    <p class="text-muted">
                        <span class="badge bg-secondary">{{ $product->brand }}</span>
                        @if($product->sku_model)
                            <small class="ms-2">SKU: {{ $product->sku_model }}</small>
                        @endif
                    </p>

                    <div class="h3 text-primary mb-4">
                        {{ number_format($product->base_price, 2) }} €
                    </div>

                    @if($product->description)
                        <div class="mb-4">
                            <h5>Popis</h5>
                            <p>{{ $product->description }}</p>
                        </div>
                    @endif

                    <!-- Výber variantu -->
                    @if($product->variants->count() > 0)
                        <form id="add-to-cart-form">
                            <!-- Výber farby -->
                            @if($variantsByColor->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Farba</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($variantsByColor->keys() as $color)
                                            <button type="button"
                                                class="btn btn-outline-secondary color-option {{ $loop->first ? 'active' : '' }}"
                                                data-color="{{ $color }}" onclick="selectColor('{{ $color }}', this)">
                                                <span class="color-dot"
                                                    style="background: {{ $color }}; width: 16px; height: 16px; border-radius: 50%; display: inline-block; vertical-align: middle; margin-right: 4px;"></span>
                                                {{ $color }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Výber veľkosti -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Veľkosť</label>
                                <div id="size-options" class="d-flex flex-wrap gap-2">
                                    @foreach($product->available_sizes as $size)
                                        <button type="button" class="btn btn-outline-secondary size-option" data-size="{{ $size }}"
                                            onclick="selectSize('{{ $size }}', this)">
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Množstvo -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Množstvo</label>
                                <div class="input-group" style="width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQty(-1)">-</button>
                                    <input type="number" id="quantity" class="form-control text-center" value="1" min="1"
                                        max="10">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
                                </div>
                            </div>

                            <!-- Stav skladu -->
                            <div id="stock-info" class="mb-3">
                                <span class="text-success"><i class="bi bi-check-circle"></i> Skladom</span>
                            </div>

                            <!-- Pridať do košíka -->
                            <button type="button" id="add-to-cart-btn" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-cart-plus"></i> Pridať do košíka
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Produkt momentálne nemá dostupné varianty.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const product = {
            id: {{ $product->product_id }},
            name: "{{ addslashes($product->name) }}",
            price: {{ $product->base_price }},
                @php
                    $jsImage = $product->main_image ?? null;
                    if ($jsImage && !Str::startsWith($jsImage, 'http')) {
                        $jsImage = '/' . ltrim($jsImage, '/');
                    } elseif (!$jsImage) {
                        $jsImage = 'https://via.placeholder.com/400x300?text=No+Image';
                    }
                @endphp
            image: "{{ $jsImage }}"
            };
            const variants = @json($product->variants);
        let selectedColor = null;
            let selectedSize = null;
        function selectColor(color, btn) {
            selectedColor = color;
            document.querySelectorAll('.color-option').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            updateStockInfo();
            }
        function selectSize(size, btn) {
            selectedSize = size;
            document.querySelectorAll('.size-option').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            updateStockInfo();
            }
        function changeQty(delta) {
            const input = document.getElementById('quantity');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            if (val > 10) val = 10;
            input.value = val;
            }
        function updateStockInfo() {
            const stockEl = document.getElementById('stock-info');
            if (!selectedColor || !selectedSize) {
                stockEl.innerHTML = '<span class="text-muted"><i class="bi bi-info-circle"></i> Vyberte farbu a veľkosť</span>';
                return;
                }
            const variant = variants.find(v => v.color === selectedColor && v.size_eu === selectedSize && v.is_active === 1);
            if (!variant) {
                stockEl.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Nedostupné</span>';
                const btn = document.getElementById('add-to-cart-btn');
                if(btn) {
                    btn.disabled = true;
                    btn.classList.add('btn-secondary');
                    btn.classList.remove('btn-primary');
                }
            } else {
                const stockQty = variant.inventory ? variant.inventory.stock_qty : 0;
                const btn = document.getElementById('add-to-cart-btn');
                
                if (stockQty > 5) {
                    stockEl.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Skladom (${stockQty} ks)</span>`;
                    if(btn) {
                        btn.disabled = false;
                        btn.classList.add('btn-primary');
                        btn.classList.remove('btn-secondary');
                    }
                } else if (stockQty > 0) {
                     stockEl.innerHTML = `<span class="text-warning fw-bold"><i class="bi bi-exclamation-circle"></i> Čoskoro vypredané (${stockQty} ks)</span>`;
                     if(btn) {
                        btn.disabled = false;
                        btn.classList.add('btn-primary');
                        btn.classList.remove('btn-secondary');
                     }
                } else {
                    stockEl.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle"></i> Vypredané</span>';
                    if(btn) {
                        btn.disabled = true;
                        btn.classList.add('btn-secondary');
                        btn.classList.remove('btn-primary');
                    }
                }
            }
        }
        // Inicializácia
            document.querySelector('.color-option')?.click();
        document.getElementById('add-to-cart-btn')?.addEventListener('click', function () {
                const qty = parseInt(document.getElementById('quantity').value) || 1;
            let variantId = null;
            let variantInfo = null;
                let maxStock = 1000; // Default fallback
            if (selectedColor && selectedSize) {
                const variant = variants.find(v => v.color === selectedColor && v.size_eu === selectedSize && v.is_active === 1);
                if (variant) {
                        const stockQty = variant.inventory ? variant.inventory.stock_qty : 0;
                    if (stockQty < qty) {
                        alert(`Na sklade je len ${stockQty} ks.`);
                        return;
                    }
                    variantId = variant.variant_id;
                    variantInfo = { color: selectedColor, size: selectedSize };
                    maxStock = stockQty;
                }
            } else {
                // Ak nie je vybraný variant (pre simple produkty bez variantov), skúsime zistiť stock inak alebo dáme default
                // Tu by sme mali mať globálny stock pre produkt, ak existuje.
                // V našom dátovom modeli majú stock len varianty.
                alert("Prosím vyberte farbu a veľkosť.");
                return;
                }
            if (window.Cart) {
                window.Cart.addItem({
                    productId: product.id,
                    variantId: variantId,
                    name: product.name,
                    price: product.price,
                    qty: qty,
                    image: product.image,
                    variant: variantInfo,
                    maxStock: maxStock
                });
            }
        });
        </script>
@endpush