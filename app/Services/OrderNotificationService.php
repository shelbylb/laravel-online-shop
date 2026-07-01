<?php

namespace App\Services;

use App\Mail\OrderAcceptedMail;
use App\Mail\OrderCreatedForStaffMail;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class OrderNotificationService
{
    public function sendOrderCreated(Order $order): void
    {
        $sentCount = 0;
        $attemptCount = 0;

        if ($order->user?->email) {
            $attemptCount++;

            if ($this->sendSafely($order->user->email, new OrderAcceptedMail($order), $order)) {
                $sentCount++;
            }
        }

        $staffEmails = User::query()
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', [
                    Role::ROLE_ADMIN,
                    Role::ROLE_MANAGER,
                ]);
            })
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        foreach ($staffEmails as $email) {
            $attemptCount++;

            if ($this->sendSafely($email, new OrderCreatedForStaffMail($order), $order)) {
                $sentCount++;
            }
        }

        if ($attemptCount > 0 && $sentCount === 0) {
            throw new RuntimeException("Order {$order->order_number} notifications were not sent.");
        }
    }

    public function sendOrderShipped(Order $order): void
    {
        if (!$order->user?->email) {
            return;
        }

        if (!$this->sendSafely($order->user->email, new OrderShippedMail($order), $order)) {
            throw new RuntimeException("Order {$order->order_number} shipped notification was not sent.");
        }
    }

    private function sendSafely(string $email, Mailable $mailable, Order $order): bool
    {
        try {
            Mail::to($email)->send($mailable);
            return true;
        } catch (Throwable $exception) {
            Log::error('Order notification mail failed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'email' => $email,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
