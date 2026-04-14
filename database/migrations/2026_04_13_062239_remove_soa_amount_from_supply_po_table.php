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
        Schema::table('supply_po', function (Blueprint $table) {
            $table->dropColumn('soa_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_po', function (Blueprint $table) {
            $table->decimal('soa_amount', 15, 2)->nullable()->after('date_received_from_end_user');
        });
    }
};
