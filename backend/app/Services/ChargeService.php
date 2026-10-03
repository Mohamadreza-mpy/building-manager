<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Charge;
use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChargeService
{
    public function list(Building $building): LengthAwarePaginator
    {
        return $building->charges()->with(['apartment.resident', 'apartment.owner'])->latest('month')->paginate(20);
    }

    public function listForResident(User $user): LengthAwarePaginator
    {
        return Charge::query()->with('apartment')->whereHas('apartment', fn ($query) => $query->where('resident_id', $user->id))->latest('month')->paginate(20);
    }

    public function listForOwner(User $user): LengthAwarePaginator
    {
        return Charge::query()->with('apartment')->whereHas('apartment', fn ($query) => $query->where('owner_id', $user->id))->latest('month')->paginate(20);
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
        return $charge->load(['apartment.resident', 'apartment.owner']);
    }

    public function update(Charge $charge, array $data): Charge
    {
        $charge->update($data);

        return $charge->refresh()->load(['apartment.resident', 'apartment.owner']);
    }

    public function approveReceipt(Charge $charge): Charge
    {
        $charge->update(['status' => 'paid', 'paid_at' => now()]);
        $charge->loadMissing('apartment.owner');
        $charge->apartment->owner?->notify(new InAppNotification(
            'charge_receipt_approved',
            'پرداخت شارژ تأیید شد',
            "رسید شارژ {$charge->title} واحد {$charge->apartment->number} تأیید شد.",
            ['charge_id' => $charge->id],
        ));

        return $charge->refresh()->load(['apartment.resident', 'apartment.owner']);
    }

    public function submitReceipt(Charge $charge, UploadedFile $image): Charge
    {
        if ($charge->payment_receipt) {
            Storage::disk('public')->delete($charge->payment_receipt);
        }

        $charge->update([
            'payment_receipt' => $image->store('charge-receipts', 'public'),
            'receipt_submitted_at' => now(),
        ]);

        $charge->loadMissing('building.manager', 'apartment');
        $charge->building->manager?->notify(new InAppNotification(
            'charge_receipt_submitted',
            'رسید پرداخت جدید',
            "برای شارژ {$charge->title} واحد {$charge->apartment->number} رسید ارسال شده است.",
            ['charge_id' => $charge->id],
        ));

        return $charge->refresh()->load(['apartment.resident', 'apartment.owner']);
    }
}
