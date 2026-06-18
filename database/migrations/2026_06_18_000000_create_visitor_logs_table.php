<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('visitor_hash', 64);
            $table->string('ip_hash', 64);
            $table->string('user_agent_hash', 64);
            $table->string('first_path')->nullable();
            $table->string('last_path')->nullable();
            $table->date('visited_on');
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedInteger('visit_count')->default(1);
            $table->timestamps();

            $table->unique(['visitor_hash', 'visited_on']);
            $table->index('visited_on');
            $table->index('visitor_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
