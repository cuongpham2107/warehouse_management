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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_code', 50)->unique();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('shipping_request_id')->nullable()->constrained('shipping_requests');
            $table->timestamp('departure_time')->nullable();
            $table->timestamp('arrival_time')->nullable();
            $table->integer('total_crates')->default(0);
            $table->integer('total_pieces')->default(0);
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->enum('status', ['loading', 'ready', 'departed', 'delivered', 'returned'])->default('loading');
            $table->boolean('pod_generated')->default(false);
            $table->string('pod_file_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['shipment_code']);
            $table->index(['vehicle_id']);
            $table->index(['shipping_request_id']);
            $table->index(['status']);
            $table->index(['departure_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
