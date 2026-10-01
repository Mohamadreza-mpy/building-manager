<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ChargeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'building_id' => $this->building_id, 'apartment_id' => $this->apartment_id, 'title' => $this->title, 'month' => $this->month?->format('Y-m'), 'amount' => $this->amount, 'status' => $this->status, 'paid_at' => $this->paid_at?->toISOString(), 'payment_receipt_url' => $this->payment_receipt ? $request->getSchemeAndHttpHost().Storage::disk('public')->url($this->payment_receipt) : null, 'receipt_submitted_at' => $this->receipt_submitted_at?->toISOString(), 'apartment' => new ApartmentResource($this->whenLoaded('apartment')), 'created_at' => $this->created_at?->toISOString(), 'updated_at' => $this->updated_at?->toISOString()];
    }
}
