<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationVerificationJob;
use App\Jobs\SendWelcomeAfterVerificationJob;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VerifyEmailController extends Controller
{
    public function notice(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('products.index', absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            SendWelcomeAfterVerificationJob::dispatch($request->user()->id);
        }

        return redirect()->intended(route('products.index', absolute: false) . '?verified=1');
    }

    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            throw new NotFoundHttpException('пользователь не найден');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('products.index', absolute: false) . '?verified=1');
        }

        SendRegistrationVerificationJob::dispatch($user->id);

        return back()->with('status', 'Ссылка для подтверждения отправлена повторно.');
    }
}
