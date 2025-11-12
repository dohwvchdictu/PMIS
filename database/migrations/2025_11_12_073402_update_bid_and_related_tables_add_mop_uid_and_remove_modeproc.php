<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        // 1) Remove modeproc (only if exists)
        Schema::table('bid_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('bid_schedules', 'modeproc')) {
                // dropping a column requires doctrine/dbal in some setups
                $table->dropColumn('modeproc');
            }
        });

        if (!Schema::hasColumn('bid_schedules', 'mop_uid')) {
            // Example using AFTER:
            DB::statement("
                ALTER TABLE `bid_schedules`
                ADD COLUMN `mop_uid` CHAR(36) NULL AFTER `mop_group_ref`;
            ");
        }

        // ---- ntf_bid_schedules ----
        if (!Schema::hasColumn('ntf_bid_schedules', 'mop_uid')) {
            DB::statement("
                ALTER TABLE `ntf_bid_schedules`
                ADD COLUMN `mop_uid` CHAR(36) NULL AFTER `mop_group_ref`;
            ");
            // or FIRST as above
        }

        // ---- pr_svps ----
        if (!Schema::hasColumn('pr_svps', 'mop_uid')) {
            DB::statement("
                ALTER TABLE `pr_svps`
                ADD COLUMN `mop_uid` CHAR(36) NULL AFTER `mop_group_ref`;
            ");
        }

    }

    public function down()
    {
        // Drop mop_uid on each table if exists
        Schema::table('bid_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('bid_schedules', 'mop_uid')) {
                $table->dropColumn('mop_uid');
            }
        });

        Schema::table('ntf_bid_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('ntf_bid_schedules', 'mop_uid')) {
                $table->dropColumn('mop_uid');
            }
        });

        Schema::table('pr_svps', function (Blueprint $table) {
            if (Schema::hasColumn('pr_svps', 'mop_uid')) {
                $table->dropColumn('mop_uid');
            }
        });

        // Recreate modeproc in bid_schedules as nullable string (adjust if original type differs)
        Schema::table('bid_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('bid_schedules', 'modeproc')) {
                $table->string('modeproc')->nullable();
            }
        });
    }
};
