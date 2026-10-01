<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'mobile',
    'password',
    'role',
    'created_by',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function managedBuildings(): HasMany
    {
        return $this->hasMany(Building::class, 'manager_id');
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class, 'resident_id');
    }

    public function ownedApartments(): HasMany
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }

    public function createdOwners(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }
}
