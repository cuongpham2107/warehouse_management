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
        Schema::table('users', function (Blueprint $table) {
            // Thêm trường 'employee_code' vào bảng 'users'
            $table->string('asgl_id')->nullable()->after('email');
        });

        // Nếu cần, có thể thêm các chỉ mục hoặc ràng buộc khác cho trường mới
        Schema::table('users', function (Blueprint $table) {
            $table->unique('asgl_id'); // Đảm bảo mã nhân viên là duy nhất
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
