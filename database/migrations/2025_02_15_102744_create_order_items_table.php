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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_title_id');
            $table->string('item',100);
            $table->integer('qty_1')->nullable();
            $table->string('unit_1',10)->nullable();
            $table->integer('qty_2')->nullable();
            $table->string('unit_2',10)->nullable();
            $table->integer('qty_3')->nullable();
            $table->string('unit_3',10)->nullable();
            $table->integer('qty_total')->default(0);
            $table->string('qty_unit',10);
            $table->decimal('price_unit',18,2);
            $table->decimal('total_price',18,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
