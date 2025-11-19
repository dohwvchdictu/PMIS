<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table and the column exist before attempting to rename
        if (Schema::hasTable('post_procurements') && Schema::hasColumn('post_procurements', 'mop_group_ref')) {
            Schema::table('post_procurements', function (Blueprint $table) {
                // Rename mop_group_ref to ref_id
                $table->renameColumn('mop_group_ref', 'ref_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the table and the new column exist before attempting to rename back
        if (Schema::hasTable('post_procurements') && Schema::hasColumn('post_procurements', 'ref_id')) {
            Schema::table('post_procurements', function (Blueprint $table) {
                // Rename ref_id back to mop_group_ref
                $table->renameColumn('ref_id', 'mop_group_ref');
            });
        }
    }
};
