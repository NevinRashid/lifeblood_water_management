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
        Schema::create('sensors_readings', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 15, 4);
            $table->string('unit', 50)->nullable();
            $table->dateTime('recorded_at');
            $table->foreignId('sensor_id')->constrained('sensors')->cascadeOnDelete();
            $table->index(['sensor_id', 'recorded_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors_readings');
    }
};
