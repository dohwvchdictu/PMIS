<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('schedule_for_pr')
            ->whereNull('thirty_percent')
            ->whereNotNull('ABC')
            ->update(['thirty_percent' => DB::raw('(ABC * 0.30)')]);
    }

    public function down(): void
    {
        // Not reversible — backfill only
    }
};
