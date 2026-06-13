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
use Database\Seeders\DemoPropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Sprint6Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_demo_property_seeder_creates_verified_demo_records_with_images(): void
    {
        Storage::fake('public');

        $this->seed(DemoPropertySeeder::class);

        $this->assertDatabaseCount('properties', 10);
        $this->assertSame(10, Property::query()->publiclyVisible()->count());
        $this->assertDatabaseHas('properties', [
            'title' => 'Rumah Sewa Berdekatan POLIMAS',
            'verification_status' => VerificationStatus::VERIFIED->value,
        ]);
        $this->assertDatabaseHas('property_images', [
            'image_path' => 'demo-properties/rumah-sewa-berdekatan-polimas.svg',
            'is_thumbnail' => true,
        ]);

        Storage::disk('public')->assertExists('demo-properties/rumah-sewa-berdekatan-polimas.svg');
    }

    public function test_property_api_contains_android_ready_malay_labels_without_sensitive_owner_data(): void
    {
        $property = $this->createProperty([
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=POLIMAS',
        ]);

        $response = $this->getJson('/api/v1/properties');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $property->id)
            ->assertJsonPath('data.0.status_label', 'Masih Kosong')
            ->assertJsonPath('data.0.jarak_label', 'Jarak anggaran: 1.5 km dari POLIMAS')
            ->assertJsonPath('data.0.penerangan_ringkas', 'Rumah sewa untuk ujian Sprint 6.')
            ->assertJsonMissingPath('data.0.pemilik.no_kad_pengenalan')
            ->assertJsonMissingPath('data.0.deleted_at');
    }

    public function test_public_detail_hides_house_map_button_when_maps_url_is_empty_but_keeps_direction(): void
    {
        $property = $this->createProperty([
            'maps_url' => null,
            'latitude' => 6.2681234,
            'longitude' => 100.4205678,
        ]);

        $response = $this->get(route('properties.show', $property));

        $response
            ->assertOk()
            ->assertDontSee('Peta Rumah')
            ->assertSee('Arah Ke POLIMAS')
            ->assertSee('Jarak anggaran: 1.5 km dari POLIMAS');
    }

    private function createProperty(array $overrides = []): Property
    {
        $owner = Owner::create([
            'name' => 'Pemilik Ujian Sprint 6',
            'phone' => '0123456789',
            'whatsapp_number' => '0123456789',
            'ic_number' => '900101025555',
            'verification_status' => VerificationStatus::VERIFIED->value,
        ]);

        $area = Area::create([
            'name' => 'Taman Siswa',
            'description' => 'Kawasan ujian',
            'status' => RecordStatus::ACTIVE->value,
        ]);

        $category = Category::create([
            'name' => 'Rumah Penuh',
            'description' => 'Kategori ujian',
            'status' => RecordStatus::ACTIVE->value,
        ]);

        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => 'Rumah Sewa Ujian Sprint 6',
            'description' => 'Rumah sewa untuk ujian Sprint 6.',
            'address' => 'POLIMAS Jitra Kedah',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'distance_km' => 1.5,
            'status' => PropertyAvailabilityStatus::AVAILABLE->value,
            'verification_status' => VerificationStatus::VERIFIED->value,
            'gender_preference' => GenderPreference::ANY->value,
        ], $overrides));
    }
}
