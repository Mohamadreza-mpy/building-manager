<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'manager_id',
    'name',
    'address',
    'total_units',
])]
class Building extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['total_units' => 'integer'];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
