<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\RegisterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
        $dto = RegisterDto::fromRequest($request);
        $user = $this
            ->userService
            ->register($dto);

        return redirect()
            ->route('login.form')
            ->with('status', 'Регистрация прошла успешно');
    }
}
