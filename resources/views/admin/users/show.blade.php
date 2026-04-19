@extends('layouts.admin', ['title' => 'Просмотр пользователя'])

@section('content')
    <div class="page-actions">
        <h1 class="page-title" style="margin-bottom:0;">Пользователь #{{ $user->id }}</h1>

        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Назад
            </a>

            @can('update', $user)
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    Редактировать
                </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div style="margin-bottom:16px;">
            <strong>Имя:</strong><br>
            {{ $user->name }}
        </div>

        <div style="margin-bottom:16px;">
            <strong>Email:</strong><br>
            {{ $user->email }}
        </div>

        <div style="margin-bottom:16px;">
            <strong>Роли:</strong><br>
            @if($user->role)
                <span class="badge">{{ $user->role->name }}</span>
            @else
                <span class="badge">Без роли</span>
            @endif
        </div>

        <div style="margin-bottom:16px;">
            <strong>Создан:</strong><br>
            {{ $user->created_at?->format('d.m.Y H:i') }}
        </div>

        <div>
            <strong>Обновлён:</strong><br>
            {{ $user->updated_at?->format('d.m.Y H:i') }}
        </div>
    </div>
@endsection
