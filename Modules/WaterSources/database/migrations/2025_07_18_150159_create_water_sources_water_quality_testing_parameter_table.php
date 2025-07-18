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
        Schema::create('water_sources_water_quality_testing_parameter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_source_id')->constrained('water_sources')->cascadeOnDelete();
            $table->foreignId('water_quality_testing_parameter_id')->constrained('water_quality_testing_parameters')->cascadeOnDelete();
            $table->unique(['water_source_id', 'water_quality_testing_parameter_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_sourcer_water_quality_testing_parameter');
    }
};
