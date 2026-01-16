@extends('layouts.app')

@section('title', 'Prihlásenie - E-Shop Tenisiek')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Prihlásenie</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>

                        {{-- Heslo --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>

                        {{-- Zapamätaj ma --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Zapamätať si ma</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Prihlásiť sa
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">Nemáte účet?
                            <a href="{{ route('register') }}" class="text-primary">Zaregistrujte sa</a>
                        </p>
                        <p class="mb-0 small">
                            <a href="{{ route('admin.login') }}" class="text-muted">
                                <i class="bi bi-shield-lock"></i> Admin prihlásenie
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /**
         * Klientská validácia prihlasovacieho formulára
         */
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('login-form');

            form.addEventListener('submit', function (e) {
                let isValid = true;

                // Reset chýb
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // Validácia emailu
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    showError(email, 'email-error', 'Zadajte platný email');
                    isValid = false;
                }

                // Validácia hesla
                const password = document.getElementById('password');
                if (password.value.length === 0) {
                    showError(password, 'password-error', 'Heslo je povinné');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function showError(input, errorId, message) {
                input.classList.add('is-invalid');
                const errorEl = document.getElementById(errorId);
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.style.display = 'block';
                }
            }
        });
    </script>
@endpush