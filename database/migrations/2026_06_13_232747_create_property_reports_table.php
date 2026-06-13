<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_phone', 30)->nullable();
            $table->string('reporter_email')->nullable();
            $table->enum('report_type', [
                'full_house',
                'wrong_location',
                'inactive_phone',
                'wrong_price',
                'misleading_photo',
                'owner_not_responding',
                'other',
            ]);
            $table->text('message');
            $table->enum('status', [
                'new',
                'reviewing',
                'resolved',
                'rejected',
            ])->default('new');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_reports');
    }
};
