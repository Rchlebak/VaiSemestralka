@extends('layouts.app')

@section('title', 'Objednávka úspešná')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow text-center">
            <div class="card-body py-5">
                <div class="display-1 text-success mb-4">
                    <i class="bi bi-check-circle"></i>
                </div>

                <h2 class="text-success mb-3">Objednávka bola úspešne vytvorená!</h2>

                <p class="lead text-muted mb-4">
                    Ďakujeme za vašu objednávku. Na email
                    <strong>{{ $order->email }}</strong>
                    vám bude zaslaný potvrdzovací email.
                </p>

                <div class="bg-light p-4 rounded mb-4">
                    <h5>Číslo objednávky</h5>
                    <div class="display-6 text-primary">
                        ORD{{ str_pad(intval($order->order_id), 6, '0', STR_PAD_LEFT) }}
                    </div>
                </div>

                <div class="row text-start mb-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-truck"></i> Doručovacia adresa</h6>
                        <p class="mb-0">
                            {{ $order->ship_name }}<br>
                            {{ $order->ship_street }}<br>
                            {{ $order->ship_zip }} {{ $order->ship_city }}<br>
                            {{ $order->ship_country }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-receipt"></i> Súhrn</h6>
                        @foreach($order->items as $item)
                            <div class="d-flex justify-content-between">
                                <span>
                                    {{ $item->qty }}x
                                    {{ $item->variant?->product?->name ?? 'Produkt' }}
                                </span>
                                <span>{{ number_format($item->line_total, 2) }} €</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Celkom:</span>
                            <span class="text-primary">{{ number_format($order->total_amount, 2) }} €</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-left"></i> Pokračovať v nákupe
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

