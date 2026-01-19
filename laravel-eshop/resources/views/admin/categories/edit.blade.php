@extends('layouts.admin')

@section('title', 'Upraviť kategóriu: ' . $category->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-pencil"></i> Upraviť kategóriu</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.categories._form', ['category' => $category])

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Uložiť zmeny
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Späť
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection