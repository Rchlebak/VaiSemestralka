@extends('layouts.app')

@section('title', 'E-Shop Tenisiek - Najlepšie tenisky')

@section('content')
<!-- Hero sekcia - Premium dizajn -->
<section class="hero-banner position-relative overflow-hidden mb-5">
    <div class="hero-background"></div>
    <div class="hero-content text-center py-5">
        <h1 class="display-3 fw-bold text-white mb-3">
            <span class="text-gradient">Prémiové Tenisky</span>
        </h1>
        <p class="lead text-white-50 mb-4">Objavte najnovšie modely od popredných značiek</p>
        <div class="hero-stats d-flex justify-content-center gap-4 mb-4">
            <div class="stat-item">
                <span class="stat-number">500+</span>
                <span class="stat-label">Produktov</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">20+</span>
                <span class="stat-label">Značiek</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">24h</span>
                <span class="stat-label">Doručenie</span>
            </div>
        </div>
    </div>
</section>

<!-- Kategórie - Rýchly výber -->
<section class="categories-section mb-5">
    <div class="row g-3">
        <!-- Muži -->
        <div class="col-md-4">
            <a href="{{ route('home', ['gender' => 'men']) }}" class="category-card category-men text-decoration-none">
                <div class="category-icon">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h3>Pre mužov</h3>
                <p>Moderné štýly pre pánov</p>
                <span class="category-arrow"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
        <!-- Ženy -->
        <div class="col-md-4">
            <a href="{{ route('home', ['gender' => 'women']) }}" class="category-card category-women text-decoration-none">
                <div class="category-icon">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h3>Pre ženy</h3>
                <p>Elegantné a pohodlné</p>
                <span class="category-arrow"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
        <!-- Unisex -->
        <div class="col-md-4">
            <a href="{{ route('home', ['gender' => 'unisex']) }}" class="category-card category-unisex text-decoration-none">
                <div class="category-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3>Unisex</h3>
                <p>Pre každého</p>
                <span class="category-arrow"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

<!-- Kategórie produktov -->
@php
    $categories = \App\Models\Category::active()->withCount('products')->orderBy('sort_order')->get();
