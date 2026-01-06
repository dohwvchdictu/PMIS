<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Change auditable_id from unsignedBigInteger to string
            $table->string('auditable_id', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Revert back to unsignedBigInteger
            $table->unsignedBigInteger('auditable_id')->change();
        });
    }
};