@extends('layouts.admin')

@section('title', 'Nová kategória')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nová kategória</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                @include('admin.categories._form')

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Vytvoriť kategóriu
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Späť
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection