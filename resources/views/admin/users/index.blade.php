@extends('layouts.admin', ['title' => 'Пользователи'])

@section('content')
    <div class="page-actions">
        <h1 class="page-title" style="margin-bottom:0;">Пользователи</h1>

        @can('create', \App\Models\User::class)
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                Создать пользователя
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-bottom:20px;">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="filters-grid">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="search" class="form-label">Поиск</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        class="form-control"
                        placeholder="Имя или email"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="role" class="form-label">Роль</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">Все роли</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') === $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Применить</button>
                </div>

                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Сбросить</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Роли</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role)
                            <span class="badge">{{ $user->role->name }}</span>
                        @else
                            <span class="badge">Без роли</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary btn-sm">
                            Просмотр
                        </a>

                        @can('update', $user)
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                Редактировать
                            </a>
                        @endcan

                        @can('delete', $user)
                            <form action="{{ route('admin.users.destroy', $user) }}"
                                  method="POST"
                                  style="display:inline-block;">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Удалить пользователя?')">
                                    Удалить
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Пользователи не найдены</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
@endsection
