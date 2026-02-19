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
        // Create new pmu_po table
        Schema::create('pmu_po', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_id');
            $table->decimal('contract_amount', 15, 2)->nullable();
            $table->string('po_contract_number')->nullable();
            $table->string('po_contract_number_link')->nullable();
            $table->date('contract_signing_date')->nullable();
            $table->date('notice_to_proceed_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ref_id')->references('id')->on('pmus')->onDelete('cascade');
        });

        // Remove moved columns from pmus table
        Schema::table('pmus', function (Blueprint $table) {
            $table->dropColumn([
                'contract_amount',
                'po_contract_number',
                'po_contract_number_link',
                'contract_signing_date',
                'notice_to_proceed_date',
                'remarks',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore columns to pmus table
        Schema::table('pmus', function (Blueprint $table) {
            $table->decimal('contract_amount', 15, 2)->nullable();
            $table->string('po_contract_number')->nullable();
            $table->string('po_contract_number_link')->nullable();
            $table->date('contract_signing_date')->nullable();
            $table->date('notice_to_proceed_date')->nullable();
            $table->text('remarks')->nullable();
        });

        Schema::dropIfExists('pmu_po');
    }
};
