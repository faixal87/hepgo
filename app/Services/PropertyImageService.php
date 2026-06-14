<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Throwable;

class PropertyImageService
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const VERSIONS = [
        'thumbnail' => ['width' => 400, 'quality' => 75, 'suffix' => 'thumb'],
        'medium' => ['width' => 900, 'quality' => 80, 'suffix' => 'medium'],
        'large' => ['width' => 1400, 'quality' => 85, 'suffix' => 'large'],
    ];

    private ImageManager $manager;

    public function __construct(?ImageManager $manager = null)
    {
        $this->manager = $manager ?? new ImageManager(Driver::class);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function storePropertyImage(Property $property, UploadedFile $file, array $options = []): PropertyImage
    {
        $this->validateUploadedFile($file);

        $image = $property->images()->create([
            'image_path' => $file->store("properties/{$property->id}/original", 'public'),
            'caption' => $options['caption'] ?? null,
            'is_thumbnail' => (bool) ($options['is_thumbnail'] ?? false),
            'sort_order' => (int) ($options['sort_order'] ?? 0),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return $this->generateWebpVersionsForImage($image);
    }

    /**
     * @return array<string, string>
     */
    public function generateWebpVersions(Property $property, UploadedFile $file): array
    {
        $this->validateUploadedFile($file);

        return $this->makeVersions(
            $property,
            $file->getRealPath(),
            now()->format('YmdHis').'-'.Str::random(8)
        );
    }

    public function generateWebpVersionsForProperty(Property $property): void
    {
        $property->loadMissing('images');

        $property->images
            ->filter(fn (PropertyImage $image): bool => $this->shouldGenerate($image))
            ->each(fn (PropertyImage $image): PropertyImage => $this->generateWebpVersionsForImage($image));
    }

    public function generateWebpVersionsForImage(PropertyImage $image): PropertyImage
    {
        $property = $image->property;

        if (! $property || blank($image->image_path) || ! Storage::disk('public')->exists($image->image_path)) {
            return $image;
        }

        $absolutePath = Storage::disk('public')->path($image->image_path);
        $mimeType = mime_content_type($absolutePath) ?: null;

        if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return $image;
        }

        try {
            $this->deleteOptimizedVersions($image);

            $versions = $this->makeVersions(
                $property,
                $absolutePath,
                "property-{$property->id}-{$image->id}-".now()->format('YmdHis')
            );

            $source = $this->manager->decodePath($absolutePath);

            $image->forceFill([
                'thumbnail_path' => $versions['thumbnail'],
                'medium_path' => $versions['medium'],
                'large_path' => $versions['large'],
                'original_name' => $image->original_name ?: basename($image->image_path),
                'mime_type' => $mimeType,
                'file_size' => Storage::disk('public')->size($image->image_path),
                'width' => $source->width(),
                'height' => $source->height(),
            ])->saveQuietly();
        } catch (Throwable) {
            return $image;
        }

        return $image->refresh();
    }

    public function deletePropertyImage(PropertyImage $image): void
    {
        $this->deleteOptimizedVersions($image);

        Storage::disk('public')->delete(array_filter([
            $image->image_path,
        ]));
    }

    public function deleteOptimizedVersions(PropertyImage $image): void
    {
        Storage::disk('public')->delete(array_filter([
            $image->thumbnail_path,
            $image->medium_path,
            $image->large_path,
        ]));
    }

    /**
     * @return array<string, string>
     */
    private function makeVersions(Property $property, string $sourcePath, string $baseName): array
    {
        $paths = [];

        foreach (self::VERSIONS as $key => $version) {
            $relativePath = "properties/{$property->id}/{$key}/{$baseName}-{$version['suffix']}.webp";
            $absolutePath = Storage::disk('public')->path($relativePath);

            if (! is_dir(dirname($absolutePath))) {
                mkdir(dirname($absolutePath), 0755, true);
            }

            $this->manager
                ->decodePath($sourcePath)
                ->scaleDown(width: $version['width'])
                ->encode(new WebpEncoder(quality: $version['quality'], strip: true))
                ->save($absolutePath);

            $paths[$key] = $relativePath;
        }

        return $paths;
    }

    private function shouldGenerate(PropertyImage $image): bool
    {
        if (blank($image->image_path) || ! Storage::disk('public')->exists($image->image_path)) {
            return false;
        }

        if (blank($image->thumbnail_path) || blank($image->medium_path) || blank($image->large_path)) {
            return true;
        }

        foreach ([$image->thumbnail_path, $image->medium_path, $image->large_path] as $path) {
            if (! Storage::disk('public')->exists($path)) {
                return true;
            }
        }

        return Storage::disk('public')->lastModified($image->image_path) > min([
            Storage::disk('public')->lastModified($image->thumbnail_path),
            Storage::disk('public')->lastModified($image->medium_path),
            Storage::disk('public')->lastModified($image->large_path),
        ]);
    }

    private function validateUploadedFile(UploadedFile $file): void
    {
        abort_if(
            ! in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true),
            422,
            'Format gambar tidak dibenarkan. Sila muat naik fail JPG, PNG atau WebP sahaja.'
        );

        abort_if(
            $file->getSize() > 5 * 1024 * 1024,
            422,
            'Saiz gambar tidak boleh melebihi 5MB.'
        );
    }
}
