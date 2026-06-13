<?php

namespace Tests\Feature;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Enums\VerificationStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint5Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_property_direction_url_uses_coordinates_when_available(): void
    {
        $property = $this->createProperty([
            'latitude' => 6.2681234,
            'longitude' => 100.4205678,
        ]);

        $this->assertStringContainsString('origin=6.2681234%2C100.4205678', $property->direction_url);
        $this->assertStringContainsString('destination=POLIMAS%2C%20Jitra%2C%20Kedah%2C%20Malaysia', $property->direction_url);
        $this->assertStringContainsString('travelmode=driving', $property->direction_url);
    }

    public function test_public_report_form_can_submit_aduan(): void
    {
        $property = $this->createProperty();

        $response = $this->post('/aduan', [
            'property_id' => $property->id,
            'reporter_name' => 'Pelajar Ujian',
            'reporter_phone' => '0123456789',
            'reporter_email' => 'pelajar@example.test',
            'report_type' => ReportType::WRONG_PRICE->value,
            'message' => 'Harga rumah sewa ini kelihatan tidak sama dengan maklumat pemilik.',
        ]);

        $response
            ->assertRedirect('/aduan')
            ->assertSessionHas('status', 'Aduan anda berjaya dihantar. Pihak HEP akan menyemak maklumat ini.');

        $this->assertDatabaseHas('property_reports', [
            'property_id' => $property->id,
            'report_type' => ReportType::WRONG_PRICE->value,
            'status' => ReportStatus::NEW->value,
        ]);
    }

    public function test_public_property_api_returns_verified_properties_with_maps_data(): void
    {
        $visibleProperty = $this->createProperty([
            'title' => 'Rumah Sewa API',
            'maps_url' => 'https://maps.google.com/?q=rumah',
        ]);

        $this->createProperty([
            'title' => 'Rumah Tidak Disahkan API',
            'verification_status' => VerificationStatus::PENDING,
        ]);

        $response = $this->getJson('/api/v1/properties');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'tajuk' => 'Rumah Sewa API',
                'maps_url' => 'https://maps.google.com/?q=rumah',
            ])
            ->assertJsonMissing([
                'tajuk' => 'Rumah Tidak Disahkan API',
            ]);

        $this->assertNotNull($response->json('data.0.direction_url'));
        $this->assertSame($visibleProperty->id, $response->json('data.0.id'));
    }

    public function test_api_report_submission_uses_standard_response(): void
    {
        $property = $this->createProperty();

        $response = $this->postJson('/api/v1/reports', [
            'property_id' => $property->id,
            'report_type' => ReportType::OWNER_NOT_RESPONDING->value,
            'message' => 'Pemilik rumah tidak memberi respons selepas beberapa kali dihubungi.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Aduan anda berjaya dihantar. Pihak HEP akan menyemak maklumat ini.')
            ->assertJsonPath('data.jenis_aduan', 'Pemilik tidak memberi respons');
    }

    public function test_api_login_returns_sanctum_token_roles_and_permissions(): void
    {
        $user = User::factory()->create([
            'email' => 'apiadmin@hep.test',
            'password' => 'password',
        ]);
        $user->assignRole('hep_admin');

        $response = $this->postJson('/api/v1/login', [
            'email' => 'apiadmin@hep.test',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user',
                    'roles',
                    'permissions',
                ],
            ])
            ->assertJsonFragment(['hep_admin']);
    }

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
            'title' => 'Rumah Sewa Ujian Sprint 5',
            'description' => 'Rumah sewa untuk ujian Sprint 5.',
            'address' => 'POLIMAS Jitra Kedah',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'distance_km' => 1.5,
            'status' => PropertyAvailabilityStatus::AVAILABLE,
            'verification_status' => VerificationStatus::VERIFIED,
            'gender_preference' => GenderPreference::ANY,
        ], $overrides));
    }
}
