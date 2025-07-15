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
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->foreignId('crate_id')
                ->nullable()
                ->constrained('crates')
                ->nullOnDelete()
                ->after('shipment_id');
            $table->integer('quantity')
                ->default(0)
                ->after('crate_id');
            $table->string('notes')
                ->nullable()
                ->after('quantity');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['crate_id']);
            $table->dropColumn(['crate_id', 'quantity', 'notes']);
        });
    }
};
