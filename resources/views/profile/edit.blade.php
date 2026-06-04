@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="container py-4">
        <div class="mx-auto" style="max-width: 900px;">
            <h1 class="h3 mb-4">Профиль</h1>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">
                    Данные профиля успешно обновлены.
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success">
                    Пароль успешно изменён.
                </div>
            @endif

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success">
                    Новое письмо для подтверждения email отправлено.
                </div>
            @endif

            @if (! auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning shadow-sm mb-4">
                    <h2 class="h5 mb-2">Email не подтверждён</h2>

                    <p class="mb-3">
                        Подтвердите ваш email, чтобы получить доступ к оформлению заказов.
                        Письмо с ссылкой для подтверждения будет отправлено на адрес
                        <strong>{{ auth()->user()->email }}</strong>.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            Отправить письмо ещё раз
                        </button>
                    </form>
                </div>
            @endif

            <div class="card card-body shadow-sm mb-4">
                <h2 class="h4 mb-2">Личные данные</h2>
                <p class="text-muted mb-4">
                    Здесь можно изменить имя и адрес электронной почты.
                </p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                            autocomplete="name"
                        >
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                            autocomplete="username"
                        >
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-light border">
                            Ваш адрес электронной почты не подтверждён.
                        </div>
                    @endif

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>

            <div class="card card-body shadow-sm mb-4">
                <h2 class="h4 mb-2">Смена пароля</h2>
                <p class="text-muted mb-4">
                    Укажите текущий пароль и введите новый.
                </p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                            autocomplete="current-password"
                        >
                        @if($errors->updatePassword->has('current_password'))
                            <div class="invalid-feedback">
                                {{ $errors->updatePassword->first('current_password') }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                            autocomplete="new-password"
                        >
                        @if($errors->updatePassword->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->updatePassword->first('password') }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение нового пароля</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                            autocomplete="new-password"
                        >
                        @if($errors->updatePassword->has('password_confirmation'))
                            <div class="invalid-feedback">
                                {{ $errors->updatePassword->first('password_confirmation') }}
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Обновить пароль
                        </button>
                    </div>
                </form>
            </div>

            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <h2 class="h4 text-danger mb-3">Удаление аккаунта</h2>
                    <p class="text-muted mb-4">
                        После удаления аккаунта все связанные с ним данные будут удалены без возможности восстановления.
                        Для подтверждения введите текущий пароль.
                    </p>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Текущий пароль</label>
                            <input
                                type="password"
                                id="delete_password"
                                name="password"
                                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                                placeholder="Введите пароль"
                            >
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->userDeletion->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-start">
                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Вы уверены, что хотите удалить аккаунт? Это действие нельзя отменить.')"
                            >
                                Удалить аккаунт
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
