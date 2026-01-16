@extends('layouts.app')

@section('title', 'Registrácia - E-Shop Tenisiek')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0"><i class="bi bi-person-plus"></i> Registrácia</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
                        @csrf

                        {{-- Meno --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Meno a priezvisko <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required minlength="2" maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>

                        {{-- Telefón --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefón</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone') }}" placeholder="+421 900 123 456">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Heslo --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Heslo <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required minlength="8">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="password-error"></div>
                            <small class="text-muted">Minimálne 8 znakov</small>
                        </div>

                        {{-- Potvrdenie hesla --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Potvrdenie hesla <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                            <div class="invalid-feedback" id="password_confirmation-error"></div>
                        </div>

                        <hr class="my-4">

                        {{-- Adresa (voliteľné) --}}
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> Adresné údaje sú voliteľné, môžete ich doplniť neskôr
                        </p>

                        <div class="mb-3">
                            <label for="address" class="form-label">Ulica a číslo</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="city" class="form-label">Mesto</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                    name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zip" class="form-label">PSČ</label>
                                <input type="text" class="form-control @error('zip') is-invalid @enderror" id="zip"
                                    name="zip" value="{{ old('zip') }}" maxlength="10">
                                @error('zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-plus"></i> Zaregistrovať sa
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Už máte účet?
                            <a href="{{ route('login') }}" class="text-primary">Prihláste sa</a>
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
         * Klientská validácia registračného formulára
         * Kontroluje vstupy pred odoslaním na server
         */
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('register-form');

            form.addEventListener('submit', function (e) {
                let isValid = true;

                // Reset chýb
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // Validácia mena
                const name = document.getElementById('name');
                if (name.value.trim().length < 2) {
                    showError(name, 'name-error', 'Meno musí mať aspoň 2 znaky');
                    isValid = false;
                }

                // Validácia emailu
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    showError(email, 'email-error', 'Zadajte platný email');
                    isValid = false;
                }

                // Validácia hesla
                const password = document.getElementById('password');
                if (password.value.length < 8) {
                    showError(password, 'password-error', 'Heslo musí mať aspoň 8 znakov');
                    isValid = false;
                }

                // Validácia potvrdenia hesla
                const passwordConfirm = document.getElementById('password_confirmation');
                if (password.value !== passwordConfirm.value) {
                    showError(passwordConfirm, 'password_confirmation-error', 'Heslá sa nezhodujú');
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