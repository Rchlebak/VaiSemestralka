@extends('layouts.admin')

@section('title', 'Prihlásenie do administrácie')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    .login-card {
        max-width: 400px;
        margin: 100px auto;
    }
</style>
@endpush

@section('content')
{{-- Override admin layout for login --}}
@endsection

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - E-Shop Tenisiek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white text-center py-4">
                    <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Admin Panel</h4>
                    <small>E-Shop Tenisiek</small>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.login.submit') }}" method="POST" id="login-form">
                        @csrf

                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-person"></i> Používateľské meno
                            </label>
                            <input type="text" name="username" id="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username') }}" required autofocus>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-key"></i> Heslo
                            </label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Prihlásiť sa
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Späť do obchodu
                    </a>
                </div>
            </div>

            <div class="text-center mt-3 text-white-50">
                <small>Predvolené heslo: admin123</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Klientská validácia
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username) {
                e.preventDefault();
                alert('Zadajte používateľské meno');
                return;
            }

            if (!password) {
                e.preventDefault();
                alert('Zadajte heslo');
                return;
            }
        });
    </script>
</body>
</html>

