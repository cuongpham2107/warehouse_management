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
            // Thêm cột pcs với giá trị mặc định là 1
            $table->integer('pcs')->default(1)->after('pieces');
            
        });

        // Cập nhật tất cả các bản ghi hiện tại để đảm bảo pcs luôn >= 1
        \App\Models\Crate::query()->update(['pcs' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crates', function (Blueprint $table) {
            // Xóa cột pcs nếu cần
            $table->dropColumn('pcs');
        });
    }
};
