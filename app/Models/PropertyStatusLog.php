<?php

namespace App\Models;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'property_id',
    'old_status',
    'new_status',
    'old_verification_status',
    'new_verification_status',
    'changed_by',
    'remarks',
    'created_at',
])]
class PropertyStatusLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_status' => PropertyAvailabilityStatus::class,
            'new_status' => PropertyAvailabilityStatus::class,
            'old_verification_status' => VerificationStatus::class,
            'new_verification_status' => VerificationStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
