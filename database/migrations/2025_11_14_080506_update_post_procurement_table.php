<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('post_procurements', function (Blueprint $table) {

            // 1. DROP FOREIGN KEYS FIRST
            if (Schema::hasColumn('post_procurements', 'procurement_stage_id')) {
                $table->dropForeign(['procurement_stage_id']);
            }

            if (Schema::hasColumn('post_procurements', 'remarks_id')) {
                $table->dropForeign(['remarks_id']);
            }

            // 2. DROP THE COLUMNS
            if (Schema::hasColumn('post_procurements', 'procurement_stage_id')) {
                $table->dropColumn('procurement_stage_id');
            }

            if (Schema::hasColumn('post_procurements', 'remarks_id')) {
                $table->dropColumn('remarks_id');
            }

            // 3. RENAME procID → mop_group_ref
            if (Schema::hasColumn('post_procurements', 'procID')) {
                $table->renameColumn('procID', 'mop_group_ref');
            }
        });
    }

    public function down()
    {
        Schema::table('post_procurements', function (Blueprint $table) {

            // Reverse rename
            if (Schema::hasColumn('post_procurements', 'mop_group_ref')) {
                $table->renameColumn('mop_group_ref', 'procID');
            }

            // Recreate columns
            if (!Schema::hasColumn('post_procurements', 'procurement_stage_id')) {
                $table->unsignedBigInteger('procurement_stage_id')->nullable();
                $table->foreign('procurement_stage_id')->references('id')->on('procurement_stages');
            }

            if (!Schema::hasColumn('post_procurements', 'remarks_id')) {
                $table->unsignedBigInteger('remarks_id')->nullable();
                $table->foreign('remarks_id')->references('id')->on('remarks');
            }
        });
    }
};
