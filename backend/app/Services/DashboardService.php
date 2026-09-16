<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Apartment;
use App\Models\Building;
use App\Models\Charge;
use App\Models\ResidentRequest;
use App\Models\User;

class DashboardService
{
    public function for(User $user): array
    {
        return $user->role === 'resident'
            ? $this->residentDashboard($user)
            : $this->managementDashboard($user);
    }

    private function managementDashboard(User $user): array
    {
        $buildings = Building::query()
            ->when($user->role !== 'admin', fn ($query) => $query->where('manager_id', $user->id));

        $buildingIds = (clone $buildings)->select('id');
        $monthlyCharges = Charge::query()
            ->whereIn('building_id', clone $buildingIds)
            ->whereDate('month', now()->startOfMonth());

        return [
            'role' => $user->role,
            'buildings_count' => (clone $buildings)->count(),
            'apartments_count' => Apartment::query()->whereIn('building_id', clone $buildingIds)->count(),
            'pending_requests_count' => ResidentRequest::query()
                ->where('status', 'pending')
                ->whereHas('apartment', fn ($query) => $query->whereIn('building_id', clone $buildingIds))
                ->count(),
            'monthly_charges_count' => (clone $monthlyCharges)->count(),
            'monthly_charges_total' => (string) (clone $monthlyCharges)->sum('amount'),
        ];
    }

    private function residentDashboard(User $user): array
    {
        $apartments = Apartment::query()
            ->with('building:id,name,address')
            ->where('resident_id', $user->id)
            ->orderBy('id')
            ->get();

        $apartmentIds = $apartments->pluck('id');
        $buildingIds = $apartments->pluck('building_id')->unique();
        $currentApartment = $apartments->first();

        return [
            'role' => 'resident',
            'current_apartment' => $currentApartment ? [
                ...$currentApartment->only(['id', 'building_id', 'number', 'floor', 'area']),
                'building' => $currentApartment->building?->only(['id', 'name', 'address']),
            ] : null,
            'unpaid_charges_count' => Charge::query()->whereIn('apartment_id', $apartmentIds)->whereIn('status', ['pending', 'overdue'])->count(),
            'unpaid_charges_total' => (string) Charge::query()->whereIn('apartment_id', $apartmentIds)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'announcements_count' => Announcement::query()->whereIn('building_id', $buildingIds)->count(),
            'recent_announcements' => Announcement::query()->whereIn('building_id', $buildingIds)->latest()->limit(3)->get(['id', 'building_id', 'title', 'created_at'])->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'building_id' => $announcement->building_id,
                'title' => $announcement->title,
                'created_at' => $announcement->created_at?->toISOString(),
            ])->all(),
            'requests_count' => ResidentRequest::query()->whereIn('apartment_id', $apartmentIds)->count(),
            'pending_requests_count' => ResidentRequest::query()->whereIn('apartment_id', $apartmentIds)->whereIn('status', ['pending', 'processing'])->count(),
        ];
    }
}