@endphp
@if($categories->count() > 0)
<section class="product-categories mb-5">
    <h2 class="section-title mb-4"><i class="bi bi-collection"></i> Kategórie</h2>
    <div class="categories-scroll">
        <a href="{{ route('home') }}" class="category-chip {{ !request('category_id') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Všetky
        </a>
        @foreach($categories as $category)
            <a href="{{ route('home', ['category_id' => $category->category_id]) }}" 
               class="category-chip {{ request('category_id') == $category->category_id ? 'active' : '' }}">
                {{ $category->name }}
                <span class="badge">{{ $category->products_count }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

<div class="row">
    <!-- Filtre sidebar -->
    <aside class="col-lg-3 mb-4">
        <div class="filter-card">
            <div class="filter-header">
                <i class="bi bi-sliders"></i> Filtre
            </div>
            <div class="filter-body">
                <form action="{{ route('home') }}" method="GET" id="filter-form">
                    <!-- Vyhľadávanie -->
                    <div class="filter-group">
                        <label class="filter-label">Vyhľadávanie</label>
                        <div class="search-input-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" name="q" id="search-input" class="form-control"
                                   placeholder="Názov, značka..." value="{{ request('q') }}">
                        </div>
                    </div>

                    <!-- Pohlavie -->
                    <div class="filter-group">
                        <label class="filter-label">Pohlavie</label>
                        <div class="gender-buttons">
                            <button type="button" class="gender-btn {{ request('gender') == 'men' ? 'active' : '' }}" data-gender="men">
                                <i class="bi bi-gender-male"></i> Muži
                            </button>
                            <button type="button" class="gender-btn {{ request('gender') == 'women' ? 'active' : '' }}" data-gender="women">
                                <i class="bi bi-gender-female"></i> Ženy
                            </button>
                            <button type="button" class="gender-btn {{ request('gender') == 'unisex' ? 'active' : '' }}" data-gender="unisex">
                                <i class="bi bi-gender-ambiguous"></i> Unisex
                            </button>
                        </div>
                        <input type="hidden" name="gender" id="gender-input" value="{{ request('gender') }}">
                    </div>

                    <!-- Cena -->
                    <div class="filter-group">
                        <label class="filter-label">Cenový rozsah</label>
                        <div class="price-range">
                            <input type="number" name="min_price" id="min-price" class="form-control"
                                   placeholder="Od €" value="{{ request('min_price') }}">
                            <span class="price-separator">-</span>
                            <input type="number" name="max_price" id="max-price" class="form-control"
                                   placeholder="Do €" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Veľkosti -->
                    @if($allSizes->count() > 0)
                    <div class="filter-group">
                        <label class="filter-label">Veľkosť</label>
                        <div class="size-grid" id="filter-sizes">
                            @foreach($allSizes as $size)
                                <button type="button" class="size-btn size-filter-btn"
                                        data-size="{{ $size }}">{{ $size }}</button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Farby -->
                    @if($allColors->count() > 0)
                    <div class="filter-group">
                        <label class="filter-label">Farba</label>
                        <div class="color-grid" id="filter-colors">
                            @foreach($allColors as $color)
                                <button type="button" class="color-btn color-filter-btn"
                                        data-color="{{ $color }}" title="{{ $color }}">
                                    <span class="color-preview" style="background: {{ $color }}"></span>
                                    {{ $color }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-filter-apply">
                            <i class="bi bi-check-lg"></i> Použiť filtre
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-filter-clear">
                            <i class="bi bi-x-lg"></i> Vymazať
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </aside>

    <!-- Produkty -->
    <div class="col-lg-9">
        <!-- Záhlavie -->
        <div class="products-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0"><i class="bi bi-grid"></i> Naše produkty</h2>
                <span class="products-count">{{ $products->total() }} produktov</span>
            </div>
            <div class="sort-wrapper">
                <select id="sort-select" class="form-select">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Odporúčané</option>
                    <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Cena: od najnižšej</option>
                    <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Cena: od najvyššej</option>
                    <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>Názov: A-Z</option>
                </select>
            </div>
        </div>

        <!-- Grid produktov -->
        @if($products->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="products-grid">
                @foreach($products as $product)
                    <div class="col">
                        <div class="product-card-modern">
                            <!-- Obrázok -->
                            <div class="product-image">
                                @if($product->gender == 'men')
                                    <span class="product-badge badge-men">Muži</span>
                                @elseif($product->gender == 'women')
                                    <span class="product-badge badge-women">Ženy</span>
                                @endif
                                <img src="{{ $product->main_image ?? 'https://picsum.photos/seed/p'.$product->product_id.'/400/300' }}"
                                     alt="{{ $product->name }}"
                                     onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                                <div class="product-overlay">
                                    <a href="{{ route('product.show', $product->product_id) }}" class="btn-view">
                                        <i class="bi bi-eye"></i> Zobraziť
                                    </a>
                                </div>
                            </div>

                            <div class="product-info">
                                <span class="product-brand">{{ $product->brand ?? 'Premium' }}</span>
                                <h3 class="product-name">{{ $product->name }}</h3>

                                <!-- Veľkosti -->
                                @if($product->available_sizes)
                                    <div class="product-sizes">
                                        @foreach(array_slice($product->available_sizes, 0, 5) as $size)
                                            <span class="size-tag">{{ $size }}</span>
                                        @endforeach
                                        @if(count($product->available_sizes) > 5)
                                            <span class="size-tag more">+{{ count($product->available_sizes) - 5 }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="product-footer">
                                    <div class="product-price">
                                        <span class="price-current">{{ number_format($product->base_price, 2) }} €</span>
                                    </div>
                                    <button class="btn-add-cart btn-add-to-cart"
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
                @endforeach
            </div>

            <!-- Paginácia -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                <h3>Žiadne produkty</h3>
                <p>Pre zadané filtre sme nenašli žiadne produkty.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Zobraziť všetky produkty</a>
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
    let selectedGender = document.getElementById('gender-input')?.value || '';

    // Inicializácia z URL parametrov
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sizes')) {
        selectedSizes = urlParams.get('sizes').split(',');
    }
    if (urlParams.get('colors')) {
        selectedColors = urlParams.get('colors').split(',');
    }

    // Pohlavie tlačidlá
    document.querySelectorAll('.gender-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const gender = this.dataset.gender;
            document.querySelectorAll('.gender-btn').forEach(b => b.classList.remove('active'));
            if (selectedGender === gender) {
                selectedGender = '';
            } else {
                this.classList.add('active');
                selectedGender = gender;
            }
            document.getElementById('gender-input').value = selectedGender;
        });
    });

    // Označenie vybraných tlačidiel
    function updateFilterButtons() {
        document.querySelectorAll('.size-filter-btn').forEach(btn => {
            btn.classList.toggle('active', selectedSizes.includes(btn.dataset.size));
        });
        document.querySelectorAll('.color-filter-btn').forEach(btn => {
            btn.classList.toggle('active', selectedColors.includes(btn.dataset.color));
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
