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
            $table->date('date_coa_stamped_received')->nullable()->after('po_date_deadline');
            $table->date('date_po_receipt_by_supplier')->nullable()->after('date_coa_stamped_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->dropColumn(['date_coa_stamped_received', 'date_po_receipt_by_supplier']);
        });
    }
};
