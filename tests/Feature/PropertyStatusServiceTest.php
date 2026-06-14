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
use App\Services\PropertyStatusService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_availability_update_creates_status_log(): void
    {
        $user = User::factory()->create();
        $property = $this->createProperty();

        $this->actingAs($user);

        app(PropertyStatusService::class)->updateAvailability(
            $property,
            PropertyAvailabilityStatus::AVAILABLE->value,
            'Rumah sudah tersedia untuk disewa.',
        );

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => PropertyAvailabilityStatus::AVAILABLE->value,
        ]);

        $this->assertDatabaseHas('property_status_logs', [
            'property_id' => $property->id,
            'old_status' => PropertyAvailabilityStatus::PENDING->value,
            'new_status' => PropertyAvailabilityStatus::AVAILABLE->value,
            'changed_by' => $user->id,
            'remarks' => 'Rumah sudah tersedia untuk disewa.',
        ]);
    }

    public function test_verification_update_creates_status_log(): void
    {
        $user = User::factory()->create();
        $property = $this->createProperty();

        $this->actingAs($user);

        app(PropertyStatusService::class)->updateVerification(
            $property,
            VerificationStatus::VERIFIED->value,
            'Maklumat rumah telah disemak.',
        );

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'verification_status' => VerificationStatus::VERIFIED->value,
            'verified_by' => $user->id,
        ]);

        $this->assertDatabaseHas('property_status_logs', [
            'property_id' => $property->id,
            'old_verification_status' => VerificationStatus::PENDING->value,
            'new_verification_status' => VerificationStatus::VERIFIED->value,
            'changed_by' => $user->id,
            'remarks' => 'Maklumat rumah telah disemak.',
        ]);
    }

    public function test_department_staff_receives_notification_when_listing_is_verified(): void
    {
        $staffJabatan = User::factory()->create([
            'name' => 'Staf Jabatan Ujian',
        ]);
        $staffJabatan->assignRole('staff_jabatan');

        $hepAdmin = User::factory()->create();
        $hepAdmin->assignRole('hep_admin');

        $property = $this->createProperty([
            'created_by' => $staffJabatan->id,
        ]);

        $this->actingAs($hepAdmin);

        app(PropertyStatusService::class)->updateVerification(
            $property,
            VerificationStatus::VERIFIED->value,
            'Maklumat rumah telah lengkap disahkan.',
        );

        $staffJabatan->refresh();

        $this->assertCount(1, $staffJabatan->notifications);
        $this->assertSame('Listing anda telah disahkan', $staffJabatan->notifications->first()->data['title']);
    }

    private function createProperty(array $overrides = []): Property
    {
        $owner = Owner::create([
            'name' => 'Pemilik Ujian',
            'phone' => '0123456789',
            'whatsapp_number' => '0123456789',
            'verification_status' => VerificationStatus::PENDING,
        ]);

        $area = Area::create([
            'name' => 'Kawasan Ujian',
            'status' => RecordStatus::ACTIVE,
        ]);

        $category = Category::create([
            'name' => 'Kategori Ujian',
            'status' => RecordStatus::ACTIVE,
        ]);

        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => 'Rumah Ujian',
            'description' => 'Penerangan rumah ujian.',
            'address' => 'Alamat rumah ujian.',
            'area_id' => $area->id,
            'category_id' => $category->id,
            'price' => 500,
            'status' => PropertyAvailabilityStatus::PENDING,
            'verification_status' => VerificationStatus::PENDING,
            'gender_preference' => GenderPreference::ANY,
        ], $overrides));
    }
}
