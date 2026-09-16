<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Building;
use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AnnouncementService
{
    public function list(Building $building): LengthAwarePaginator
    {
        return $building->announcements()->with('creator')->latest()->paginate(20);
    }

    public function create(Building $building, array $data, User $user): Announcement
    {
        $announcement = $building->announcements()->create([...$data, 'created_by' => $user->id])->load('creator');
        User::query()->whereHas('apartments', fn ($query) => $query->where('building_id', $building->id))->each(fn (User $resident) => $resident->notify(new InAppNotification('announcement_created', $announcement->title, 'اطلاعیه جدیدی برای ساختمان شما منتشر شد.', ['announcement_id' => $announcement->id])));

        return $announcement;
    }
}
