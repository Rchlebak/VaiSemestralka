@extends('layouts.admin')

@section('title', 'Detail objednávky #' . (int) $order->order_id)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Späť na objednávky
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Položky objednávky --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-cart"></i> Položky objednávky
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produkt</th>
                                <th class="text-center">Veľkosť</th>
                                <th class="text-center">Farba</th>
                                <th class="text-center">Počet</th>
                                <th class="text-end">Cena/ks</th>
                                <th class="text-end">Spolu</th>
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
                                                    <img src="{{ $mainImage->image_path }}" alt="{{ $product->name }}"
                                                        class="me-2 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    <br><small class="text-muted">{{ $product->brand }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Produkt nedostupný</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->variant->size_eu ?? '-' }}</td>
                                    <td class="text-center">{{ $item->variant->color ?? '-' }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }} €</td>
                                    <td class="text-end"><strong>{{ number_format($item->line_total, 2) }} €</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Celkom:</th>
                                <th class="text-end">{{ number_format($order->total_amount, 2) }} €</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Info o objednávke --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informácie
                </div>
                <div class="card-body">
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
                    <p class="mb-2">
                        <strong>Stav:</strong>
                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </p>
                    <p class="mb-2"><strong>Email:</strong> {{ $order->email }}</p>
                    @if($order->user)
                        <p class="mb-2">
                            <strong>Registrovaný:</strong>
                            <a href="{{ route('admin.users.index') }}">{{ $order->user->name }}</a>
                        </p>
                    @endif
                </div>
            </div>

            {{-- Doručovacia adresa --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-geo-alt"></i> Doručovacia adresa
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->ship_name }}</strong></p>
                    <p class="mb-1">{{ $order->ship_street }}</p>
                    <p class="mb-1">{{ $order->ship_zip }} {{ $order->ship_city }}</p>
                    <p class="mb-0">{{ $order->ship_country }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection