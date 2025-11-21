<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('ntf_bid_schedules_uid_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            // Re-add the unique constraint if rolling back
            $table->unique('uid');
        });
    }
};
