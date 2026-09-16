<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['apartment_id', 'title', 'description', 'status', 'response'])]
class ResidentRequest extends Model
{
    protected $table = 'requests';

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
