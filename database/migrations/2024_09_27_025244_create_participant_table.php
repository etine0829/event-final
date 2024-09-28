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
        Schema::create('participant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('participant_id')->unique();
            $table->string('participant_photo');
            $table->string('participant_fname');
            $table->string('participant_middleIniatial');
            $table->string('participant_lname');
            $table->string('participant_gender');
            $table->string('participant_comment');
            $table->string('participant_department');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant');
    }
};
