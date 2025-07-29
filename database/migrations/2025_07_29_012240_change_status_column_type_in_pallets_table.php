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
        Schema::table('pallets', function (Blueprint $table) {
            $table->string('status')->default('in_transit')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pallets', function (Blueprint $table) {
            $table->enum('status', ['in_transit', 'stored', 'staging', 'shipped'])->default('in_transit')->change();
        });
    }
};
