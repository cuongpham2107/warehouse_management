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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pallet_id')->constrained('pallets');
            $table->enum('movement_type', ['check_in', 'check_out', 'move', 'adjust']);
            $table->foreignId('from_location_id')->nullable()->constrained('warehouse_locations');
            $table->foreignId('to_location_id')->nullable()->constrained('warehouse_locations');
            $table->timestamp('movement_date')->useCurrent();
            $table->enum('reference_type', ['receiving_plan', 'shipping_request', 'manual']);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->enum('device_type', ['web', 'pda', 'forklift_computer']);
            $table->string('device_id', 100)->nullable();
            $table->timestamps();
            
            $table->index(['pallet_id']);
            $table->index(['movement_type']);
            $table->index(['movement_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['performed_by']);
            $table->index(['device_type']);
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
