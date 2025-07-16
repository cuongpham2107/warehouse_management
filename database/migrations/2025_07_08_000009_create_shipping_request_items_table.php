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
        Schema::create('shipping_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_request_id')->constrained('shipping_requests')->onDelete('cascade');
            $table->foreignId('crate_id')->constrained('crates');
            $table->integer('quantity_requested')->default(1);
            $table->integer('quantity_shipped')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['shipping_request_id']);
            $table->index(['crate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_request_items');
    }
};
