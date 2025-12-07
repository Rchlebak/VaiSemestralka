@extends('layouts.admin')

@section('title', 'Nový produkt')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nový produkt</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
            @csrf

            @include('admin.products._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Vytvoriť produkt
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Späť
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

