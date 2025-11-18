<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The tables that need updating.
     */
    protected $tables = [
        'bid_schedules',
        'ntf_bid_schedules',
        'pr_svps'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    // Check if the old column exists before trying to rename
                    if (Schema::hasColumn($table->getTable(), 'mop_group_ref')) {
                        $table->renameColumn('mop_group_ref', 'ref_id');
                    }
                    // If it doesn't exist, and ref_id doesn't exist, maybe we need to create it?
                    // (Optional safety: if neither exists, create ref_id)
                    elseif (!Schema::hasColumn($table->getTable(), 'ref_id')) {
                        $table->unsignedBigInteger('ref_id')->nullable()->index();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'ref_id')) {
                        $table->renameColumn('ref_id', 'mop_group_ref');
                    }
                });
            }
        }
    }
};
