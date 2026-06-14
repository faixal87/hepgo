<?php

namespace Tests\Feature;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\VerificationStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Owner;
use App\Models\PortalSetting;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_homepage_is_public_and_uses_malay_content(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Cari Rumah Sewa Luar Kampus Dengan Mudah')
            ->assertSee('Jumlah Rumah Disahkan')
            ->assertSee('Rumah Masih Kosong');
    }

    public function test_homepage_uses_configured_portal_hero_image_when_available(): void
    {
        PortalSetting::current()->update([
            'hero_image_path' => 'portal-settings/contoh-peta.png',
            'hero_image_title' => 'Peta taman pilihan admin',
            'hero_image_caption' => 'Klik untuk lihat imej penuh.',
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('/storage/portal-settings/contoh-peta.png')
            ->assertSee('Peta taman pilihan admin')
            ->assertSee('Klik untuk lihat imej penuh.');
    }

    public function test_listing_page_shows_only_publicly_visible_properties(): void
    {
        $visibleProperty = $this->createProperty([
            'title' => 'Rumah Sewa Taman Siswa',
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::VERIFIED,
        ]);

        $this->createProperty([
            'title' => 'Rumah Belum Disahkan',
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::PENDING,
        ]);

        Facility::create(['name' => 'WiFi']);
        $visibleProperty->facilities()->attach(Facility::first());

        $response = $this->get('/rumah-sewa');

        $response
            ->assertOk()
            ->assertSee('Senarai Rumah Sewa')
            ->assertSee('Rumah Sewa Taman Siswa')
            ->assertDontSee('Rumah Belum Disahkan');
    }

    public function test_verified_property_detail_is_public(): void
    {
        $property = $this->createProperty([
            'title' => 'Rumah Sewa Disahkan',
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::VERIFIED,
        ]);

        $response = $this->get(route('properties.show', $property));

        $response
            ->assertOk()
            ->assertSee('Rumah Sewa Disahkan')
            ->assertSee('Maklumat Pemilik')
            ->assertSee('Nota: HEP menyediakan maklumat ini sebagai rujukan.');
    }

    public function test_unverified_property_detail_is_not_public(): void
    {
        $property = $this->createProperty([
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::PENDING,
        ]);

        $this->get(route('properties.show', $property))
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createProperty(array $overrides = []): Property
    {
        $owner = Owner::create([
            'name' => 'Pemilik Ujian',
            'phone' => '0123456789',
            'whatsapp_number' => '0123456789',
            'verification_status' => VerificationStatus::VERIFIED,
        ]);

        $area = Area::firstOrCreate(
            ['name' => 'Taman Siswa'],
            ['description' => 'Kawasan ujian', 'status' => RecordStatus::ACTIVE]
        );

        $category = Category::firstOrCreate(
            ['name' => 'Rumah Penuh'],
            ['description' => 'Kategori ujian', 'status' => RecordStatus::ACTIVE]
        );

        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => 'Rumah Sewa Ujian',
            'description' => 'Rumah sewa untuk ujian paparan awam.',
            'address' => 'Alamat ujian',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'distance_km' => 2.5,
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::VERIFIED,
            'gender_preference' => GenderPreference::ANY,
            'has_wifi' => true,
        ], $overrides));
    }
}
