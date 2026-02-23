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
            // Drop old FK (ref_id → pmus.id) first before altering the column
            $table->dropForeign(['ref_id']);
        });

        Schema::table('pmu_po', function (Blueprint $table) {
            // Add pmu_id to keep the PMU link
            $table->unsignedBigInteger('pmu_id')->nullable()->after('id');
            $table->foreign('pmu_id')->references('id')->on('pmus')->onDelete('cascade');

            // Change ref_id to string to reference procurements.procID
            $table->string('ref_id')->nullable()->change();
        });

        Schema::table('pmu_po', function (Blueprint $table) {
            // Now add FK on the string ref_id → procurements.procID
            $table->foreign('ref_id')->references('procID')->on('procurements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmu_po', function (Blueprint $table) {
            $table->dropForeign(['ref_id']);
            $table->dropForeign(['pmu_id']);
            $table->dropColumn('pmu_id');
        });

        Schema::table('pmu_po', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id')->nullable()->change();
        });

        Schema::table('pmu_po', function (Blueprint $table) {
            $table->foreign('ref_id')->references('id')->on('pmus')->onDelete('cascade');
        });
    }
};
