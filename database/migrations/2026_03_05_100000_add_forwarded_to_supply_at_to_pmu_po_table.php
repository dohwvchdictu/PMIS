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
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->timestamp('forwarded_to_supply_at')->nullable()->after('manual_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->dropColumn('forwarded_to_supply_at');
        });
    }
};
