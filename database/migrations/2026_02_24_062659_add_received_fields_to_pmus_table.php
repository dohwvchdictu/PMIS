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
        Schema::table('pmus', function (Blueprint $table) {
            $table->date('date_received')->nullable()->after('date_forwarded');
            $table->text('received_remarks')->nullable()->after('date_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmus', function (Blueprint $table) {
            $table->dropColumn(['date_received', 'received_remarks']);
        });
    }
};
