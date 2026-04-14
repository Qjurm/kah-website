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
        // 1. Delete duplicates keep lowest ID
        $duplicates = DB::table('instruments')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(id) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('instruments')
                ->where('name', $duplicate->name)
                ->orderBy('id', 'asc')
                ->pluck('id');

            $keepId = $ids->shift(); // get first ID to keep

            // Re-assign foreign keys to the kept ID in aliases
            // Note: score_parts instrument column is a string so it is unaffected
            DB::table('instrument_aliases')
                ->whereIn('instrument_id', $ids)
                ->update(['instrument_id' => $keepId]);

            if (Schema::hasTable('instrument_user')) {
                foreach ($ids as $dropId) {
                    $usersWithKeep = DB::table('instrument_user')->where('instrument_id', $keepId)->pluck('user_id')->toArray();
                    
                    // Update those who don't have the original
                    if (!empty($usersWithKeep)) {
                        DB::table('instrument_user')
                            ->where('instrument_id', $dropId)
                            ->whereNotIn('user_id', $usersWithKeep)
                            ->update(['instrument_id' => $keepId]);
                    } else {
                        DB::table('instrument_user')
                            ->where('instrument_id', $dropId)
                            ->update(['instrument_id' => $keepId]);
                    }
                    
                    // Delete the rest (who already have the original)
                    DB::table('instrument_user')->where('instrument_id', $dropId)->delete();
                }
            }
            
            // Delete duplicate instruments
            DB::table('instruments')->whereIn('id', $ids)->delete();
        }

        // Add the unique index
        Schema::table('instruments', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instruments', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
