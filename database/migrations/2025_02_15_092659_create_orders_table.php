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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('prev_id')->nullable();
            $table->string('job_number',50);
            $table->enum('status',['DRAFT','TO REVIEW','RELEASED','APPROVED','CLOSED']);
            $table->integer('approval_step')->default(0);
            $table->integer('rev')->default(0);
            $table->string('title',100);
            $table->string('study_lab',100)->nullable();
            $table->string('group',100)->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->unsignedBigInteger('category_id');
            $table->decimal('price',18,2)->nullable();
            $table->decimal('split_price',18,2)->nullable();
            $table->string('split_to',50)->nullable();
            $table->decimal('profit',18,2)->nullable();
            $table->string('customer',50)->nullable();
            $table->text('reviewed_notes')->nullable();
            $table->datetime('reviewed_datetime')->nullable();
            $table->unsignedBigInteger('released_by')->nullable();
            $table->unsignedBigInteger('approved_1_by')->nullable();
            $table->datetime('approved_date_1')->nullable();
            $table->unsignedBigInteger('approved_2_by')->nullable();
            $table->datetime('approved_date_2')->nullable();
            $table->unsignedBigInteger('approved_3_by')->nullable();
            $table->datetime('approved_date_3')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categorys');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
