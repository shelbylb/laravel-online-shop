<?php

namespace App\Services\Auth;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function register(RegisterDTO $dto): User
    {
        $user = new User();
        $user->name = $dto->name;
        $user->last_name = $dto->last_name;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();

        // TODO: после изучения очередей добавить событие для отправки приветственного письма:
        // event(new Registered($user));

        return $user;
    }
}
