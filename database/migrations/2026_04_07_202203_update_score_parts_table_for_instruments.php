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
        Schema::table('score_parts', function (Blueprint $table) {
            // Add instrument_id foreign key
            $table->foreignId('instrument_id')->nullable()->constrained()->onDelete('set null')->after('score_id');
            // Add part_number for when same instrument has multiple parts (e.g., Klarinet 1, 2, 3)
            $table->integer('part_number')->default(1)->after('instrument_id');
            // Keep old 'instrument' column for now (will drop in next migration after data migration)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_parts', function (Blueprint $table) {
            $table->dropForeign(['instrument_id']);
            $table->dropColumn(['instrument_id', 'part_number']);
        });
    }
};
