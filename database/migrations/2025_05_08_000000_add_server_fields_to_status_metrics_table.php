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
        Schema::table('status_metrics', function (Blueprint $table) {
            $table->string('server_name')->nullable()->after('source');
            $table->string('server_ip')->nullable()->after('server_name');
            $table->string('environment')->nullable()->after('server_ip');
            $table->string('region')->nullable()->after('environment');
            
            // Add indexes for faster querying
            $table->index('server_name');
            $table->index('environment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_metrics', function (Blueprint $table) {
            $table->dropColumn([
                'server_name',
                'server_ip',
                'environment',
                'region'
            ]);
        });
    }
};