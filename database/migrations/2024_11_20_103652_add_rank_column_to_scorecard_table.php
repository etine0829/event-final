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
        Schema::table('scorecard', function (Blueprint $table) {
            $table->string('avg_score')->nullable()->after('score');
            $table->integer('rank')->nullable()->after('avg_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scorecard', function (Blueprint $table) {
            //
        });
    }
};