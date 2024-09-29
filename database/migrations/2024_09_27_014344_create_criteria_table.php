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
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('category_id');
            $table->string('criteria_id')->unique();
            $table->string('criteria_name');
            $table->string('criteria_score');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};