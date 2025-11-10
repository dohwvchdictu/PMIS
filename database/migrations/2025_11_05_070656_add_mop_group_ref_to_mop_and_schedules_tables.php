<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add to mop table
        Schema::table('mops', function (Blueprint $table) {
            $table->string('mop_group_ref')->nullable()->after('id')->comment('Reference number of the MopGroup');
        });

        // Add to bid_schedules table
        Schema::table('bid_schedules', function (Blueprint $table) {
            $table->string('mop_group_ref')->nullable()->after('id')->comment('Reference number of the MopGroup');
            // Optional: remove mop_id if not needed
            // $table->dropColumn('mop_id');
        });

        // Add to ntf_bid_schedules table
        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            $table->string('mop_group_ref')->nullable()->after('id')->comment('Reference number of the MopGroup');
            // $table->dropColumn('mop_id');
        });

        // Add to svp_details table
        Schema::table('pr_svps', function (Blueprint $table) {
            $table->string('mop_group_ref')->nullable()->after('id')->comment('Reference number of the MopGroup');
            // $table->dropColumn('mop_id');
        });
    }

    public function down(): void
    {
        Schema::table('mops', function (Blueprint $table) {
            $table->dropColumn('mop_group_ref');
        });

        Schema::table('bid_schedules', function (Blueprint $table) {
            $table->dropColumn('mop_group_ref');
        });

        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            $table->dropColumn('mop_group_ref');
        });

        Schema::table('pr_svps', function (Blueprint $table) {
            $table->dropColumn('mop_group_ref');
        });
    }
};
