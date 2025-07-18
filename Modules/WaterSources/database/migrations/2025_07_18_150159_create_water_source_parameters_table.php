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
        Schema::create('water_source_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_source_id')->constrained('water_sources')->cascadeOnDelete();
            $table->foreignId('testing_parameters_id')->constrained('testing_parameters')->cascadeOnDelete();
            // $table->unique(['water_source_id', 'testing_parameters_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_source_parameters');
    }
};
