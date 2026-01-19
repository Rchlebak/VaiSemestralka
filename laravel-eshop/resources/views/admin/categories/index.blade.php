@extends('layouts.admin')

@section('title', 'Kategórie')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-tags"></i> Kategórie produktov</h4>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nová kategória
            </a>
        </div>
        <div class="card-body">
            @if($categories->count() > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Poradie</th>
                            <th>Názov</th>
                            <th>Slug</th>
                            <th>Produkty</th>
                            <th>Stav</th>
                            <th style="width: 150px;">Akcie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td class="text-center">{{ $category->sort_order }}</td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                    @if($category->description)
                                        <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                    @endif
                                </td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $category->products_count }}</span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Aktívna</span>
                                    @else
                                        <span class="badge bg-danger">Neaktívna</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $category->category_id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Vymazať kategóriu {{ $category->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $categories->links() }}
            @else
                <div class="text-center py-5">
                    <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Zatiaľ nemáte žiadne kategórie.</p>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Vytvoriť prvú kategóriu
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection