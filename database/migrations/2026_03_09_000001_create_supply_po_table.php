<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supply_po', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->cascadeOnDelete();
            $table->unsignedBigInteger('ref_id'); // pmu_po ref_id (procID or prItemID)
            $table->string('batch_no')->nullable();
            $table->date('delivery_completion')->nullable();
            $table->datetime('date_received_from_end_user')->nullable();
            $table->decimal('soa_amount', 15, 2)->nullable();
            $table->datetime('date_forwarded_to_budget')->nullable();
            $table->timestamps();

            $table->unique(['supply_id', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_po');
    }
};
