<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\User\UserFilterDTO;
use App\DTOs\User\UserUpsertDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function index(UserIndexRequest $request): View
    {
        $this->authorize('viewAny', User::class);

        $filter = UserFilterDTO::fromRequest($request);

        $users = $this->userService->paginateWithFilters($filter);
        $roles = Role::query()->get();

        return view('admin.users.index', compact('users', 'roles', 'filter'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::query()->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $dto = UserUpsertDTO::fromStoreRequest($request);

        $this->userService->create($dto);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно создан');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('roles');
        $roles = Role::query()->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $dto = UserUpsertDTO::fromUpdateRequest($request);

        $this->userService->update($user, $dto);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлён');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно удалён');
    }
}
