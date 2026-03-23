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
        Schema::dropIfExists('inventory_movements');
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pallet_id')->constrained('pallets');
            $table->enum('movement_type', ['transfer', 'relocate']);
            $table->string('from_location_code')->nullable();
            $table->string('to_location_code')->nullable();
            $table->timestamp('movement_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->enum('device_type', ['scanner', 'manual'])->default('manual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
