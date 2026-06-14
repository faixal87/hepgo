<?php

namespace Tests\Feature;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\VerificationStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Owner;
use App\Models\Property;
use App\Services\PropertyImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class PropertyImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_image_upload_generates_three_webp_versions(): void
    {
        Storage::fake('public');

        $property = $this->createProperty();
        $file = UploadedFile::fake()->image('rumah-sewa.jpg', 1600, 900)->size(1024);

        $image = app(PropertyImageService::class)->storePropertyImage($property, $file, [
            'caption' => '<b>Hadapan rumah</b>',
            'is_thumbnail' => true,
        ]);

        $this->assertSame('Hadapan rumah', $image->caption);
        $this->assertSame('image/jpeg', $image->mime_type);
        $this->assertSame(1600, $image->width);
        $this->assertSame(900, $image->height);

        foreach ([$image->thumbnail_path, $image->medium_path, $image->large_path] as $path) {
            $this->assertNotNull($path);
            $this->assertStringEndsWith('.webp', $path);
            Storage::disk('public')->assertExists($path);
        }

        $manager = new ImageManager(Driver::class);

        $this->assertLessThanOrEqual(400, $manager->decodePath(Storage::disk('public')->path($image->thumbnail_path))->width());
        $this->assertLessThanOrEqual(900, $manager->decodePath(Storage::disk('public')->path($image->medium_path))->width());
        $this->assertLessThanOrEqual(1400, $manager->decodePath(Storage::disk('public')->path($image->large_path))->width());

        $this->assertSame($image->thumbnailUrl(), $property->fresh(['images', 'thumbnailImage'])->thumbnailUrl());
    }

    public function test_property_image_delete_removes_original_and_optimized_files(): void
    {
        Storage::fake('public');

        $property = $this->createProperty();
        $image = app(PropertyImageService::class)->storePropertyImage(
            $property,
            UploadedFile::fake()->image('rumah-sewa.png', 1200, 800)->size(900)
        );

        $paths = [
            $image->image_path,
            $image->thumbnail_path,
            $image->medium_path,
            $image->large_path,
        ];

        $image->delete();

        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    private function createProperty(): Property
    {
        $owner = Owner::create([
            'name' => 'Pemilik Gambar',
            'phone' => '0123456789',
            'whatsapp_number' => '0123456789',
            'verification_status' => VerificationStatus::VERIFIED,
        ]);

        $area = Area::create([
            'name' => 'Taman Gambar',
            'status' => RecordStatus::ACTIVE,
        ]);

        $category = Category::create([
            'name' => 'Rumah Gambar',
            'status' => RecordStatus::ACTIVE,
        ]);

        return Property::create([
            'owner_id' => $owner->id,
            'title' => 'Rumah Sewa Gambar',
            'description' => 'Rumah sewa untuk ujian optimasi gambar.',
            'address' => 'Alamat rumah gambar',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::VERIFIED,
            'gender_preference' => GenderPreference::ANY,
        ]);
    }
}
