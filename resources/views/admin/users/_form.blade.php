@csrf

<div class="form-group">
    <label for="name" class="form-label">Имя</label>
    <input
        type="text"
        name="name"
        id="name"
        class="form-control"
        value="{{ old('name', $user->name ?? '') }}"
        required
    >
    @error('name')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="email" class="form-label">Email</label>
    <input
        type="email"
        name="email"
        id="email"
        class="form-control"
        value="{{ old('email', $user->email ?? '') }}"
        required
    >
    @error('email')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="password" class="form-label">
        Пароль
        @isset($user)
            <small>(оставь пустым, если не меняется)</small>
        @endisset
    </label>
    <input
        type="password"
        name="password"
        id="password"
        class="form-control"
        {{ isset($user) ? '' : 'required' }}
    >
    @error('password')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
    <input
        type="password"
        name="password_confirmation"
        id="password_confirmation"
        class="form-control"
        {{ isset($user) ? '' : 'required' }}
    >
</div>

<div class="form-group">
    <label class="form-label">Роль</label>

    @php
        $selectedRole = old('role', $user->role->slug ?? null);
    @endphp

    @foreach($roles as $role)
        <div style="margin-bottom:8px;">
            <label>
                <input
                    type="radio"
                    name="role"
                    value="{{ $role->slug }}"
                    {{ $selectedRole === $role->slug ? 'checked' : '' }}
                    required
                >
                {{ $role->name }}
            </label>
        </div>
    @endforeach

    @error('role')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-success">
    Сохранить
</button>
