{{-- Zdieľaný formulár pre vytvorenie/úpravu kategórie --}}

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
            <label for="name" class="form-label">Názov kategórie *</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $category->name ?? '') }}" required minlength="2" maxlength="100">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <!-- Slug -->
        <div class="mb-3">
            <label for="slug" class="form-label">URL slug</label>
            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                value="{{ old('slug', $category->slug ?? '') }}" maxlength="100" placeholder="Automaticky podľa názvu">
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Ponechajte prázdne pre automatické generovanie z názvu.</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9">
        <!-- Popis -->
        <div class="mb-3">
            <label for="description" class="form-label">Popis</label>
            <textarea name="description" id="description" class="form-control" rows="3"
                maxlength="500">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="col-md-3">
        <!-- Poradie -->
        <div class="mb-3">
            <label for="sort_order" class="form-label">Poradie</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control"
                value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0">
            <div class="form-text">Nižšie číslo = vyššie poradie</div>
        </div>
    </div>
</div>

<!-- Aktívna -->
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active ?? 1) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">
            Kategória je aktívna (zobrazí sa v obchode)
        </label>
    </div>
</div>