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
        Schema::create('pallet_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->string('description')->nullable();
            $table->timestamp('action_time')->useCurrent();// Mặc định thời gian hiện tại
            $table->string('old_data')->nullable(); // Dữ liệu cũ trước khi thay đổi
            $table->string('new_data')->nullable(); // Dữ liệu mới sau khi thay
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallet_activities');
    }
};
