<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Xử lý bảng receiving_plans
        Schema::table('receiving_plans', function (Blueprint $table) {
            // Thêm cột tạm thời để lưu dữ liệu time
            $table->time('arrival_time_temp')->nullable()->after('arrival_date');
        });

        // Chuyển đổi dữ liệu từ datetime sang time
        DB::statement("UPDATE receiving_plans SET arrival_time_temp = TIME(arrival_date) WHERE arrival_date IS NOT NULL");

        Schema::table('receiving_plans', function (Blueprint $table) {
            // Xóa cột cũ
            $table->dropColumn('arrival_date');
        });

        Schema::table('receiving_plans', function (Blueprint $table) {
            // Đổi tên cột tạm thời thành tên cột chính thức
            $table->renameColumn('arrival_time_temp', 'arrival_date');
        });

        // Xử lý bảng shipping_requests
        Schema::table('shipping_requests', function (Blueprint $table) {
            // Thêm cột tạm thời để lưu dữ liệu time
            $table->time('lifting_time_temp')->nullable()->after('lifting_time');
        });

        // Chuyển đổi dữ liệu từ datetime sang time
        DB::statement("UPDATE shipping_requests SET lifting_time_temp = TIME(lifting_time) WHERE lifting_time IS NOT NULL");

        Schema::table('shipping_requests', function (Blueprint $table) {
            // Xóa cột cũ
            $table->dropColumn('lifting_time');
        });

        Schema::table('shipping_requests', function (Blueprint $table) {
            // Đổi tên cột tạm thời thành tên cột chính thức
            $table->renameColumn('lifting_time_temp', 'lifting_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback receiving_plans
        Schema::table('receiving_plans', function (Blueprint $table) {
            $table->datetime('arrival_date_temp')->nullable()->after('arrival_date');
        });

        // Chuyển đổi dữ liệu từ time về datetime (sử dụng ngày hiện tại)
        DB::statement("UPDATE receiving_plans SET arrival_date_temp = CONCAT(CURDATE(), ' ', arrival_date) WHERE arrival_date IS NOT NULL");

        Schema::table('receiving_plans', function (Blueprint $table) {
            $table->dropColumn('arrival_date');
        });

        Schema::table('receiving_plans', function (Blueprint $table) {
            $table->renameColumn('arrival_date_temp', 'arrival_date');
        });

        // Rollback shipping_requests
        Schema::table('shipping_requests', function (Blueprint $table) {
            $table->datetime('lifting_time_temp')->nullable()->after('lifting_time');
        });

        // Chuyển đổi dữ liệu từ time về datetime (sử dụng ngày hiện tại)
        DB::statement("UPDATE shipping_requests SET lifting_time_temp = CONCAT(CURDATE(), ' ', lifting_time) WHERE lifting_time IS NOT NULL");

        Schema::table('shipping_requests', function (Blueprint $table) {
            $table->dropColumn('lifting_time');
        });

        Schema::table('shipping_requests', function (Blueprint $table) {
            $table->renameColumn('lifting_time_temp', 'lifting_time');
        });
    }
};
