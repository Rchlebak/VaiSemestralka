@extends('layouts.app')

@section('title', 'E-Shop Tenisiek - Najlepšie tenisky')

@section('content')
<!-- Hero sekcia -->
<section class="hero text-center py-4 bg-light rounded mb-4">
    <h1><i class="bi bi-shoe"></i> Vitajte v našom e-shope tenisiek</h1>
    <p class="lead text-muted">Najlepšie tenisky pre každú príležitosť!</p>
</section>

<div class="row">
    <!-- Filtre a vyhľadávanie -->
    <aside class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-search"></i> Vyhľadávanie a filtre
            </div>
            <div class="card-body">
                <form action="{{ route('home') }}" method="GET" id="filter-form">
                    <!-- Vyhľadávanie -->
                    <div class="mb-3">
                        <label class="form-label">Hľadať</label>
                        <input type="text" name="q" id="search-input" class="form-control"
                               placeholder="Názov alebo značka..." value="{{ request('q') }}">
                    </div>

                    <!-- Cena -->
                    <div class="mb-3">
                        <label class="form-label">Cena (€)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" id="min-price" class="form-control"
                                       placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" id="max-price" class="form-control"
                                       placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Veľkosti -->
                    @if($allSizes->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Veľkosť</label>
                        <div id="filter-sizes" class="d-flex flex-wrap gap-1">
                            @foreach($allSizes as $size)
                                <button type="button" class="btn btn-sm btn-outline-secondary size-filter-btn"
                                        data-size="{{ $size }}">{{ $size }}</button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Farby -->
                    @if($allColors->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Farba</label>
                        <div id="filter-colors" class="d-flex flex-wrap gap-1">
                            @foreach($allColors as $color)
                                <button type="button" class="btn btn-sm btn-outline-secondary color-filter-btn"
                                        data-color="{{ $color }}">{{ $color }}</button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Použiť filtre
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Vymazať filtre
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </aside>

    <!-- Produkty -->
    <div class="col-lg-9">
        <!-- Zoradenie -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-grid"></i> Naše produkty</h2>
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0">Zoradiť:</label>
                <select id="sort-select" class="form-select form-select-sm" style="width: auto;">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Odporúčané</option>
                    <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Cena ↑</option>
                    <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Cena ↓</option>
                </select>
            </div>
        </div>

        <!-- Grid produktov -->
        @if($products->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="products-grid">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 product-card">
                            <!-- Obrázok -->
                            <div class="product-image-wrapper">
                                <img src="{{ $product->main_image ?? 'https://picsum.photos/seed/p'.$product->product_id.'/400/300' }}"
                                     class="card-img-top product-img" alt="{{ $product->name }}"
                                     onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <!-- Info -->
                                <h5 class="card-title mb-1">{{ $product->name }}</h5>
                                <p class="text-muted small mb-2">{{ $product->brand }}</p>

                                <!-- Farby -->
                                @if($product->available_colors)
                                    <div class="mb-2">
                                        @foreach($product->available_colors as $color)
                                            <span class="color-dot" title="{{ $color }}"
                                                  style="background: {{ $color }}; display:inline-block; width:14px; height:14px; border-radius:50%; margin-right:4px; border:1px solid #ccc"></span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Veľkosti -->
                                @if($product->available_sizes)
                                    <div class="mb-2">
                                        @foreach($product->available_sizes as $size)
                                            <span class="badge bg-secondary">{{ $size }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Cena a akcie -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 text-primary mb-0">{{ number_format($product->base_price, 2) }} €</span>
                                        <div>
                                            <a href="{{ route('product.show', $product->product_id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-primary btn-add-to-cart"
                                                    data-product-id="{{ $product->product_id }}"
                                                    data-product-name="{{ $product->name }}"
                                                    data-product-price="{{ $product->base_price }}"
                                                    data-product-image="{{ $product->main_image ?? 'https://picsum.photos/seed/p'.$product->product_id.'/400/300' }}">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginácia -->
            <div class="mt-4">
                {{ $products->withQueryString()->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Žiadne produkty nenájdené.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vybrané filtre
    let selectedSizes = [];
    let selectedColors = [];

    // Inicializácia z URL parametrov
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sizes')) {
        selectedSizes = urlParams.get('sizes').split(',');
    }
    if (urlParams.get('colors')) {
        selectedColors = urlParams.get('colors').split(',');
    }

    // Označenie vybraných tlačidiel
    function updateFilterButtons() {
        document.querySelectorAll('.size-filter-btn').forEach(btn => {
            if (selectedSizes.includes(btn.dataset.size)) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-secondary');
            } else {
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-outline-secondary');
            }
        });
        document.querySelectorAll('.color-filter-btn').forEach(btn => {
            if (selectedColors.includes(btn.dataset.color)) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-secondary');
            } else {
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-outline-secondary');
            }
        });
    }

    // Filtre veľkostí
    document.querySelectorAll('.size-filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const size = this.dataset.size;
            const index = selectedSizes.indexOf(size);
            if (index > -1) {
                selectedSizes.splice(index, 1);
            } else {
                selectedSizes.push(size);
            }
            updateFilterButtons();
        });
    });

    // Filtre farieb
    document.querySelectorAll('.color-filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const color = this.dataset.color;
            const index = selectedColors.indexOf(color);
            if (index > -1) {
                selectedColors.splice(index, 1);
            } else {
                selectedColors.push(color);
            }
            updateFilterButtons();
        });
    });

    // Pri odoslaní formulára pridáme vybrané filtre
    document.getElementById('filter-form')?.addEventListener('submit', function(e) {
        // Pridáme hidden inputy pre veľkosti a farby
        if (selectedSizes.length > 0) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'sizes';
            input.value = selectedSizes.join(',');
            this.appendChild(input);
        }
        if (selectedColors.length > 0) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'colors';
            input.value = selectedColors.join(',');
            this.appendChild(input);
        }
    });

    // Inicializácia tlačidiel
    updateFilterButtons();

    // Zoradenie
    document.getElementById('sort-select')?.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });

    // Pridanie do košíka
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
});
</script>
@endpush

