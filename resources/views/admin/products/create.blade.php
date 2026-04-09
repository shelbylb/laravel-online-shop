@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Создание товара</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('admin.products._form')
        </form>
    </div>
@endsection
