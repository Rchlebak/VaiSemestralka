<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Shop Tenisiek')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Vlastné CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css?v=' . time()) }}">

    @stack('styles')
</head>

<body
    style="background-color: #f8f9fa !important; background-image: radial-gradient(circle at 0% 0%, rgba(13, 110, 253, 0.25) 0%, transparent 60%), radial-gradient(circle at 100% 0%, rgba(220, 53, 69, 0.2) 0%, transparent 60%), radial-gradient(circle at 50% 100%, rgba(13, 202, 240, 0.15) 0%, transparent 60%) !important; background-attachment: fixed;">
    <!-- Navigácia -->
    <header class="mb-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm"
            style="background-color: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(12px);">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                    <i class="bi bi-shoe"></i> E-Shop Tenisiek
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">
                                <i class="bi bi-house"></i> Domov
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        @auth
                            {{-- Prihlásený používateľ --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="bi bi-person"></i> Môj profil
                                        </a></li>
                                    @if(Auth::user()->isAdmin())
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                                <i class="bi bi-speedometer2"></i> Admin panel
                                            </a></li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right"></i> Odhlásiť sa
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            {{-- Neprihlásený používateľ --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Prihlásiť sa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus"></i> Registrácia
                                </a>
                            </li>
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white ms-2 px-3" href="#" id="open-cart">
                                <i class="bi bi-cart3"></i> Košík (<span id="cart-count">0</span>)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash správy -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Hlavný obsah -->
    <main class="container py-3">
        @yield('content')
    </main>

    <!-- Košík (drawer) -->
    <aside id="cart-drawer" class="cart-drawer">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-cart3"></i> Váš košík</h5>
                <button id="close-cart" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="cart-items"></div>
            <div class="mt-3 border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Medzisúčet:</strong>
                    <span id="cart-subtotal" class="fw-bold">0 €</span>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-primary w-100" id="checkout-btn">
                    <i class="bi bi-credit-card"></i> Pokračovať k platbe
                </a>
            </div>
        </div>
    </aside>

    <!-- Overlay -->
    <div id="cart-overlay" class="cart-overlay"></div>

    <!-- Päta -->
    <footer class="bg-light py-4 mt-5 border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h6>E-Shop Tenisiek</h6>
                    <p class="text-muted small">Najlepšie tenisky pre každú príležitosť.</p>
                </div>
                <div class="col-md-4">
                    <h6>Kontakt</h6>
                    <p class="text-muted small">
                        <i class="bi bi-envelope"></i> info@eshop-tenisiek.sk<br>
                        <i class="bi bi-phone"></i> +421 900 123 456
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="text-muted small">© {{ date('Y') }} E-Shop Tenisiek</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vlastné JS -->
    <script src="{{ asset('js/cart.js?v=' . time()) }}"></script>
    <script src="{{ asset('js/product-filter.js?v=' . time()) }}"></script>
    <script src="{{ asset('js/app.js?v=' . time()) }}"></script>

    @stack('scripts')
</body>

</html>