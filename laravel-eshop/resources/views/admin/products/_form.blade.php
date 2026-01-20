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
                    <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id ?? '') == $category->category_id ? 'selected' : '' }}>
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

<!-- Správa obrázkov -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-light">
                <label for="main_image" class="form-label mb-0 fw-bold">
                    <i class="bi bi-star-fill text-warning"></i> Hlavný obrázok
                </label>
            </div>
            <div class="card-body text-center">
                <div class="main-image-preview mb-3">
                    <img id="main-preview-img"
                        src="{{ isset($product->main_image) ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : asset($product->main_image)) : 'https://via.placeholder.com/300x200?text=Vyberte+obrázok' }}"
                        class="img-fluid rounded border" style="max-height: 200px; object-fit: contain;">
                </div>
                <input class="form-control" type="file" id="main_image" name="main_image" accept="image/*"
                    onchange="previewMainImage(this)">
                <div class="form-text">Zobrazí sa v zozname produktov. Nahrádza existujúci hlavný obrázok.</div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-light">
                <label for="gallery_images" class="form-label mb-0 fw-bold">
                    <i class="bi bi-images text-primary"></i> Galéria (Ďalšie obrázky)
                </label>
            </div>
            <div class="card-body">
                <input class="form-control mb-3" type="file" id="gallery_images" name="gallery_images[]" multiple
                    accept="image/*" onchange="previewGalleryImages(this)">

                <div id="gallery-preview" class="d-flex flex-wrap gap-2">
                    <!-- Náhľady sa tu objavia -->
                    <p class="text-muted small w-100 text-center py-4 border rounded bg-light">
                        Tu sa zobrazia náhľady vybraných súborov.
                    </p>
                </div>
                <div class="form-text mt-2">Môžete vybrať viacero obrázkov naraz (Ctrl + Click).</div>
            </div>
        </div>
    </div>
</div>

<!-- URL obrázkov (Legacy/Backup) -->
<div class="mb-3">
    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
        data-bs-target="#urlInputCollapse">
        <i class="bi bi-link-45deg"></i> Pridať cez URL (pokročilé)
    </button>
    <div class="collapse mt-2" id="urlInputCollapse">
        <div class="card card-body">
            <label for="image_urls" class="form-label">URL obrázkov</label>
            <textarea name="image_urls" id="image_urls" class="form-control" rows="2"
                placeholder="https://example.com/image.jpg"></textarea>
            <div class="form-text">Každú URL na nový riadok.</div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Klientská validácia
        document.getElementById('product-form')?.addEventListener('submit', function (e) {
            // ... (zachovaná validácia názvu/ceny) ...
        });

        // Náhľad hlavného obrázka
        function previewMainImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('main-preview-img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Náhľad galérie
        function previewGalleryImages(input) {
            const container = document.getElementById('gallery-preview');
            container.innerHTML = ''; // Vyčistiť

            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.className = 'position-relative';
                        div.innerHTML = `
                                 <img src="${e.target.result}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;">
                                 <div class="small text-truncate mt-1" style="max-width: 80px; font-size: 10px;">${file.name}</div>
                             `;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                container.innerHTML = '<p class="text-muted small w-100 text-center py-4 border rounded bg-light">Tu sa zobrazia náhľady.</p>';
            }
        }
    </script>
@endpush