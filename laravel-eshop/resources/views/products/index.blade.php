@extends('layouts.app')

@section('title', 'E-Shop Tenisiek - Produkty')

@section('content')

<!-- Kategórie produktov -->
@php
    $categories = \App\Models\Category::active()->withCount('products')->orderBy('sort_order')->get();
@endphp
@if($categories->count() > 0)
<section class="product-categories mb-4 mt-4">
    <div class="categories-scroll">
        <a href="{{ route('products.index') }}" class="category-chip {{ !request('category_id') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Všetky
        </a>
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category_id' => $category->category_id]) }}" 
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
                <form action="{{ route('products.index') }}" method="GET" id="filter-form">
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

                    <!-- Značky -->
                    @if(isset($allBrands) && $allBrands->count() > 0)
                    <div class="filter-group">
                        <label class="filter-label">Značka</label>
                        <div class="brand-grid d-flex flex-wrap gap-2" id="filter-brands">
                            @foreach($allBrands as $brand)
                                <button type="button" class="btn btn-sm btn-outline-secondary brand-filter-btn"
                                        data-brand="{{ $brand }}">
                                    {{ $brand }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                        <a href="{{ route('products.index') }}" class="btn btn-filter-clear">
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
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnovšie</option>
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
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            <!-- Paginácia -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->withQueryString()->links('vendor.pagination.bootstrap-5') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                <h3>Žiadne produkty</h3>
                <p>Pre zadané filtre sme nenašli žiadne produkty.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Zobraziť všetky produkty</a>
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
    let selectedBrands = [];
    let selectedGender = document.getElementById('gender-input')?.value || '';

    // Inicializácia z URL parametrov
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sizes')) {
        selectedSizes = urlParams.get('sizes').split(',');
    }
    if (urlParams.get('colors')) {
        selectedColors = urlParams.get('colors').split(',');
    }
    if (urlParams.get('brands')) {
        selectedBrands = urlParams.get('brands').split(',');
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
        document.querySelectorAll('.brand-filter-btn').forEach(btn => {
            const isActive = selectedBrands.includes(btn.dataset.brand);
            btn.classList.toggle('active', isActive);
            btn.classList.toggle('btn-primary', isActive); // Use primary color for active brand
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('btn-outline-secondary', !isActive);
        });
    }

    // Funkcia na odoslanie filtrov
    function applyFilters() {
        const form = document.getElementById('filter-form');
        if (!form) return;

        // Helper pre pridanie inputov
        const addInput = (name, values) => {
            if (values.length > 0) {
                // Odstránime staré inputy ak existujú
                const oldInput = form.querySelector(`input[name="${name}"]`);
                if (oldInput) oldInput.remove();

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = values.join(',');
                form.appendChild(input);
            }
        };

        addInput('sizes', selectedSizes);
        addInput('colors', selectedColors);
        addInput('brands', selectedBrands);

        form.submit();
    }

    // Filtre značiek
    document.querySelectorAll('.brand-filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const brand = this.dataset.brand;
            const index = selectedBrands.indexOf(brand);
            if (index > -1) {
                selectedBrands.splice(index, 1);
            } else {
                selectedBrands.push(brand);
            }
            updateFilterButtons();
            applyFilters(); // Auto-submit
        });
    });

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
            applyFilters(); // Auto-submit
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
            applyFilters(); // Auto-submit
        });
    });

    // Pri manuálnom odoslaní (ak by ostalo tlačidlo)
    document.querySelector('.btn-filter-apply')?.addEventListener('click', function(e) {
        e.preventDefault();
        applyFilters();
    });

    // Odstránime pôvodný listener na submit, lebo riešime cez applyFilters
    // (Ponecháme prázdny ak by niečo iné triggerovalo, ale applyFilters volá submit priamo)
    
    // Inicializácia tlačidiel
    updateFilterButtons();

    // Zoradenie
    document.getElementById('sort-select')?.addEventListener('change', function() {
        // Zachovať existujúce filtre aj pri zmene sortu
        // Najjednoduchšie je pridať sort parameter do form action a odoslať
        // Alebo len upraviť URL ako predtým, ale musíme pridať aj filtre
        const url = new URL(window.location);
        url.searchParams.set('sort', this.value);
        // Filtre už sú v URL, takž stačí reload
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
