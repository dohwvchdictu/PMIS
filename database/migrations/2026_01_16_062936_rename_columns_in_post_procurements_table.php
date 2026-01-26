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
        Schema::table('post_procurements', function (Blueprint $table) {
            $table->renameColumn('date_of_posting_of_award_on_philgeps', 'philgeps_posting_of_award');
            $table->renameColumn('award_notice_no', 'philgeps_notice_of_award_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_procurements', function (Blueprint $table) {
            $table->renameColumn('philgeps_posting_of_award', 'date_of_posting_of_award_on_philgeps');
            $table->renameColumn('philgeps_notice_of_award_no', 'award_notice_no');
        });
    }
};
