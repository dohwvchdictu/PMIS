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
        Schema::table('mops', function (Blueprint $table) {
            // Add UID column (unique identifier per MOP)
            if (!Schema::hasColumn('mops', 'uid')) {
                $table->string('uid')->unique()->after('id');
            }

            // Track original and current Mode of Procurement
            if (!Schema::hasColumn('mops', 'original_mode_of_procurement_id')) {
                $table->foreignId('original_mode_of_procurement_id')
                    ->nullable()
                    ->after('mode_of_procurement_id')
                    ->constrained('modes_of_procurements')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('mops', 'current_mode_of_procurement_id')) {
                $table->foreignId('current_mode_of_procurement_id')
                    ->nullable()
                    ->after('original_mode_of_procurement_id')
                    ->constrained('modes_of_procurements')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mops', function (Blueprint $table) {
            $table->dropForeign(['original_mode_of_procurement_id']);
            $table->dropForeign(['current_mode_of_procurement_id']);

            $table->dropColumn([
                'uid',
                'original_mode_of_procurement_id',
                'current_mode_of_procurement_id',
            ]);
        });
    }
};
