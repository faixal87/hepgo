<?php

namespace Tests\Feature;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\ReportType;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Sprint7Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_app_config_endpoint_returns_mobile_configuration(): void
    {
        $this->getJson('/api/v1/app-config')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Konfigurasi aplikasi berjaya dipaparkan.')
            ->assertJsonPath('data.app_name', 'Portal Rumah Sewa HEP')
            ->assertJsonPath('data.campus_location', 'POLIMAS, Jitra, Kedah, Malaysia')
            ->assertJsonPath('data.features.bookmark', true);
    }

    public function test_property_list_supports_mobile_filters_sort_and_root_meta(): void
    {
        $area = Area::create(['name' => 'Taman Siswa', 'status' => RecordStatus::ACTIVE->value]);
        $category = Category::create(['name' => 'Rumah Penuh', 'status' => RecordStatus::ACTIVE->value]);
        $facility = Facility::create(['name' => 'WiFi']);

        $first = $this->createProperty([
            'title' => 'Rumah Murah POLIMAS',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 300,
            'distance_km' => 1.2,
        ]);
        $first->facilities()->attach($facility);

        $this->createProperty([
            'title' => 'Rumah Mahal Jitra',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 900,
            'distance_km' => 3.0,
        ]);

        $this->getJson('/api/v1/properties?search=Murah&area_id='.$area->id.'&category_id='.$category->id.'&min_price=100&max_price=500&status=available&gender_preference=any&facilities[]='.$facility->id.'&sort=price_low&per_page=5')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Senarai data berjaya dipaparkan.')
            ->assertJsonPath('data.0.id', $first->id)
            ->assertJsonPath('data.0.harga_label', 'RM300 sebulan')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_property_detail_is_mobile_ready_and_hides_sensitive_owner_data(): void
    {
        $property = $this->createProperty([
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=POLIMAS',
        ]);

        $this->getJson('/api/v1/properties/'.$property->id)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $property->id)
            ->assertJsonPath('data.harga_label', 'RM500 sebulan')
            ->assertJsonPath('data.maklumat_pemilik_public.nama', 'Pemilik API Sprint 7')
            ->assertJsonPath('data.nota_keselamatan', 'Nota: HEP menyediakan maklumat ini sebagai rujukan. Sila semak sendiri keadaan rumah dan persetujuan sewaan sebelum membuat sebarang bayaran.')
            ->assertJsonMissingPath('data.maklumat_pemilik_public.ic_number')
            ->assertJsonMissingPath('data.remarks')
            ->assertJsonMissingPath('data.deleted_at')
            ->assertJsonMissingPath('data.created_by')
            ->assertJsonMissingPath('data.verified_by');
    }

    public function test_auth_api_returns_bearer_token_profile_and_logout_messages(): void
    {
        $user = User::factory()->create([
            'email' => 'mobile@hep.test',
            'password' => 'password',
            'phone' => '0123456789',
            'status' => UserStatus::ACTIVE->value,
        ]);
        $user->assignRole('student');

        $login = $this->postJson('/api/v1/login', [
            'email' => 'mobile@hep.test',
            'password' => 'password',
        ]);

        $login
            ->assertOk()
            ->assertJsonPath('message', 'Log masuk berjaya.')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', 'mobile@hep.test');

        $token = $login->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('message', 'Profil pengguna berjaya dipaparkan.')
            ->assertJsonPath('data.email', 'mobile@hep.test')
            ->assertJsonPath('data.phone', '0123456789');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Log keluar berjaya.');
    }

    public function test_bookmark_api_can_add_list_and_delete_saved_property(): void
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $property = $this->createProperty(['title' => 'Rumah Simpanan Sprint 7']);

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/bookmarks/'.$property->id)
            ->assertOk()
            ->assertJsonPath('message', 'Rumah sewa berjaya disimpan.')
            ->assertJsonPath('data.is_bookmarked', true);

        $this->getJson('/api/v1/bookmarks')
            ->assertOk()
            ->assertJsonPath('message', 'Senarai rumah simpanan berjaya dipaparkan.')
            ->assertJsonPath('data.0.id', $property->id);

        $this->deleteJson('/api/v1/bookmarks/'.$property->id)
            ->assertOk()
            ->assertJsonPath('message', 'Rumah sewa berjaya dibuang daripada simpanan.')
            ->assertJsonPath('data.is_bookmarked', false);
    }

    public function test_public_report_api_does_not_expose_admin_remarks(): void
    {
        $property = $this->createProperty();

        $this->postJson('/api/v1/reports', [
            'property_id' => $property->id,
            'report_type' => ReportType::WRONG_LOCATION->value,
            'message' => 'Lokasi rumah sewa ini kelihatan tidak tepat dalam peta.',
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Aduan anda berjaya dihantar. Pihak HEP akan menyemak maklumat ini.')
            ->assertJsonMissingPath('data.catatan_admin');
    }

    private function createProperty(array $overrides = []): Property
    {
        $owner = Owner::firstOrCreate(
            ['phone' => '0123000000'],
            [
                'name' => 'Pemilik API Sprint 7',
                'whatsapp_number' => '0123000000',
                'ic_number' => '900101025555',
                'verification_status' => VerificationStatus::VERIFIED->value,
            ]
        );

        $area = Area::firstOrCreate(
            ['name' => 'Jitra'],
            ['description' => 'Kawasan ujian', 'status' => RecordStatus::ACTIVE->value]
        );

        $category = Category::firstOrCreate(
            ['name' => 'Rumah Penuh'],
            ['description' => 'Kategori ujian', 'status' => RecordStatus::ACTIVE->value]
        );

        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => 'Rumah Sewa API Sprint 7',
            'description' => 'Rumah sewa untuk ujian API Sprint 7.',
            'address' => 'POLIMAS Jitra Kedah',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'deposit' => 500,
            'distance_km' => 1.5,
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Jitra',
            'status' => PropertyAvailabilityStatus::AVAILABLE->value,
            'verification_status' => VerificationStatus::VERIFIED->value,
            'gender_preference' => GenderPreference::ANY->value,
        ], $overrides));
    }
}
