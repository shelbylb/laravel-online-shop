<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /** Показ формы регистрации */
    public function showRegistrationForm(): Factory|View
    {
        return view('auth.register');
    }

    /** Обработка регистрации */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $dto = RegisterDTO::fromRequest($request);
        $user = $this
            ->userService
            ->register($dto);

        return redirect()
            ->route('login.form')
            ->with('status', 'Регистрация прошла успешно');
    }

    /** Показ формы авторизации */
    public function showLoginForm(): Factory|View
    {
        return view('auth.login');
    }

    /** обработка логина */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile');
        }

        return back()->withErrors(['email' => 'Неверные учетные данные']);
    }

    /** обработка логаута */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login.form');
    }
}
