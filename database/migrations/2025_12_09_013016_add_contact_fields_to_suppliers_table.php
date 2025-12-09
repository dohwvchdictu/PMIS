<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('tin')->nullable()->after('slug');
            $table->string('address')->nullable()->after('tin');
            $table->string('mobile')->nullable()->after('address');
            $table->string('telephone')->nullable()->after('mobile');
            $table->string('email')->nullable()->after('telephone');
            $table->string('contact_person')->nullable()->after('email');
            $table->text('remarks')->nullable()->after('contact_person');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'tin',
                'address',
                'mobile',
                'telephone',
                'email',
                'contact_person',
                'remarks'
            ]);
        });
    }
};
