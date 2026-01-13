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
            $table->string('notice_of_award_number')->nullable()->after('recommending_for_award');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_procurements', function (Blueprint $table) {
            $table->dropColumn('notice_of_award_number');
        });
    }
};
