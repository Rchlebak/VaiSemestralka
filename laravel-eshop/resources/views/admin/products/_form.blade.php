{{-- Zdieľaný formulár pre vytvorenie/úpravu produktu --}}

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
    <div class="col-md-6">
        <!-- Názov -->
        <div class="mb-3">
            <label for="name" class="form-label">Názov produktu *</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $product->name ?? '') }}" required minlength="2" maxlength="200">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <!-- Cena -->
        <div class="mb-3">
            <label for="base_price" class="form-label">Cena (€) *</label>
            <input type="number" step="0.01" name="base_price" id="base_price"
                class="form-control @error('base_price') is-invalid @enderror"
                value="{{ old('base_price', $product->base_price ?? '') }}" required min="0.01">
            @error('base_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <!-- SKU -->
        <div class="mb-3">
            <label for="sku_model" class="form-label">SKU / Model</label>
            <input type="text" name="sku_model" id="sku_model" class="form-control"
                value="{{ old('sku_model', $product->sku_model ?? '') }}" maxlength="84">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Značka -->
        <div class="mb-3">
            <label for="brand" class="form-label">Značka</label>
            <input type="text" name="brand" id="brand" class="form-control"
                value="{{ old('brand', $product->brand ?? '') }}" maxlength="200">
        </div>
    </div>
    <div class="col-md-4">
        <!-- Kategória -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Kategória</label>
            <select name="category_id" id="category_id" class="form-select">
                <option value="">-- Bez kategórie --</option>
                @foreach(\App\Models\Category::active()->orderBy('sort_order')->orderBy('name')->get() as $category)
                    <option value="{{ $category->category_id }}" 
                        {{ old('category_id', $product->category_id ?? '') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <!-- Pohlavie -->
        <div class="mb-3">
            <label for="gender" class="form-label">Pohlavie</label>
            <select name="gender" id="gender" class="form-select">
                <option value="unisex" {{ old('gender', $product->gender ?? 'unisex') == 'unisex' ? 'selected' : '' }}>
                    Unisex</option>
                <option value="men" {{ old('gender', $product->gender ?? '') == 'men' ? 'selected' : '' }}>Muži</option>
                <option value="women" {{ old('gender', $product->gender ?? '') == 'women' ? 'selected' : '' }}>Ženy
                </option>
            </select>
        </div>
    </div>
</div>

<!-- URL obrázkov -->
<div class="mb-3">
    <label for="image_urls" class="form-label">URL obrázkov</label>
    <textarea name="image_urls" id="image_urls" class="form-control" rows="3"
        placeholder="Zadajte URL adresy obrázkov, každú na nový riadok..."></textarea>
    <div class="form-text">Každú URL adresu obrázka zadajte na nový riadok. Prvý obrázok bude nastavený ako hlavný.
    </div>
</div>

<!-- Upload súborov - Drag & Drop -->
<div class="mb-3">
    <label class="form-label">Nahrať obrázky z počítača</label>

    <!-- Drag & Drop zóna -->
    <div id="image-drop-zone" class="image-drop-zone">
        <i class="bi bi-cloud-arrow-up"></i>
        <p class="mb-1">Pretiahnite obrázky sem</p>
        <p class="text-muted small mb-0">alebo kliknite pre výber súborov</p>
        <p class="text-muted small">(Max. 5MB, formáty: JPG, PNG, GIF, WebP)</p>
    </div>

    <!-- Skrytý file input -->
    <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp"
        style="display: none;">

    <!-- Náhľady vybraných obrázkov -->
    <div id="image-preview-container" class="image-preview-container" style="display: none;"></div>
</div>

@push('styles')
    <style>
        .image-drop-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .image-drop-zone:hover {
            border-color: #0d6efd;
            background: #e7f1ff;
        }

        .image-drop-zone.drag-over {
            border-color: #198754;
            background: #d1e7dd;
            transform: scale(1.02);
        }

        .image-drop-zone i {
            font-size: 2.5rem;
            color: #6c757d;
            display: block;
            margin-bottom: 0.5rem;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .image-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
            border: 2px solid #dee2e6;
        }

        .image-preview-item .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #dc3545;
            color: white;
            border: 2px solid white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            padding: 0;
        }

        .image-preview-item .remove-btn:hover {
            background: #bb2d3b;
        }

        .image-preview-item .file-name {
            display: block;
            font-size: 10px;
            text-align: center;
            color: #6c757d;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
        }
    </style>
@endpush

<!-- Popis -->
<div class="mb-3">
    <label for="description" class="form-label">Popis</label>
    <textarea name="description" id="description" class="form-control"
        rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<!-- Aktívny -->
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active ?? 1) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">
            Produkt je aktívny (zobrazí sa v obchode)
        </label>
    </div>
</div>

@push('scripts')
    <script>
        // Klientská validácia formulára
        document.getElementById('product-form')?.addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const price = parseFloat(document.getElementById('base_price').value);
            let errors = [];

            if (name.length < 2) {
                errors.push('Názov musí mať aspoň 2 znaky');
            }

            if (isNaN(price) || price <= 0) {
                errors.push('Cena musí byť kladné číslo');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Chyby vo formulári:\n\n' + errors.join('\n'));
            }
        });
    </script>
@endpush