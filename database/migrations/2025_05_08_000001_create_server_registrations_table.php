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
        Schema::create('server_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('server_name')->unique();
            $table->string('server_ip')->nullable();
            $table->string('environment')->default('production');
            $table->string('region')->nullable();
            $table->string('api_key')->unique();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamps();
            
            $table->index('environment');
            $table->index('active');
            $table->index('last_reported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_registrations');
    }
};