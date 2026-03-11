<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fund_sources', function (Blueprint $table) {
            $table->foreignId('fund_source_group_id')
                ->nullable()
                ->after('fundsources')
                ->constrained('fund_source_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fund_sources', function (Blueprint $table) {
            $table->dropForeign(['fund_source_group_id']);
            $table->dropColumn('fund_source_group_id');
        });
    }
};
