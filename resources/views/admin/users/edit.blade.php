@extends('layouts.admin', ['title' => 'Редактировать пользователя'])

@section('content')
    <div class="page-actions">
        <h1 class="page-title" style="margin-bottom:0;">
            Редактировать пользователя #{{ $user->id }}
        </h1>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Назад
        </a>
    </div>

    <div class="card">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            @include('admin.users._form')
        </form>
    </div>
@endsection
