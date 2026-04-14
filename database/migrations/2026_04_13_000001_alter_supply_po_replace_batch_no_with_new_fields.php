<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('supply_po', function (Blueprint $table) {
            $table->dropColumn('batch_no');
            $table->text('description')->nullable()->after('ref_id');
            $table->date('deadline')->nullable()->after('description');
            $table->date('date_of_delivery')->nullable()->after('deadline');
            $table->date('date_of_acceptance')->nullable()->after('date_of_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('supply_po', function (Blueprint $table) {
            $table->dropColumn(['description', 'deadline', 'date_of_delivery', 'date_of_acceptance']);
            $table->string('batch_no')->nullable()->after('ref_id');
        });
    }
};
