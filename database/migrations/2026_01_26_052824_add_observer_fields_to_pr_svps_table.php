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
        Schema::table('pr_svps', function (Blueprint $table) {
            $table->text('list_invited_observers')->nullable()->after('ads_post_ib');
            $table->date('obsrvr_prebid_conf')->nullable()->after('list_invited_observers');
            $table->date('obsrvr_eligibility')->nullable()->after('obsrvr_prebid_conf');
            $table->date('obsrvr_sub_open_of_bid')->nullable()->after('obsrvr_eligibility');
            $table->date('obsrvr_bid')->nullable()->after('obsrvr_sub_open_of_bid');
            $table->date('obsrvr_post_qual')->nullable()->after('obsrvr_bid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pr_svps', function (Blueprint $table) {
            $table->dropColumn([
                'list_invited_observers',
                'obsrvr_prebid_conf',
                'obsrvr_eligibility',
                'obsrvr_sub_open_of_bid',
                'obsrvr_bid',
                'obsrvr_post_qual',
            ]);
        });
    }
};
