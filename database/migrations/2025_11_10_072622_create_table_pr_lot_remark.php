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
        Schema::create('pr_lot_remark', function (Blueprint $table) {
            $table->id();
            $table->string('procID');
            $table->unsignedBigInteger('remarks_id');
            $table->timestamp('remark_history')->nullable();
            $table->timestamps();

            $table->foreign('procID')->references('procID')->on('procurements')->onDelete('cascade');
            $table->foreign('remarks_id')->references('id')->on('remarks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_lot_remark');
    }
};
