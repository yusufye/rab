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
            $table->text('approval_rejected_notes')->nullable()->after('reviewed_datetime');
            $table->integer('approval_rejected_by')->nullable()->after('approval_rejected_notes');
            $table->datetime('approval_rejected_datetime')->nullable()->after('approval_rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('approval_rejected_notes');
            $table->dropColumn('approval_rejected_by');
            $table->dropColumn('approval_rejected_datetime');
        });
    }
};
