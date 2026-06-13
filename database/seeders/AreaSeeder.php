<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'Taman Siswa',
            'Taman Mahsuri',
            'Jitra',
            'Changlun',
            'Napoh',
            'Kodiang',
            'Arau',
            'Alor Setar',
        ])->each(fn (string $name): Area => Area::updateOrCreate(
            ['name' => $name],
            ['status' => RecordStatus::ACTIVE],
        ));
    }
}
