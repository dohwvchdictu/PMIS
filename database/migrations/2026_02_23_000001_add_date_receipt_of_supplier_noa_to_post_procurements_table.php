<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('post_procurements', function (Blueprint $table) {
            $table->date('date_receipt_of_supplier_noa')->nullable()->after('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::table('post_procurements', function (Blueprint $table) {
            $table->dropColumn('date_receipt_of_supplier_noa');
        });
    }
};
