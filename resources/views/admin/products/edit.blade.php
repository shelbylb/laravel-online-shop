@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Редактирование товара</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.products._form')
        </form>
    </div>
@endsection
