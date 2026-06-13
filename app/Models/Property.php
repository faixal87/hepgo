<?php

namespace App\Models;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'owner_id',
    'title',
    'description',
    'address',
    'area_id',
    'category_id',
    'price',
    'deposit',
    'distance_km',
    'latitude',
    'longitude',
    'maps_url',
    'status',
    'verification_status',
    'gender_preference',
    'total_rooms',
    'total_bathrooms',
    'max_occupants',
    'has_parking',
    'has_wifi',
    'has_washing_machine',
    'has_kitchen',
    'has_aircond',
    'remarks',
    'created_by',
    'verified_by',
    'verified_at',
])]
class Property extends Model
{
    use HasFactory, SoftDeletes;

    public const POLIMAS_DESTINATION = 'POLIMAS, Jitra, Kedah, Malaysia';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'deposit' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'status' => PropertyAvailabilityStatus::class,
            'verification_status' => VerificationStatus::class,
            'gender_preference' => GenderPreference::class,
            'total_rooms' => 'integer',
            'total_bathrooms' => 'integer',
            'max_occupants' => 'integer',
            'has_parking' => 'boolean',
            'has_wifi' => 'boolean',
            'has_washing_machine' => 'boolean',
            'has_kitchen' => 'boolean',
            'has_aircond' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'property_facility');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function thumbnailImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)
            ->where('is_thumbnail', true)
            ->orderBy('sort_order');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PropertyStatusLog::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PropertyReport::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(PropertyBookmark::class);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('verification_status', VerificationStatus::VERIFIED->value)
            ->whereIn('status', [
                PropertyAvailabilityStatus::AVAILABLE->value,
                PropertyAvailabilityStatus::FULL->value,
            ]);
    }

    public function thumbnailUrl(): ?string
    {
        $path = $this->thumbnailImage?->image_path
            ?? $this->images->sortBy('sort_order')->first()?->image_path;

        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }

    public function whatsappUrl(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->owner?->whatsapp_number);

        if (blank($number)) {
            return null;
        }

        if (str_starts_with($number, '0')) {
            $number = '6'.$number;
        }

        $message = rawurlencode("Salam, saya berminat dengan rumah sewa: {$this->title}");

        return "https://wa.me/{$number}?text={$message}";
    }

    public function mapsUrl(): ?string
    {
        if (filled($this->maps_url)) {
            return $this->maps_url;
        }

        if (filled($this->latitude) && filled($this->longitude)) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        return null;
    }

    public function getDirectionUrlAttribute(): ?string
    {
        $origin = null;

        if (filled($this->latitude) && filled($this->longitude)) {
            $origin = "{$this->latitude},{$this->longitude}";
        } elseif (filled($this->address)) {
            $origin = $this->address;
        }

        if (blank($origin)) {
            return null;
        }

        return 'https://www.google.com/maps/dir/?api=1'
            .'&origin='.rawurlencode($origin)
            .'&destination='.rawurlencode(self::POLIMAS_DESTINATION)
            .'&travelmode=driving';
    }
}
