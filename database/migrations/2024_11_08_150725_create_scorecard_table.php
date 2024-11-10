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
        Schema::create('scorecard', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('criteria_id');
            $table->unsignedBigInteger('participant_id');
            $table->integer('score');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category')->onDelete('restrict');
            $table->foreign('criteria_id')->references('id')->on('criteria')->onDelete('restrict');
            $table->foreign('participant_id')->references('id')->on('participant')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scorecard');
    }
};
