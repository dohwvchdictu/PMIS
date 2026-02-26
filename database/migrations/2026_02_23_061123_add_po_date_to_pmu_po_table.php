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
            $table->date('po_date')->nullable()->after('ref_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pmu_po', 'po_date')) {
            Schema::table('pmu_po', function (Blueprint $table) {
                $table->dropColumn('po_date');
            });
        }
    }
};
