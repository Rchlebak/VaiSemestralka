@extends('layouts.app')

@section('title', 'Prihlásenie')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0"><i class="bi bi-person-circle"></i> Prihlásenie</h4>
            </div>
            <div class="card-body p-4">
                <p class="text-center text-muted mb-4">Vyberte typ prihlásenia</p>

                <div class="row g-4">
                    <!-- User Login -->
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <div class="display-4 text-primary mb-3">
                                    <i class="bi bi-person"></i>
                                </div>
                                <h5>Zákazník</h5>
                                <p class="text-muted small">Prihláste sa pre sledovanie objednávok</p>
                                <button class="btn btn-outline-primary" disabled>
                                    <i class="bi bi-arrow-right"></i> Prihlásiť sa
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted">Pripravuje sa...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Login -->
                    <div class="col-md-6">
                        <div class="card h-100 border-dark">
                            <div class="card-body text-center">
                                <div class="display-4 text-dark mb-3">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h5>Administrátor</h5>
                                <p class="text-muted small">Správa produktov a objednávok</p>
                                <a href="{{ route('admin.login') }}" class="btn btn-dark">
                                    <i class="bi bi-arrow-right"></i> Admin prihlásenie
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('home') }}">
                    <i class="bi bi-arrow-left"></i> Späť do obchodu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

