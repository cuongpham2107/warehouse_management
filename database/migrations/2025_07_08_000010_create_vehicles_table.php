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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_code', 50)->unique();
            $table->enum('vehicle_type', ['truck', 'container', 'van']);
            $table->string('license_plate', 20);
            $table->string('driver_name', 255)->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->decimal('capacity_weight', 10, 2)->nullable();
            $table->decimal('capacity_volume', 10, 2)->nullable();
            $table->enum('status', ['available', 'loading', 'in_transit', 'maintenance'])->default('available');
            $table->timestamps();
            
            $table->index(['vehicle_code']);
            $table->index(['vehicle_type']);
            $table->index(['status']);
            $table->index(['license_plate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
