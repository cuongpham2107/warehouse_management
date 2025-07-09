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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_code', 50)->unique();
            $table->string('zone', 50);
            $table->string('rack', 50);
            $table->integer('level');
            $table->string('position', 50);
            $table->decimal('max_weight', 10, 2)->nullable();
            $table->decimal('max_volume', 10, 2)->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->timestamps();
            
            $table->index(['location_code']);
            $table->index(['zone']);
            $table->index(['status']);
            $table->index(['zone', 'rack', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
