@extends('layouts.app')

@section('title', 'Pokladňa')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Domov</a></li>
        <li class="breadcrumb-item active">Pokladňa</li>
    </ol>
</nav>

<h2 class="mb-4"><i class="bi bi-credit-card"></i> Pokladňa</h2>

<div class="row">
    <!-- Formulár -->
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-truck"></i> Doručovacie údaje</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Meno a priezvisko *</label>
                            <input type="text" name="customer_name" id="customer_name"
                                   class="form-control @error('customer_name') is-invalid @enderror"
                                   value="{{ old('customer_name') }}" required minlength="2">
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_email" class="form-label">Email *</label>
                            <input type="email" name="customer_email" id="customer_email"
                                   class="form-control @error('customer_email') is-invalid @enderror"
                                   value="{{ old('customer_email') }}" required>
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Telefón</label>
                        <input type="tel" name="customer_phone" id="customer_phone"
                               class="form-control" value="{{ old('customer_phone') }}">
                    </div>

                    <div class="mb-3">
                        <label for="ship_street" class="form-label">Ulica a číslo domu *</label>
                        <input type="text" name="ship_street" id="ship_street"
                               class="form-control @error('ship_street') is-invalid @enderror"
                               value="{{ old('ship_street') }}" required minlength="5">
                        @error('ship_street')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ship_city" class="form-label">Mesto *</label>
                            <input type="text" name="ship_city" id="ship_city"
                                   class="form-control @error('ship_city') is-invalid @enderror"
                                   value="{{ old('ship_city') }}" required>
                            @error('ship_city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="ship_zip" class="form-label">PSČ *</label>
                            <input type="text" name="ship_zip" id="ship_zip"
                                   class="form-control @error('ship_zip') is-invalid @enderror"
                                   value="{{ old('ship_zip') }}" required minlength="4">
                            @error('ship_zip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="ship_country" class="form-label">Krajina *</label>
                            <select name="ship_country" id="ship_country" class="form-select" required>
                                <option value="SK" {{ old('ship_country', 'SK') == 'SK' ? 'selected' : '' }}>Slovensko</option>
                                <option value="CZ" {{ old('ship_country') == 'CZ' ? 'selected' : '' }}>Česko</option>
                            </select>
                        </div>
                    </div>

                    <!-- Skryté pole pre košík -->
                    <div id="cart-data"></div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-order">
                        <i class="bi bi-check-circle"></i> Odoslať objednávku
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Súhrn objednávky -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Súhrn objednávky</h5>
            </div>
            <div class="card-body">
                <div id="checkout-items"></div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Celkom:</strong>
                    <strong id="checkout-total" class="text-primary">0 €</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('eshop_cart_v2') || '{"items":[]}');
    const itemsEl = document.getElementById('checkout-items');
    const totalEl = document.getElementById('checkout-total');
    const cartDataEl = document.getElementById('cart-data');

    if (cart.items.length === 0) {
        itemsEl.innerHTML = '<div class="alert alert-warning">Košík je prázdny</div>';
        document.getElementById('submit-order').disabled = true;
        return;
    }

    let html = '';
    let total = 0;

    cart.items.forEach((item, idx) => {
        total += item.price * item.qty;
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>${item.name}</strong>
                    ${item.variant ? `<br><small class="text-muted">${item.variant.color || ''} / ${item.variant.size || ''}</small>` : ''}
                    <br><small>${item.qty} x ${item.price.toFixed(2)} €</small>
                </div>
                <div class="fw-bold">${(item.price * item.qty).toFixed(2)} €</div>
            </div>
        `;

        // Pridaj skryté polia pre položky košíka
        cartDataEl.innerHTML += `
            <input type="hidden" name="cart[${idx}][productId]" value="${item.productId}">
            <input type="hidden" name="cart[${idx}][variantId]" value="${item.variantId || ''}">
            <input type="hidden" name="cart[${idx}][qty]" value="${item.qty}">
            <input type="hidden" name="cart[${idx}][price]" value="${item.price}">
        `;
    });

    itemsEl.innerHTML = html;
    totalEl.textContent = total.toFixed(2) + ' €';

    // Klientská validácia
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const name = document.getElementById('customer_name').value.trim();
        const email = document.getElementById('customer_email').value.trim();
        const street = document.getElementById('ship_street').value.trim();
        let errors = [];

        if (name.length < 2) errors.push('Meno je povinné');
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) errors.push('Neplatný email');
        if (street.length < 5) errors.push('Zadajte adresu');

        if (errors.length > 0) {
            e.preventDefault();
            alert('Chyby:\n\n' + errors.join('\n'));
        } else {
            // Po úspešnom odoslaní vyčistiť košík
            localStorage.removeItem('eshop_cart_v2');
        }
    });
});
</script>
@endpush

