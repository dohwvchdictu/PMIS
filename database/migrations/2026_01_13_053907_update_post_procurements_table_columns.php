<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('post_procurements', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('resolution_number', 'resolution_award_number');
            $table->renameColumn('recommending_for_award', 'resolution_award_date');

            // Drop columns
            $table->dropColumn(['bid_evaluation_date', 'post_qual_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_procurements', function (Blueprint $table) {
            // Restore the original column names
            $table->renameColumn('resolution_award_number', 'resolution_number');
            $table->renameColumn('resolution_award_date', 'recommending_for_award');

            // Restore the dropped columns (adjust data types as needed)
            $table->date('bid_evaluation_date')->nullable();
            $table->date('post_qual_date')->nullable();
        });
    }
};
