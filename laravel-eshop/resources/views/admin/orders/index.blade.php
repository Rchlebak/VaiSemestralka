@extends('layouts.admin')

@section('title', 'Správa objednávok')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-receipt"></i> Objednávky</span>
            <span class="badge bg-primary">{{ $orders->total() }} celkom</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Zákazník</th>
                            <th>Email</th>
                            <th>Adresa</th>
                            <th>Položky</th>
                            <th class="text-end">Suma</th>
                            <th>Stav</th>
                            <th class="text-end">Akcie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $statusLabels = [
                                'pending' => 'Čaká',
                                'confirmed' => 'Potvrdená',
                                'shipped' => 'Odoslaná',
                                'delivered' => 'Doručená',
                                'cancelled' => 'Zrušená'
                            ];
                        @endphp
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ (int) $order->order_id }}</strong></td>
                                <td>{{ $order->ship_name }}</td>
                                <td>{{ $order->email }}</td>
                                <td>
                                    <small>{{ $order->ship_city }}, {{ $order->ship_country }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->items->count() }} ks</span>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($order->total_amount, 2) }} €</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order->order_id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Žiadne objednávky
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection