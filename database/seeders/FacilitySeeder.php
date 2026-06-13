<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'WiFi',
            'Parking',
            'Mesin Basuh',
            'Dapur',
            'Penyaman Udara',
            'Katil',
            'Meja',
            'Almari',
            'Pemanas Air',
        ])->each(fn (string $name): Facility => Facility::updateOrCreate(
            ['name' => $name],
            ['icon' => null],
        ));
    }
}
