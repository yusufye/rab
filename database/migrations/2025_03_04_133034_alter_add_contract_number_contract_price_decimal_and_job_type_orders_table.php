<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('contract_number',25)->nullable()->after('updated_by');
            $table->decimal('contract_price',18,2)->nullable()->after('contract_number');
            $table->string('job_type',25)->nullable()->after('contract_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('contract_number',25);
            $table->dropColumn('contract_price',18,2);
            $table->dropColumn('job_type',25);
        });
    }
};
