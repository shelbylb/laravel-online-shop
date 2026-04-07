<?php

namespace App\Services\User;

use App\DTOs\User\UserFilterDTO;
use App\DTOs\User\UserUpsertDTO;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function paginateWithFilters(UserFilterDTO $dto, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->when(
                $dto->search,
                function (Builder $query, string $search) {
                    $query->where(function (Builder $subQuery) use ($search) {
                        $subQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
                }
            )
            ->when(
                $dto->role,
                function (Builder $query, string $role) {
                    $query->whereHas('roles', function (Builder $roleQuery) use ($role) {
                        $roleQuery->where('slug', $role);
                    });
                }
            )
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(UserUpsertDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::query()->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ]);

            $this->syncRoles($user, $dto->roles);

            return $user->load('roles');
        });
    }

    public function update(User $user, UserUpsertDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto) {
            $payload = [
                'name' => $dto->name,
                'email' => $dto->email,
            ];

            if ($dto->password !== null) {
                $payload['password'] = Hash::make($dto->password);
            }

            $user->update($payload);

            $this->syncRoles($user, $dto->roles);

            return $user->load('roles');
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->roles()->detach();
            $user->delete();
        });
    }

    private function syncRoles(User $user, array $roleSlugs): void
    {
        $roleIds = Role::query()
            ->whereIn('slug', $roleSlugs)
            ->pluck('id')
            ->all();

        $user->roles()->sync($roleIds);
    }
}
