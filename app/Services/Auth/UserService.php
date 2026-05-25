<?php

namespace App\Services\Auth;
use App\Jobs\SendRegistrationVerificationJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function register(string $name,string $email,string $password): User
    {
        $userRoleId = Role::query()
            ->where('slug', Role::ROLE_USER)
            ->value('id');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $userRoleId,
        ]);

        Auth::login($user);

        SendRegistrationVerificationJob::dispatch($user->id);
        return $user;
    }
}
