<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\User\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWelcomeAfterVerificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $userId
    ) {
        $this->onQueue('users.notifications.welcome');
    }

    public function handle(
        UserNotificationService $notificationService
    ): void {
        $user = User::query()->find($this->userId);

        if (!$user) {
            Log::error("пользователь не найден id:{$this->userId}");
            return;
        }

        $notificationService->sendWelcome($user);
    }
}
