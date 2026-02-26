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
            // Rename existing po_date to min_po_date
            $table->renameColumn('po_date', 'min_po_date');
        });

        Schema::table('pmu_po', function (Blueprint $table) {
            // Add new po_date after min_po_date
            $table->date('po_date')->nullable()->after('min_po_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            if (Schema::hasColumn('pmu_po', 'po_date')) {
                $table->dropColumn('po_date');
            }
        });

        Schema::table('pmu_po', function (Blueprint $table) {
            if (Schema::hasColumn('pmu_po', 'min_po_date')) {
                $table->renameColumn('min_po_date', 'po_date');
            }
        });
    }
};
