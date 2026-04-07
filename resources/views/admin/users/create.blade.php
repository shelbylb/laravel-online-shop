@extends('layouts.admin', ['title' => 'Создать пользователя'])

@section('content')
    <div class="page-actions">
        <h1 class="page-title" style="margin-bottom:0;">Создать пользователя</h1>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Назад
        </a>
    </div>

    <div class="card">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @include('admin.users._form')
        </form>
    </div>
@endsection
