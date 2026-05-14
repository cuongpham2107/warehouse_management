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
        Schema::table('crates', function (Blueprint $table) {
            // Xóa ràng buộc unique cũ trên duy nhất crate_id
            $table->dropUnique(['crate_id']);
            
            // Thêm ràng buộc unique composite trên crate_id và receiving_plan_id
            $table->unique(['crate_id', 'receiving_plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crates', function (Blueprint $table) {
            $table->dropUnique(['crate_id', 'receiving_plan_id']);
            $table->unique('crate_id');
        });
    }
};
