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
        Schema::create('crates', function (Blueprint $table) {
            $table->id();
            $table->string('crate_id', 100)->unique();
            $table->foreignId('receiving_plan_id')->constrained('receiving_plans')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->integer('pieces')->default(0);
            $table->string('type')->default('standard');
            $table->decimal('gross_weight', 10, 2)->default(0);
            $table->decimal('dimensions_length', 8, 2)->nullable();
            $table->decimal('dimensions_width', 8, 2)->nullable();
            $table->decimal('dimensions_height', 8, 2)->nullable();
            $table->enum('status', ['planned', 'checked_in', 'checked_out', 'shipped'])->default('planned');
            $table->string('barcode', 255)->nullable();
            $table->timestamps();
            
            $table->index(['crate_id']);
            $table->index(['receiving_plan_id']);
            $table->index(['status']);
            $table->index(['barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crates');
    }
};
