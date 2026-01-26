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
            // Add philgeps_ref_no after ib_number (before pre_proc_conference)
            $table->string('philgeps_posting_ref_no')->nullable()->after('ib_number');

            // Add bid_evaluation_date and post_qualification_date after sub_open_bids
            $table->date('bid_evaluation_date')->nullable()->after('sub_open_bids');
            $table->date('post_qualification_date')->nullable()->after('bid_evaluation_date');

            // Add resolution_number_mop after bidding_result
            $table->string('resolution_number_mop')->nullable()->after('bidding_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bid_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'philgeps_posting_ref_no',
                'bid_evaluation_date',
                'post_qualification_date',
                'resolution_number_mop'
            ]);
        });
    }
};
