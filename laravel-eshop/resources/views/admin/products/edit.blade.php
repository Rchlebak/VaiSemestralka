@extends('layouts.admin')

@section('title', 'Upraviť produkt: ' . $product->name)

@section('content')
    <div class="row">
        <!-- Formulár produktu -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-pencil"></i> Upraviť produkt</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('admin.products._form', ['product' => $product])

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Uložiť zmeny
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Späť
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Obrázky -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Obrázky produktu</h5>
                </div>
                <div class="card-body">
                    @if($product->images->count() > 0)
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            @foreach($product->images as $image)
                                <div class="col">
                                    <div class="card h-100">
                                        <img src="{{ $image->image_path }}" class="card-img-top"
                                            style="height: 120px; object-fit: cover;"
                                            onerror="this.src='https://via.placeholder.com/120?text=Error'">
                                        <div class="card-body p-2 text-center">
                                            @if($image->is_main)
                                                <span class="badge bg-primary mb-1">Hlavný</span>
                                            @else
                                                <form action="{{ route('admin.images.setMain', $image->image_id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary mb-1">
                                                        Nastaviť hlavný
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.images.destroy', $image->image_id) }}" method="POST"
                                                onsubmit="return confirm('Vymazať obrázok?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Žiadne obrázky. Pridajte ich vyššie v sekcii "URL obrázkov".</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Varianty -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-collection"></i> Varianty produktu</h5>
                </div>
                <div class="card-body">
                    <!-- Existujúce varianty -->
                    @if($product->variants->count() > 0)
                        <div class="list-group mb-4">
                            @foreach($product->variants as $variant)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $variant->color }}</strong> / {{ $variant->size_eu }}
                                            <br>
                                            <small class="text-muted">SKU: {{ $variant->sku }}</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- AJAX In-place stock editing --}}
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="number" class="form-control form-control-sm stock-input-ajax"
                                                    style="width: 80px;" value="{{ $variant->stock_qty }}" min="0" max="99999"
                                                    data-variant-id="{{ $variant->variant_id }}"
                                                    title="Zmeňte hodnotu a automaticky sa uloží">
                                                <span class="text-muted small">ks</span>
                                            </div>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.variants.destroy', $variant->variant_id) }}" method="POST"
                                                onsubmit="return confirm('Vymazať variant?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Žiadne varianty. Pridajte prvý variant nižšie.
                        </div>
                    @endif

                    <!-- Pridať nový variant -->
                    <h6>Pridať nový variant</h6>
                    <form action="{{ route('admin.products.variants.store', $product->product_id) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-5">
                                <input type="text" name="color" class="form-control form-control-sm"
                                    placeholder="Farba (napr. black)" required>
                            </div>
                            <div class="col-3">
                                <input type="text" name="size_eu" class="form-control form-control-sm" placeholder="Veľkosť"
                                    required>
                            </div>
                            <div class="col-2">
                                <input type="number" name="stock_qty" class="form-control form-control-sm"
                                    placeholder="Sklad" value="0" min="0">
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-sm btn-success w-100">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">SKU sa vygeneruje automaticky</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection