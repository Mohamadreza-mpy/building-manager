<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OwnerService
{
    public function list(User $user, ?string $search = null): LengthAwarePaginator
    {
        return User::query()
            ->where('role', 'owner')
            ->when($user->role !== 'admin', fn ($query) => $query->where(function ($scope) use ($user) {
                $scope->where('created_by', $user->id)
                    ->orWhereHas('ownedApartments.building', fn ($buildings) => $buildings->where('manager_id', $user->id));
            }))
            ->when($search, fn ($query) => $query->where(function ($scope) use ($search) {
                $scope->where('name', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%");
            }))
            ->withCount('ownedApartments')
            ->latest()
            ->paginate(20);
    }

    public function create(User $manager, array $data): User
    {
        unset($data['password_confirmation']);

        return User::create([...$data, 'role' => 'owner', 'created_by' => $manager->id])->loadCount('ownedApartments');
    }
}
