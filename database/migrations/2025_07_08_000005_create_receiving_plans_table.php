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
        Schema::create('receiving_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_code', 50)->unique();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->date('plan_date');
            $table->integer('total_crates')->default(0);
            $table->integer('total_pieces')->default(0);
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['plan_code']);
            $table->index(['vendor_id']);
            $table->index(['status']);
            $table->index(['plan_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_plans');
    }
};
