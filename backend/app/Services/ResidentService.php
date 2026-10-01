<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResidentService
{
    public function list(User $user, ?string $search = null): LengthAwarePaginator
    {
        return User::query()
            ->where('role', 'resident')
            ->when($user->role !== 'admin', fn ($query) => $query->where(function ($scope) use ($user) {
                $scope->where('created_by', $user->id)
                    ->orWhereHas('apartments.building', fn ($buildings) => $buildings->where('manager_id', $user->id));
            }))
            ->when($search, fn ($query) => $query->where(function ($scope) use ($search) {
                $scope->where('name', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%");
            }))
            ->withCount('apartments')
            ->latest()
            ->paginate(20);
    }

    public function create(User $manager, array $data): User
    {
        unset($data['password_confirmation']);

        return User::create([...$data, 'role' => 'resident', 'created_by' => $manager->id])->loadCount('apartments');
    }
}
