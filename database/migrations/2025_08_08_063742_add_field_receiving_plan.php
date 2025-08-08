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
        Schema::table('receiving_plans', function (Blueprint $table) {
            //Thời gian nhập kho
            $table->date('arrival_date')->after('plan_date')->nullable();
            //Biển số xe
            $table->string('license_plate')->after('arrival_date')->nullable();
            //Nhà xe vận chuyển
            $table->string('transport_garage')->after('license_plate')->nullable();
            //Tải trọng của xe
            $table->integer('vehicle_capacity')->after('transport_garage')->nullable(); // Uncomment if you want to add vehicle capacity

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receiving_plans', function (Blueprint $table) {
            $table->dropColumn('arrival_date');
            $table->dropColumn('license_plate');
            $table->dropColumn('transport_garage');
            $table->dropColumn('vehicle_capacity');
        });
    }
};
