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
        Schema::table('bid_schedules', function (Blueprint $table) {
            $table->dropColumn('bidding_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bid_schedules', function (Blueprint $table) {
            $table->date('bidding_date')->nullable()->after('post_qualification_date');
        });
    }
};
