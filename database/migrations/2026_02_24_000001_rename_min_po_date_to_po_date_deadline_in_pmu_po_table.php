<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->renameColumn('min_po_date', 'po_date_deadline');
        });
    }

    public function down(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->renameColumn('po_date_deadline', 'min_po_date');
        });
    }
};
