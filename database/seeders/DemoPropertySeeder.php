<?php

namespace Database\Seeders;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\VerificationStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoPropertySeeder extends Seeder
{
    /**
     * Seed demo rental data for public portal and API testing.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@hep.test')->first();

        $this->ensureBaseData();

        foreach ($this->demoProperties() as $index => $item) {
            $owner = $this->owner($index, $admin?->id);
            $area = Area::query()->firstWhere('name', $item['area']);
            $category = Category::query()->firstWhere('name', $item['category']);

            $property = Property::withTrashed()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'owner_id' => $owner->id,
                    'description' => $item['description'],
                    'address' => $item['address'],
                    'area_id' => $area?->id,
                    'category_id' => $category?->id,
                    'price' => $item['price'],
                    'deposit' => $item['deposit'],
                    'distance_km' => $item['distance_km'],
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'maps_url' => $item['maps_url'],
                    'status' => $item['status'],
                    'verification_status' => VerificationStatus::VERIFIED->value,
                    'gender_preference' => $item['gender_preference'],
                    'total_rooms' => $item['total_rooms'],
                    'total_bathrooms' => $item['total_bathrooms'],
                    'max_occupants' => $item['max_occupants'],
                    'has_parking' => $item['has_parking'],
                    'has_wifi' => $item['has_wifi'],
                    'has_washing_machine' => $item['has_washing_machine'],
                    'has_kitchen' => $item['has_kitchen'],
                    'has_aircond' => $item['has_aircond'],
                    'remarks' => 'Data demo untuk ujian sistem.',
                    'created_by' => $admin?->id,
                    'verified_by' => $admin?->id,
                    'verified_at' => now(),
                ],
            );

            if ($property->trashed()) {
                $property->restore();
            }

            $facilityIds = Facility::query()
                ->whereIn('name', $item['facilities'])
                ->pluck('id')
                ->all();

            $property->facilities()->sync($facilityIds);
            $this->thumbnail($property, $index, $item['title']);
        }
    }

    private function ensureBaseData(): void
    {
        foreach (['Taman Siswa', 'Taman Mahsuri', 'Jitra', 'Changlun', 'Napoh', 'Kodiang', 'Arau', 'Alor Setar'] as $area) {
            Area::withTrashed()->updateOrCreate(
                ['name' => $area],
                [
                    'description' => "Kawasan {$area} untuk rujukan rumah sewa sekitar POLIMAS.",
                    'status' => RecordStatus::ACTIVE->value,
                ],
            )->restore();
        }

        foreach (['Rumah Penuh', 'Bilik Sewa', 'Homestay', 'Rumah Keluarga', 'Penginapan Sementara'] as $category) {
            Category::withTrashed()->updateOrCreate(
                ['name' => $category],
                [
                    'description' => "Kategori {$category}.",
                    'status' => RecordStatus::ACTIVE->value,
                ],
            )->restore();
        }

        foreach (['WiFi', 'Parking', 'Mesin Basuh', 'Dapur', 'Penyaman Udara', 'Katil', 'Meja', 'Almari', 'Pemanas Air'] as $facility) {
            Facility::withTrashed()->updateOrCreate(['name' => $facility], ['icon' => null])->restore();
        }
    }

    private function owner(int $index, ?int $adminId): Owner
    {
        $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

        $owner = Owner::withTrashed()->updateOrCreate(
            ['phone' => '01110020'.$number],
            [
                'name' => 'Pemilik Demo '.$number,
                'whatsapp_number' => '601110020'.$number,
                'email' => 'pemilik.demo'.$number.'@example.test',
                'address' => 'Alamat pemilik demo '.$number.', Kedah',
                'verification_status' => VerificationStatus::VERIFIED->value,
                'remarks' => 'Data demo tanpa maklumat sensitif sebenar.',
                'created_by' => $adminId,
                'verified_by' => $adminId,
                'verified_at' => now(),
            ],
        );

        if ($owner->trashed()) {
            $owner->restore();
        }

        return $owner;
    }

    private function thumbnail(Property $property, int $index, string $title): void
    {
        $slug = Str::slug($title);
        $path = "demo-properties/{$slug}.svg";

        Storage::disk('public')->put($path, $this->svgPlaceholder($title, $index));

        PropertyImage::withTrashed()->updateOrCreate(
            [
                'property_id' => $property->id,
                'sort_order' => 0,
            ],
            [
                'image_path' => $path,
                'caption' => 'Gambar demo '.$title,
                'is_thumbnail' => true,
            ],
        )->restore();
    }

    private function svgPlaceholder(string $title, int $index): string
    {
        $colors = [
            ['#047857', '#d9f99d'],
            ['#0f766e', '#cffafe'],
            ['#b45309', '#fef3c7'],
            ['#1d4ed8', '#dbeafe'],
            ['#be123c', '#ffe4e6'],
        ];

        [$primary, $secondary] = $colors[$index % count($colors)];
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900" role="img" aria-label="{$escapedTitle}">
  <rect width="1200" height="900" fill="{$secondary}"/>
  <rect x="90" y="115" width="1020" height="670" rx="48" fill="#ffffff" opacity="0.88"/>
  <path d="M250 520 L600 265 L950 520 V730 H710 V585 H490 V730 H250 Z" fill="{$primary}"/>
  <path d="M205 520 L600 225 L995 520" fill="none" stroke="#111827" stroke-width="34" stroke-linecap="round" stroke-linejoin="round"/>
  <rect x="520" y="610" width="160" height="120" rx="16" fill="#fefce8"/>
  <text x="600" y="145" text-anchor="middle" font-family="Arial, sans-serif" font-size="58" font-weight="700" fill="#111827">Portal Rumah Sewa HEP</text>
  <text x="600" y="835" text-anchor="middle" font-family="Arial, sans-serif" font-size="42" font-weight="700" fill="#111827">{$escapedTitle}</text>
</svg>
SVG;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function demoProperties(): array
    {
        $items = [
            [
                'title' => 'Rumah Sewa Taman Siswa',
                'area' => 'Taman Siswa',
                'category' => 'Rumah Penuh',
                'description' => 'Rumah lengkap untuk pelajar yang mahu tinggal berdekatan POLIMAS. Sesuai untuk kumpulan kecil dan mudah ke kedai makan.',
                'address' => 'Taman Siswa, Jitra, Kedah',
                'price' => 650,
                'deposit' => 650,
                'distance_km' => 1.5,
                'latitude' => 6.2695000,
                'longitude' => 100.4214000,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::FEMALE->value,
                'total_rooms' => 3,
                'total_bathrooms' => 2,
                'max_occupants' => 6,
                'has_parking' => true,
                'has_wifi' => true,
                'has_washing_machine' => true,
                'has_kitchen' => true,
                'has_aircond' => false,
                'facilities' => ['WiFi', 'Parking', 'Mesin Basuh', 'Dapur'],
            ],
            [
                'title' => 'Bilik Sewa Jitra',
                'area' => 'Jitra',
                'category' => 'Bilik Sewa',
                'description' => 'Bilik sewa asas untuk pelajar. Lokasi mudah dicapai dan sesuai untuk penginapan semester.',
                'address' => 'Pekan Jitra, Kedah',
                'price' => 220,
                'deposit' => 220,
                'distance_km' => 3.2,
                'latitude' => null,
                'longitude' => null,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::MALE->value,
                'total_rooms' => 1,
                'total_bathrooms' => 1,
                'max_occupants' => 1,
                'has_parking' => false,
                'has_wifi' => true,
                'has_washing_machine' => false,
                'has_kitchen' => true,
                'has_aircond' => false,
                'facilities' => ['WiFi', 'Dapur', 'Katil', 'Meja'],
            ],
            [
                'title' => 'Rumah Keluarga Changlun',
                'area' => 'Changlun',
                'category' => 'Rumah Keluarga',
                'description' => 'Rumah keluarga untuk ibu bapa atau penjaga yang datang menghantar pelajar baharu.',
                'address' => 'Changlun, Kedah',
                'price' => 900,
                'deposit' => 900,
                'distance_km' => 12.8,
                'latitude' => 6.4342000,
                'longitude' => 100.4309000,
                'status' => PropertyAvailabilityStatus::FULL->value,
                'gender_preference' => GenderPreference::FAMILY->value,
                'total_rooms' => 4,
                'total_bathrooms' => 2,
                'max_occupants' => 8,
                'has_parking' => true,
                'has_wifi' => true,
                'has_washing_machine' => true,
                'has_kitchen' => true,
                'has_aircond' => true,
                'facilities' => ['WiFi', 'Parking', 'Dapur', 'Penyaman Udara', 'Pemanas Air'],
            ],
            [
                'title' => 'Penginapan Sementara Napoh',
                'area' => 'Napoh',
                'category' => 'Penginapan Sementara',
                'description' => 'Penginapan sementara untuk tempoh pendek semasa pendaftaran atau urusan keluarga.',
                'address' => 'Napoh, Kedah',
                'price' => 120,
                'deposit' => null,
                'distance_km' => 9.5,
                'latitude' => null,
                'longitude' => null,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::ANY->value,
                'total_rooms' => 2,
                'total_bathrooms' => 1,
                'max_occupants' => 4,
                'has_parking' => true,
                'has_wifi' => false,
                'has_washing_machine' => false,
                'has_kitchen' => true,
                'has_aircond' => true,
                'facilities' => ['Parking', 'Dapur', 'Penyaman Udara'],
            ],
            [
                'title' => 'Rumah Sewa Taman Mahsuri',
                'area' => 'Taman Mahsuri',
                'category' => 'Rumah Penuh',
                'description' => 'Rumah sewa selesa dengan ruang tamu dan dapur. Sesuai untuk pelajar berkumpulan.',
                'address' => 'Taman Mahsuri, Jitra, Kedah',
                'price' => 700,
                'deposit' => 700,
                'distance_km' => 2.4,
                'latitude' => 6.2782000,
                'longitude' => 100.4211000,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::FEMALE->value,
                'total_rooms' => 3,
                'total_bathrooms' => 2,
                'max_occupants' => 6,
                'has_parking' => true,
                'has_wifi' => true,
                'has_washing_machine' => false,
                'has_kitchen' => true,
                'has_aircond' => false,
                'facilities' => ['WiFi', 'Parking', 'Dapur', 'Almari'],
            ],
            [
                'title' => 'Bilik Sewa Arau',
                'area' => 'Arau',
                'category' => 'Bilik Sewa',
                'description' => 'Bilik sewa untuk pelajar yang memerlukan pilihan bajet dengan kemudahan asas.',
                'address' => 'Arau, Perlis',
                'price' => 200,
                'deposit' => 200,
                'distance_km' => 22.0,
                'latitude' => null,
                'longitude' => null,
                'status' => PropertyAvailabilityStatus::FULL->value,
                'gender_preference' => GenderPreference::MALE->value,
                'total_rooms' => 1,
                'total_bathrooms' => 1,
                'max_occupants' => 1,
                'has_parking' => false,
                'has_wifi' => false,
                'has_washing_machine' => false,
                'has_kitchen' => true,
                'has_aircond' => false,
                'facilities' => ['Dapur', 'Katil', 'Meja'],
            ],
            [
                'title' => 'Rumah Sewa Kodiang',
                'area' => 'Kodiang',
                'category' => 'Rumah Penuh',
                'description' => 'Rumah satu tingkat dengan ruang parking dan suasana kejiranan yang tenang.',
                'address' => 'Kodiang, Kedah',
                'price' => 550,
                'deposit' => 550,
                'distance_km' => 14.2,
                'latitude' => 6.3780000,
                'longitude' => 100.3030000,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::ANY->value,
                'total_rooms' => 3,
                'total_bathrooms' => 1,
                'max_occupants' => 5,
                'has_parking' => true,
                'has_wifi' => false,
                'has_washing_machine' => true,
                'has_kitchen' => true,
                'has_aircond' => false,
                'facilities' => ['Parking', 'Mesin Basuh', 'Dapur'],
            ],
            [
                'title' => 'Rumah Sewa Alor Setar',
                'area' => 'Alor Setar',
                'category' => 'Rumah Keluarga',
                'description' => 'Rumah sesuai untuk keluarga kecil yang mencari penginapan lebih luas di kawasan bandar.',
                'address' => 'Alor Setar, Kedah',
                'price' => 950,
                'deposit' => 950,
                'distance_km' => 20.5,
                'latitude' => null,
                'longitude' => null,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::FAMILY->value,
                'total_rooms' => 4,
                'total_bathrooms' => 2,
                'max_occupants' => 7,
                'has_parking' => true,
                'has_wifi' => true,
                'has_washing_machine' => true,
                'has_kitchen' => true,
                'has_aircond' => true,
                'facilities' => ['WiFi', 'Parking', 'Mesin Basuh', 'Dapur', 'Penyaman Udara'],
            ],
            [
                'title' => 'Rumah Sewa Berdekatan POLIMAS',
                'area' => 'Taman Siswa',
                'category' => 'Rumah Penuh',
                'description' => 'Pilihan paling hampir dengan POLIMAS. Sesuai untuk pelajar yang mahu perjalanan harian lebih singkat.',
                'address' => 'Jalan POLIMAS, Bandar Darulaman, Jitra, Kedah',
                'price' => 780,
                'deposit' => 780,
                'distance_km' => 0.8,
                'latitude' => 6.2649000,
                'longitude' => 100.4196000,
                'status' => PropertyAvailabilityStatus::AVAILABLE->value,
                'gender_preference' => GenderPreference::ANY->value,
                'total_rooms' => 3,
                'total_bathrooms' => 2,
                'max_occupants' => 6,
                'has_parking' => true,
                'has_wifi' => true,
                'has_washing_machine' => true,
                'has_kitchen' => true,
                'has_aircond' => true,
                'facilities' => ['WiFi', 'Parking', 'Mesin Basuh', 'Dapur', 'Penyaman Udara'],
            ],
            [
                'title' => 'Rumah Keluarga Jitra',
                'area' => 'Jitra',
                'category' => 'Rumah Keluarga',
                'description' => 'Rumah keluarga berdekatan kemudahan bandar Jitra dan sesuai untuk penginapan ibu bapa.',
                'address' => 'Bandar Darulaman, Jitra, Kedah',
                'price' => 850,
                'deposit' => 850,
                'distance_km' => 4.0,
                'latitude' => 6.2767000,
                'longitude' => 100.4353000,
                'status' => PropertyAvailabilityStatus::FULL->value,
                'gender_preference' => GenderPreference::FAMILY->value,
                'total_rooms' => 4,
                'total_bathrooms' => 2,
                'max_occupants' => 8,
                'has_parking' => true,
                'has_wifi' => true,
                'has_washing_machine' => false,
                'has_kitchen' => true,
                'has_aircond' => true,
                'facilities' => ['WiFi', 'Parking', 'Dapur', 'Penyaman Udara', 'Pemanas Air'],
            ],
        ];

        return array_map(fn (array $item): array => $this->withMapsUrl($item), $items);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function withMapsUrl(array $item): array
    {
        $origin = filled($item['latitude']) && filled($item['longitude'])
            ? $item['latitude'].','.$item['longitude']
            : $item['address'];

        $item['maps_url'] = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($origin);

        return $item;
    }
}
