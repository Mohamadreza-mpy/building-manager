<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'manager_id',
    'name',
    'address',
    'total_units',
])]
class Building extends Model
{
    use HasFactory;


    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }


    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
