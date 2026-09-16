<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class ExpenseService
{
    public function list(Building $building): LengthAwarePaginator
    {
        return $building->expenses()->with('creator')->latest()->paginate(20);
    }

    public function create(Building $building, array $data, User $user): Expense
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        if ($image instanceof UploadedFile) {
            $data['image'] = $image->store('receipts', 'public');
        }

return $building->expenses()->create([...$data, 'created_by' => $user->id])->load('creator');
    }
}
