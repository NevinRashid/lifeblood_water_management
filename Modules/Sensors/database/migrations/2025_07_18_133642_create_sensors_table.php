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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->string('name');
            $table->geometry('location', subtype: 'point')->nullable();
            $table->enum('sensor_type', ['pressure_sensor', 'flow_sensor', 'level_sensor', 'quality_sensor']);
            $table->enum('status', ['active', 'inactive', 'faulty', 'under_maintenance']);
            $table->morphs('sensorable');
            $table->index(['sensor_type', 'status']);
            $table->index(['sensorable_id', 'sensorable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
