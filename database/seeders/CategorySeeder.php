<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'Rumah Penuh',
            'Bilik Sewa',
            'Homestay',
            'Rumah Keluarga',
            'Penginapan Sementara',
        ])->each(fn (string $name): Category => Category::updateOrCreate(
            ['name' => $name],
            ['status' => RecordStatus::ACTIVE],
        ));
    }
}
