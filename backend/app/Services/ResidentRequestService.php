<?php

namespace App\Services;

use App\Models\ResidentRequest;
use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResidentRequestService
{
    public function list(User $user): LengthAwarePaginator
    {
        return ResidentRequest::query()->with('apartment.building')->when($user->role === 'resident', fn ($q) => $q->whereHas('apartment', fn ($a) => $a->where('resident_id', $user->id)))->when($user->role === 'manager', fn ($q) => $q->whereHas('apartment.building', fn ($b) => $b->where('manager_id', $user->id)))->latest()->paginate(20);
    }

    public function create(array $data): ResidentRequest
    {
        return ResidentRequest::create($data)->load('apartment.building');
    }

    public function respond(ResidentRequest $item, array $data): ResidentRequest
    {
        $item->update($data);

        $item = $item->refresh()->load('apartment.building');
        $item->apartment->resident?->notify(new InAppNotification('request_answered', 'پاسخ درخواست', 'مدیر به درخواست شما پاسخ داد.', ['request_id' => $item->id]));

        return $item;
    }
}
