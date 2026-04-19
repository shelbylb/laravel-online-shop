<?php

namespace App\Listeners;

use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
