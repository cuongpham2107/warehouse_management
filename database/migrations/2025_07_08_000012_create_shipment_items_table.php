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
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->foreignId('pallet_id')->constrained('pallets');
            $table->timestamp('loaded_at')->nullable();
            $table->foreignId('loaded_by')->nullable()->constrained('users');
            $table->timestamps();
             $table->foreignId('crate_id')
                ->nullable()
                ->constrained('crates')
                ->nullOnDelete()
                ->after('shipment_id');
            $table->integer('quantity')
                ->default(0)
                ->after('crate_id');
            $table->string('notes')
                ->nullable()
                ->after('quantity');

            
            $table->index(['shipment_id']);
            $table->index(['pallet_id']);
            $table->index(['loaded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
