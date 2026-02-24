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
            $table->string('ntp_link', 2048)->nullable()->after('po_contract_number_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->dropColumn('ntp_link');
        });
    }
};
