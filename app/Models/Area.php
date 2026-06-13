<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'description',
    'distance_from_campus',
    'status',
])]
class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'distance_from_campus' => 'decimal:2',
            'status' => RecordStatus::class,
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
