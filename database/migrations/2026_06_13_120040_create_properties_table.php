<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('address');
            $table->foreignId('area_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('deposit', 10, 2)->nullable();
            $table->decimal('distance_km', 6, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('maps_url')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('verification_status')->default('pending')->index();
            $table->string('gender_preference')->default('any')->index();
            $table->unsignedInteger('total_rooms')->nullable();
            $table->unsignedInteger('total_bathrooms')->nullable();
            $table->unsignedInteger('max_occupants')->nullable();
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_washing_machine')->default(false);
            $table->boolean('has_kitchen')->default(false);
            $table->boolean('has_aircond')->default(false);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
