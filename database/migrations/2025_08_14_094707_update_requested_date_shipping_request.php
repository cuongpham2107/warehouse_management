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
            // chuyển trường requested_date từ date thành datetime
            $table->dateTime('requested_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_requests', function (Blueprint $table) {
            // chuyển trường requested_date từ datetime về date
            $table->date('requested_date')->change();
        });
    }
};
