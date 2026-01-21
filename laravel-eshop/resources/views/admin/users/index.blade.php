@extends('layouts.admin')

@section('title', 'Správa používateľov')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people"></i> Používatelia</span>
            <span class="badge bg-primary">{{ $users->total() }} celkom</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Meno</th>
                            <th>Email</th>
                            <th>Rola</th>
                            <th>Telefón</th>
                            <th>Mesto</th>
                            <th class="text-end">Akcie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ (int) $user->id }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">Zákazník</span>
                                    @endif
                                </td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>{{ $user->city ?? '-' }}</td>
                                <td class="text-end">
                                    {{-- Zmena hesla --}}
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#passwordModal{{ $user->id }}">
                                        <i class="bi bi-key"></i>
                                    </button>

                                    {{-- Zmazať (len pre ne-adminov) --}}
                                    @if(!$user->isAdmin())
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $user->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            {{-- Modal pre zmazanie --}}
                            @if(!$user->isAdmin())
                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Odstrániť používateľa</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Naozaj chcete odstrániť používateľa <strong>{{ $user->name }}</strong>?</p>
                                                <p class="text-muted mb-0">Táto akcia je nevratná.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Zrušiť</button>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i> Odstrániť
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Modal pre zmenu hesla --}}
                            <div class="modal fade" id="passwordModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Zmena hesla - {{ $user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="password{{ $user->id }}" class="form-label">Nové
                                                        heslo</label>
                                                    <input type="password" class="form-control" id="password{{ $user->id }}"
                                                        name="password" required minlength="6">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation{{ $user->id }}"
                                                        class="form-label">Potvrdiť heslo</label>
                                                    <input type="password" class="form-control"
                                                        id="password_confirmation{{ $user->id }}" name="password_confirmation"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Zrušiť</button>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="bi bi-key"></i> Zmeniť heslo
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Žiadni používatelia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection