@extends('layouts.admin')

@section('title', 'Správa produktov')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam text-primary me-2"></i> Správa produktov</h5>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary rounded-pill px-4">
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
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Vymazať"
                                                data-product-id="{{ $product->product_id }}"
                                                data-product-name="{{ $product->name }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $product->product_id }}"
                                                action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
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
                    {{ $products->links('vendor.pagination.bootstrap-5') }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Zatiaľ neboli pridané žiadne produkty.
                    <a href="{{ route('admin.products.create') }}">Pridať prvý produkt</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Potvrdiť vymazanie
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavrieť"></button>
                </div>
                <div class="modal-body">
                    <p>Naozaj chcete vymazať produkt <strong id="deleteProductName"></strong>?</p>
                    <p class="text-muted small">Táto akcia sa nedá vrátiť späť. Všetky varianty a obrázky budú tiež
                        vymazané.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušiť</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i>Vymazať
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let deleteProductId = null;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            // Attach click handlers to all delete buttons
            document.querySelectorAll('.btn-delete').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    deleteProductId = this.dataset.productId;
                    document.getElementById('deleteProductName').textContent = this.dataset.productName;
                    deleteModal.show();
                });
            });

            // Handle confirm delete
            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (deleteProductId) {
                    document.getElementById('delete-form-' + deleteProductId).submit();
                }
            });
        });
    </script>
@endsection