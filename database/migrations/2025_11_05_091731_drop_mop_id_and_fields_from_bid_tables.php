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
        // Drop foreign key first, then mop_id in bid_schedules
        Schema::table('bid_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('bid_schedules', 'mop_id')) {
                $table->dropForeign(['mop_id']); // Drop FK first
                $table->dropColumn('mop_id');
            }
        });

        // Drop foreign key first, then mop_id in ntf_bid_schedules
        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            $columnsToDrop = ['mop_id', 'bidding_date', 'bidding_result'];
            if (Schema::hasColumn('ntf_bid_schedules', 'mop_id')) {
                $table->dropForeign(['mop_id']);
                $table->dropColumn('mop_id');
            }
            if (Schema::hasColumn('ntf_bid_schedules', 'bidding_date')) {
                $table->dropColumn('bidding_date');
            }
            if (Schema::hasColumn('ntf_bid_schedules', 'bidding_result')) {
                $table->dropColumn('bidding_result');
            }
        });

        // Drop foreign key first, then mop_id in pr_svps
        Schema::table('pr_svps', function (Blueprint $table) {
            if (Schema::hasColumn('pr_svps', 'mop_id')) {
                $table->dropForeign(['mop_id']);
                $table->dropColumn('mop_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bid_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('bid_schedules', 'mop_id')) {
                $table->unsignedBigInteger('mop_id')->nullable()->after('id');
                $table->foreign('mop_id')->references('id')->on('mops')->onDelete('cascade');
            }
        });

        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('ntf_bid_schedules', 'mop_id')) {
                $table->unsignedBigInteger('mop_id')->nullable()->after('id');
                $table->foreign('mop_id')->references('id')->on('mops')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ntf_bid_schedules', 'bidding_date')) {
                $table->date('bidding_date')->nullable()->after('mop_id');
            }
            if (!Schema::hasColumn('ntf_bid_schedules', 'bidding_result')) {
                $table->string('bidding_result')->nullable()->after('bidding_date');
            }
        });

        Schema::table('pr_svps', function (Blueprint $table) {
            if (!Schema::hasColumn('pr_svps', 'mop_id')) {
                $table->unsignedBigInteger('mop_id')->nullable()->after('id');
                $table->foreign('mop_id')->references('id')->on('mops')->onDelete('cascade');
            }
        });
    }
};
