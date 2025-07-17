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
        Schema::create('shipping_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 50)->unique();
            $table->string('customer_name', 255);
            $table->string('customer_contact', 255)->nullable();
            $table->text('delivery_address')->nullable();
            $table->date('requested_date');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'processing', 'ready', 'shipping', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['request_code']);
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['requested_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_requests');
    }
};
