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
        Schema::create('pmus', function (Blueprint $table) {
            $table->id();
            $table->string('notice_of_award_number')->unique();
            $table->date('date_forwarded')->nullable();
            $table->decimal('contract_amount', 15, 2)->nullable();
            $table->string('po_contract_number')->nullable();
            $table->date('contract_signing_date')->nullable();
            $table->date('notice_to_proceed_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmus');
    }
};
