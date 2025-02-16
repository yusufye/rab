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
        Schema::create('order_titles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_mak_id');
            $table->string('title',50);
            $table->timestamps();

            $table->foreign('order_mak_id')->references('id')->on('order_maks');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_titles');
    }
};
