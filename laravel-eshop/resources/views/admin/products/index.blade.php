@extends('layouts.admin')

@section('title', 'Správa produktov')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-box-seam"></i> Správa produktov</h4>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nový produkt
        </a>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Obrázok</th>
                            <th>Názov</th>
                            <th>Značka</th>
                            <th>Cena</th>
                            <th>Varianty</th>
                            <th>Stav</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ intval($product->product_id) }}</td>
                                <td>
                                    <img src="{{ $product->main_image ?? 'https://via.placeholder.com/60x60?text=?' }}"
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;"
                                         onerror="this.src='https://via.placeholder.com/60x60?text=?'">
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->sku_model)
                                        <br><small class="text-muted">{{ $product->sku_model }}</small>
                                    @endif
                                </td>
                                <td>{{ $product->brand ?? '-' }}</td>
                                <td class="fw-bold">{{ number_format($product->base_price, 2) }} €</td>
                                <td>
                                    <span class="badge bg-info">{{ $product->variants->count() }} variantov</span>
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success"><i class="bi bi-check"></i> Aktívny</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-x"></i> Neaktívny</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.edit', $product->product_id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Upraviť">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('product.show', $product->product_id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Zobraziť" target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->product_id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Naozaj chcete vymazať tento produkt?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Vymazať">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginácia -->
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Zatiaľ neboli pridané žiadne produkty.
                <a href="{{ route('admin.products.create') }}">Pridať prvý produkt</a>
            </div>
        @endif
    </div>
</div>
@endsection

