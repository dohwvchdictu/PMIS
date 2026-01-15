<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pr_svps', function (Blueprint $table) {
            $table->string('philgeps_posting_ref_no')->nullable()->after('uid');
            $table->date('ads_post_ib')->nullable()->after('philgeps_posting_ref_no');

            $table->renameColumn('resolution_number', 'resolution_number_mop');
        });
    }

    public function down(): void
    {
        Schema::table('pr_svps', function (Blueprint $table) {
            $table->dropColumn(['philgeps_posting_ref_no', 'ads_post_ib']);

            $table->renameColumn('resolution_number_mop', 'resolution_number');
        });
    }
};
