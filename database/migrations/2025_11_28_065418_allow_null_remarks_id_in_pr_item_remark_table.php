<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pr_item_remark', function (Blueprint $table) {
            $table->unsignedBigInteger('remarks_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pr_item_remark', function (Blueprint $table) {
            $table->unsignedBigInteger('remarks_id')->nullable(false)->change();
        });
    }
};
