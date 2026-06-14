<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'hero_image_path',
    'hero_image_title',
    'hero_image_caption',
])]
class PortalSetting extends Model
{
    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'hero_image_title' => 'Peta taman sekitar Jitra',
                'hero_image_caption' => 'Klik untuk buka imej penuh dalam tab baharu.',
            ],
        );
    }

    public function heroImageUrl(): string
    {
        if (filled($this->hero_image_path)) {
            return Storage::disk('public')->url($this->hero_image_path);
        }

        return asset('images/taman-sekitar-jitra.png');
    }

    public function heroImageTitle(): string
    {
        return filled($this->hero_image_title)
            ? $this->hero_image_title
            : 'Peta taman sekitar Jitra';
    }

    public function heroImageCaption(): string
    {
        return filled($this->hero_image_caption)
            ? $this->hero_image_caption
            : 'Klik untuk buka imej penuh dalam tab baharu.';
    }
}
