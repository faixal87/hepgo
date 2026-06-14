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
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_dashboard_shows_malay_stats(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Papan Pemuka HEP')
            ->assertSee('Jumlah Rumah Sewa')
            ->assertSee('Rumah Masih Kosong')
            ->assertSee('Aduan Baharu');
    }

    public function test_property_edit_page_shows_image_and_status_log_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $property = $this->createProperty();

        $response = $this
            ->actingAs($user)
            ->get("/admin/properties/{$property->id}/edit");

        $response
            ->assertOk()
            ->assertSee('Gambar Rumah')
            ->assertSee('Log Status Rumah')
            ->assertSee('Set Masih Kosong')
            ->assertSee('Sahkan Rumah');
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $this
            ->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_logout_redirects_to_public_homepage(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this
            ->actingAs($user)
            ->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_hep_admin_can_view_but_cannot_modify_super_admin_account(): void
    {
        $superAdmin = User::factory()->create([
            'name' => 'Pentadbir Utama Sistem',
            'email' => 'utama@hep.test',
        ]);
        $superAdmin->assignRole('super_admin');

        $hepAdmin = User::factory()->create([
            'email' => 'hepadmin@hep.test',
        ]);
        $hepAdmin->assignRole('hep_admin');

        $this
            ->actingAs($hepAdmin)
            ->get("/admin/users/{$superAdmin->id}")
            ->assertOk()
            ->assertSee('Pentadbir Utama Sistem');

        $this
            ->actingAs($hepAdmin)
            ->get("/admin/users/{$superAdmin->id}/edit")
            ->assertForbidden();

        $this->assertTrue($hepAdmin->can('view', $superAdmin));
        $this->assertFalse($hepAdmin->can('update', $superAdmin));
        $this->assertFalse($hepAdmin->can('delete', $superAdmin));
    }

    public function test_department_staff_can_submit_records_but_cannot_verify_or_publish(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff_jabatan');
        $property = $this->createProperty(['created_by' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->get("/admin/properties/{$property->id}/edit");

        $response
            ->assertOk()
            ->assertSee('Rumah Sewa Admin')
            ->assertDontSee('Sahkan Rumah')
            ->assertDontSee('Tolak Rumah')
            ->assertDontSee('Set Masih Kosong')
            ->assertDontSee('Set Telah Penuh');

        $verifiedProperty = $this->createProperty([
            'title' => 'Rumah Telah Disahkan',
            'created_by' => $user->id,
            'verification_status' => VerificationStatus::VERIFIED,
        ]);

        $this->assertTrue($user->can('create', Property::class));
        $this->assertTrue($user->can('update', $property));
        $this->assertFalse($user->can('verify', $property));
        $this->assertFalse($user->can('updateAvailability', $property));
        $this->assertFalse($user->can('update', $verifiedProperty));
    }

    private function createProperty(array $overrides = []): Property
    {
        $owner = Owner::create([
            'name' => 'Pemilik Ujian',
            'phone' => '0123456789',
            'whatsapp_number' => '0123456789',
            'verification_status' => VerificationStatus::PENDING,
        ]);

        $area = Area::firstOrCreate(
            ['name' => 'Kawasan Ujian'],
            ['status' => RecordStatus::ACTIVE]
        );

        $category = Category::firstOrCreate(
            ['name' => 'Kategori Ujian'],
            ['status' => RecordStatus::ACTIVE]
        );

        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => 'Rumah Sewa Admin',
            'description' => 'Rumah sewa untuk ujian admin.',
            'address' => 'Alamat rumah sewa admin.',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'status' => PropertyAvailabilityStatus::PENDING,
            'verification_status' => VerificationStatus::PENDING,
            'gender_preference' => GenderPreference::ANY,
        ], $overrides));
    }
}
