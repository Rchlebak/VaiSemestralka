@extends('layouts.app')

@section('title', 'Môj profil - E-Shop Tenisiek')

@section('content')
    <div class="row">
        <div class="col-lg-4 mb-4">
            {{-- Profilová karta --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Môj profil</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-circle mb-3 mx-auto">
                        <span class="avatar-initials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'primary' }}">
                        {{ $user->isAdmin() ? 'Administrátor' : 'Zákazník' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Osobné údaje --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Osobné údaje</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" id="profile-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Meno a priezvisko</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefón</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Ulica a číslo</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" value="{{ old('address', $user->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="city" class="form-label">Mesto</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                    name="city" value="{{ old('city', $user->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zip" class="form-label">PSČ</label>
                                <input type="text" class="form-control @error('zip') is-invalid @enderror" id="zip"
                                    name="zip" value="{{ old('zip', $user->zip) }}">
                                @error('zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Uložiť zmeny
                        </button>
                    </form>
                </div>
            </div>

            {{-- Zmena hesla --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-key"></i> Zmena hesla</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}" id="password-form">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Aktuálne heslo</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Nové heslo</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required minlength="8">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimálne 8 znakov</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Potvrdenie nového hesla</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key"></i> Zmeniť heslo
                        </button>
                    </form>
                </div>
            </div>

            {{-- Moje objednávky --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bag-check"></i> Moje objednávky</h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                            <div class="card mb-3 border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Objednávka #{{ (int)$order->order_id }}</strong>
                                        <span class="text-muted ms-2">{{ $order->created_at ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Čaká na spracovanie',
                                                'confirmed' => 'Potvrdená',
                                                'shipped' => 'Odoslaná',
                                                'delivered' => 'Doručená',
                                                'cancelled' => 'Zrušená'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Doručovacia adresa --}}
                                    <div class="mb-3 p-2 bg-light rounded">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-geo-alt"></i> Doručovacia adresa:</small>
                                        <strong>{{ $order->ship_name }}</strong><br>
                                        {{ $order->ship_street }}, {{ $order->ship_zip }} {{ $order->ship_city }}, {{ $order->ship_country }}
                                    </div>

                                    {{-- Položky objednávky --}}
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produkt</th>
                                                <th class="text-center">Veľkosť</th>
                                                <th class="text-center">Farba</th>
                                                <th class="text-center">Ks</th>
                                                <th class="text-end">Cena</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->items as $item)
                                                <tr>
                                                    <td>
                                                        @if($item->variant && $item->variant->product)
                                                            @php
                                                                $product = $item->variant->product;
                                                                $mainImage = $product->images->where('is_main', 1)->first() ?? $product->images->first();
                                                            @endphp
                                                            <div class="d-flex align-items-center">
                                                                @if($mainImage)
                                                                    <img src="{{ $mainImage->image_path }}" 
                                                                         alt="{{ $product->name }}" 
                                                                         class="me-2 rounded" 
                                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                                @endif
                                                                <span>{{ $product->name }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Produkt nedostupný</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $item->variant->size_eu ?? '-' }}</td>
                                                    <td class="text-center">{{ $item->variant->color ?? '-' }}</td>
                                                    <td class="text-center">{{ $item->qty }}</td>
                                                    <td class="text-end">{{ number_format($item->line_total, 2) }} €</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <th colspan="4" class="text-end">Celkom:</th>
                                                <th class="text-end">{{ number_format($order->total_amount, 2) }} €</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-bag-x display-4 text-muted"></i>
                            <p class="text-muted mt-2">Zatiaľ nemáte žiadne objednávky</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="bi bi-cart"></i> Nakupovať
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 80px;
            height: 80px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-initials {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
@endsection