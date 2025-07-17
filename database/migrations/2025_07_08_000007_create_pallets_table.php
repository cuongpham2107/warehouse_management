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
        Schema::create('pallets', function (Blueprint $table) {
            $table->id();
            $table->string('pallet_id', 100)->unique();
            $table->foreignId('crate_id')->constrained('crates');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->enum('status', ['in_transit', 'stored', 'staging', 'shipped'])->default('in_transit');
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users');
            $table->timestamp('checked_out_at')->nullable();
            $table->foreignId('checked_out_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['pallet_id']);
            $table->index(['crate_id']);
            $table->index(['location_id']);
            $table->index(['status']);
            $table->index(['checked_in_at']);
            $table->index(['checked_out_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallets');
    }
};
