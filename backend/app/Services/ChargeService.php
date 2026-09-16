<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Charge;
use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChargeService
{
    public function list(Building $building): LengthAwarePaginator
    {
        return $building->charges()->with('apartment.resident')->latest('month')->paginate(20);
    }

    public function listForResident(User $user): LengthAwarePaginator
    {
        return Charge::query()->with('apartment')->whereHas('apartment', fn ($query) => $query->where('resident_id', $user->id))->latest('month')->paginate(20);
    }

    public function create(Building $building, array $data): Charge
    {
        $data['month'] .= '-01';

        $charge = $building->charges()->create($data)->load('apartment.resident');
        $charge->apartment->resident?->notify(new InAppNotification('charge_created', 'شارژ جدید', 'یک شارژ جدید برای واحد شما ثبت شد.', ['charge_id' => $charge->id]));

        return $charge;
    }

    public function find(Charge $charge): Charge
    {
        return $charge->load('apartment.resident');
    }

    public function update(Charge $charge, array $data): Charge
    {
        $charge->update($data);

        return $charge->refresh()->load('apartment.resident');
    }
}
