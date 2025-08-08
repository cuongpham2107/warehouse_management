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
        Schema::table('shipping_requests', function (Blueprint $table) {
            //Thêm trường thời gian nâng hạ hàng
            $table->dateTime('lifting_time')->after('requested_date')->nullable();
            // Thêm trường nhà xe vận chuyển
            $table->string('transport_garage')->after('license_plate')->nullable();
            // Thêm trường tải trọng xe
            $table->integer('vehicle_capacity')->after('transport_garage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_requests', function (Blueprint $table) {
            $table->dropColumn('lifting_time');
            $table->dropColumn('transport_garage');
            $table->dropColumn('vehicle_capacity');
        });
    }
};
