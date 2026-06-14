<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('hero_image_path')->nullable();
            $table->string('hero_image_title')->default('Peta taman sekitar Jitra');
            $table->string('hero_image_caption')->default('Klik untuk buka imej penuh dalam tab baharu.');
            $table->timestamps();
        });

        DB::table('portal_settings')->insert([
            'id' => 1,
            'hero_image_path' => null,
            'hero_image_title' => 'Peta taman sekitar Jitra',
            'hero_image_caption' => 'Klik untuk buka imej penuh dalam tab baharu.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_settings');
    }
};
