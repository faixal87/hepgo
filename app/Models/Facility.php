<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'icon',
])]
class Facility extends Model
{
    use HasFactory, SoftDeletes;

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_facility');
    }
}
