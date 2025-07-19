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
        Schema::create('water_quality_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_source_id')->constrained('water_sources')->cascadeOnDelete();
            $table->decimal('ph_level', 10, 4)->nullable();
            $table->decimal('dissolved_oxygen', 10, 4)->nullable();
            $table->decimal('total_dissolved_solids', 10, 4)->nullable();
            $table->decimal('turbidity', 10, 4)->nullable();
            $table->decimal('temperature', 10, 4)->nullable();
            $table->decimal('chlorine', 10, 4)->nullable();
            $table->decimal('nitrate', 10, 4)->nullable();
            $table->decimal('total_coliform_bacteria', 10, 4)->nullable();
            $table->dateTime('test_date');
            $table->boolean('meets_standard_parameters')->nullable();
            $table->index(['water_source_id', 'test_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_quality_tests');
    }
};
