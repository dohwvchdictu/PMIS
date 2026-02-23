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
        Schema::table('pr_lot_prstage', function (Blueprint $table) {
            $table->date('actual_date_forwarded')->nullable()->after('stage_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pr_lot_prstage', function (Blueprint $table) {
            $table->dropColumn('actual_date_forwarded');
        });
    }
};
