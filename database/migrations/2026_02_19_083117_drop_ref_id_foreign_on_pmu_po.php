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
            $table->dropForeign(['ref_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // FK is not restored — ref_id now holds mixed procID/prItemID values
    }
};
