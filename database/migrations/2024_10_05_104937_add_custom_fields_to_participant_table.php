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
        Schema::table('participant', function (Blueprint $table) {
            $table->string('custom_label_1')->nullable(); 
            $table->string('custom_value_1')->nullable(); 
            $table->string('custom_label_2')->nullable(); 
            $table->string('custom_value_2')->nullable(); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant', function (Blueprint $table) {
            $table->dropColumn(['custom_label_1', 'custom_value_1', 'custom_label_2', 'custom_value_2']);
        });
    }
};
