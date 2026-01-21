@extends('layouts.app')

@section('title', 'E-Shop Tenisiek - Domov')

@section('content')
    <!-- Hero sekcia - Premium dizajn -->
    <section class="hero-banner position-relative overflow-hidden mb-5">
        <div class="hero-background"
            style="background-image: url('https://images.unsplash.com/photo-1556906781-9a412961c28c?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
        </div>
        <div class="hero-content text-center py-5">
            <h1 class="display-3 fw-bold text-white mb-3">
                <span class="text-gradient">Nová Kolekcia 2026</span>
            </h1>
            <p class="lead text-white-50 mb-4">Objavte najnovšie trendy a limitované edície od svetových značiek</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5">Nakupovať</a>
                <a href="{{ route('products.index', ['gender' => 'men']) }}"
                    class="btn btn-outline-light btn-lg px-4">Muži</a>
                <a href="{{ route('products.index', ['gender' => 'women']) }}"
                    class="btn btn-outline-light btn-lg px-4">Ženy</a>
            </div>
        </div>
    </section>

    <!-- Smart Categories Grid (Footshop Style) -->
    <section class="container mb-5">
        <h2 class="text-center mb-4 fw-bold">Vyberte si kategóriu</h2>
        <div class="row g-3">
            <!-- 1. Tenisky (Main) -->
            <div class="col-md-6 col-lg-8">
                <a href="{{ route('products.index', ['category_id' => $categories['Tenisky']->category_id ?? '']) }}"
                    class="card bg-dark text-white border-0 h-100 overflow-hidden category-tile-large">
                    <img src="https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?auto=format&fit=crop&w=800&q=80"
                        class="card-img opacity-50" alt="Tenisky">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                        <h3 class="card-title fw-bold display-6">Tenisky</h3>
                        <p class="card-text">Všetky modely na jednom mieste</p>
                    </div>
                </a>
            </div>

            <!-- 2. Nová Kolekcia -->
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                    class="card bg-secondary text-white border-0 h-100 overflow-hidden category-tile">
                    <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=800&q=80"
                        class="card-img opacity-50" alt="Nová Kolekcia">
                    <div
                        class="card-img-overlay d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <i class="bi bi-stars display-4 mb-3 text-warning"></i>
                        <h3 class="fw-bold">Nová Kolekcia</h3>
                        <p>Horúce novinky</p>
                    </div>
                </a>
            </div>

            <!-- 3. Pánske -->
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['gender' => 'men']) }}"
                    class="card bg-dark text-white border-0 h-100 overflow-hidden category-tile">
                    <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80"
                        class="card-img opacity-50" alt="Pánske">
                    <div class="card-img-overlay d-flex align-items-center justify-content-center">
                        <h4 class="fw-bold text-uppercase">Pánske</h4>
                    </div>
                </a>
            </div>

            <!-- 4. Dámske -->
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['gender' => 'women']) }}"
                    class="card bg-dark text-white border-0 h-100 overflow-hidden category-tile">
                    <img src="https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=600&q=80"
                        class="card-img opacity-50" alt="Dámske">
                    <div class="card-img-overlay d-flex align-items-center justify-content-center">
                        <h4 class="fw-bold text-uppercase">Dámske</h4>
                    </div>
                </a>
            </div>

            <!-- 5. Akcie (Sale) -->
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['sort' => 'price-asc']) }}"
                    class="card bg-danger text-white border-0 h-100 overflow-hidden category-tile">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=600&q=80"
                        class="card-img opacity-50" alt="Akcie">
                    <div class="card-img-overlay d-flex flex-column justify-content-center align-items-center">
                        <i class="bi bi-percent display-5 mb-2 text-warning"></i>
                        <h4 class="fw-bold">Akcie</h4>
                        <small>Najlepšie ceny</small>
                    </div>
                </a>
            </div>

            <!-- 6. Doplnky -->
            <div class="col-6 col-md-3">
                <a href="{{ route('products.index', ['category_id' => $categories['Doplnky']->category_id ?? '']) }}"
                    class="card bg-dark text-white border-0 h-100 overflow-hidden category-tile">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80"
                        class="card-img opacity-50" alt="Doplnky">
                    <div class="card-img-overlay d-flex flex-column justify-content-center align-items-center">
                        <i class="bi bi-backpack display-5 mb-2"></i>
                        <h4 class="fw-bold">Doplnky</h4>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
        <section class="container mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold m-0">Top Modely</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Zobraziť všetko</a>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                @foreach($featuredProducts as $product)
                    <div class="col">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <style>
        /* Custom Grid Styling */
        .category-tile,
        .category-tile-large {
            transition: transform 0.3s ease;
        }

        .category-tile:hover,
        .category-tile-large:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img {
            height: 100%;
            object-fit: cover;
            filter: brightness(0.6);
            transition: filter 0.3s ease;
        }

        .card:hover .card-img {
            filter: brightness(0.4);
        }
    </style>
@endsection