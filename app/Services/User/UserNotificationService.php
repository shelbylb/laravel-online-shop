<?php

namespace App\Services\User;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserNotificationService
{
    public function sendEmailVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
        Log::info('письмо отправлено пользователю с id {id}', ['id' => $user->id]);
    }

    public function sendWelcome(User $user): void
    {
        Mail::to($user->email)
            ->send(new WelcomeMail($user));
    }
}
