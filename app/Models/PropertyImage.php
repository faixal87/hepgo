<?php

namespace App\Models;

use App\Services\PropertyImageService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'property_id',
    'image_path',
    'thumbnail_path',
    'medium_path',
    'large_path',
    'original_name',
    'mime_type',
    'file_size',
    'width',
    'height',
    'caption',
    'is_thumbnail',
    'sort_order',
])]
class PropertyImage extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saved(function (PropertyImage $image): void {
            if (! $image->is_thumbnail || blank($image->property_id)) {
                return;
            }

            self::query()
                ->where('property_id', $image->property_id)
                ->where('id', '!=', $image->getKey())
                ->update(['is_thumbnail' => false]);
        });

        static::deleting(function (PropertyImage $image): void {
            app(PropertyImageService::class)->deletePropertyImage($image);
        });
    }

    protected function casts(): array
    {
        return [
            'is_thumbnail' => 'boolean',
            'sort_order' => 'integer',
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function setCaptionAttribute(?string $value): void
    {
        $this->attributes['caption'] = filled($value) ? strip_tags($value) : null;
    }

    public function url(): string
    {
        return $this->publicUrl($this->image_path);
    }

    public function thumbnailUrl(): string
    {
        return $this->publicUrl($this->thumbnail_path ?: $this->medium_path ?: $this->image_path);
    }

    public function mediumUrl(): string
    {
        return $this->publicUrl($this->medium_path ?: $this->large_path ?: $this->image_path);
    }

    public function largeUrl(): string
    {
        return $this->publicUrl($this->large_path ?: $this->medium_path ?: $this->image_path);
    }

    private function publicUrl(?string $path): string
    {
        if (blank($path)) {
            return '';
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }
}
