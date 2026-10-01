<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['building_id', 'apartment_id', 'title', 'month', 'amount', 'status', 'paid_at', 'payment_receipt', 'receipt_submitted_at'])]
class Charge extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['month' => 'date', 'amount' => 'decimal:2', 'paid_at' => 'datetime', 'receipt_submitted_at' => 'datetime'];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
