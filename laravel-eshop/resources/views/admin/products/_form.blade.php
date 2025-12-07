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
    <div class="col-md-6">
        <!-- Značka -->
        <div class="mb-3">
            <label for="brand" class="form-label">Značka</label>
            <input type="text" name="brand" id="brand" class="form-control"
                   value="{{ old('brand', $product->brand ?? '') }}" maxlength="200">
        </div>
    </div>
    <div class="col-md-6">
        <!-- Pohlavie -->
        <div class="mb-3">
            <label for="gender" class="form-label">Pohlavie</label>
            <select name="gender" id="gender" class="form-select">
                <option value="unisex" {{ old('gender', $product->gender ?? 'unisex') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                <option value="men" {{ old('gender', $product->gender ?? '') == 'men' ? 'selected' : '' }}>Muži</option>
                <option value="women" {{ old('gender', $product->gender ?? '') == 'women' ? 'selected' : '' }}>Ženy</option>
            </select>
        </div>
    </div>
</div>

<!-- URL obrázkov -->
<div class="mb-3">
    <label for="image_urls" class="form-label">URL obrázkov</label>
    <textarea name="image_urls" id="image_urls" class="form-control" rows="3"
              placeholder="Zadajte URL adresy obrázkov, každú na nový riadok..."></textarea>
    <div class="form-text">Každú URL adresu obrázka zadajte na nový riadok. Prvý obrázok bude nastavený ako hlavný.</div>
</div>

<!-- Upload súborov -->
<div class="mb-3">
    <label for="images" class="form-label">Nahrať obrázky z počítača</label>
    <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
    <div class="form-text">Môžete vybrať viacero obrázkov naraz. Max. 5MB na obrázok.</div>
</div>

<!-- Popis -->
<div class="mb-3">
    <label for="description" class="form-label">Popis</label>
    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<!-- Aktívny -->
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $product->is_active ?? 1) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">
            Produkt je aktívny (zobrazí sa v obchode)
        </label>
    </div>
</div>

@push('scripts')
<script>
// Klientská validácia formulára
document.getElementById('product-form')?.addEventListener('submit', function(e) {
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

