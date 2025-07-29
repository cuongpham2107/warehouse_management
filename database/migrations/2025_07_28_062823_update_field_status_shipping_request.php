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
        Schema::table('shipping_requests', function (Blueprint $table) {
            // Add the status column with a default value
            $table->string('status')->default('pending')->after('notes');
        });

        // Sau khi tạo cột xong mới update dữ liệu
        DB::table('shipping_requests')->update(['status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_requests', function (Blueprint $table) {

            // Drop the status column
            $table->dropColumn('status');
        });
    }
};
