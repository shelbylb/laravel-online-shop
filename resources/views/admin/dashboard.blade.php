@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <h1 class="page-title">Dashboard</h1>

    <div class="cards-grid">
        <div class="card">
            <div>Всего пользователей</div>
            <div class="stat-value">{{ $data->usersCount }}</div>
        </div>

        <div class="card">
            <div>Администраторы</div>
            <div class="stat-value">{{ $data->adminsCount }}</div>
        </div>

        <div class="card">
            <div>Менеджеры</div>
            <div class="stat-value">{{ $data->managersCount }}</div>
        </div>
    </div>
@endsection
