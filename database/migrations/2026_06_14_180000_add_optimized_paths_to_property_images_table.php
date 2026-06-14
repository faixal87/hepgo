<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_images', function (Blueprint $table): void {
            $table->string('thumbnail_path')->nullable()->after('image_path');
            $table->string('medium_path')->nullable()->after('thumbnail_path');
            $table->string('large_path')->nullable()->after('medium_path');
            $table->string('original_name')->nullable()->after('large_path');
            $table->string('mime_type')->nullable()->after('original_name');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            $table->unsignedInteger('width')->nullable()->after('file_size');
            $table->unsignedInteger('height')->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('property_images', function (Blueprint $table): void {
            $table->dropColumn([
                'thumbnail_path',
                'medium_path',
                'large_path',
                'original_name',
                'mime_type',
                'file_size',
                'width',
                'height',
            ]);
        });
    }
};
