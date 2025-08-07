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
        Schema::create('reservoirs_activity', function (Blueprint $table) {
            $table->id();
            $table->decimal('activity_level', 15, 4);
            $table->dateTime('activity_time');
            $table->decimal('amount', 15, 4)->nullable();
            $table->enum('triggered_by', ['manual_user', 'scada_system']);
            $table->enum('activity_type', ['filling_started', 'filling_ended', 'emptying_started', 'emptying_ended', 'overflow_detected', 'critical_low_level', 'level_restored_above_critical', 'manual_adjustment', 'pump_started_scada', 'valve_opened_scada']);
            $table->json('notes')->nullable();
            $table->foreignId('reservoir_id')->constrained('reservoirs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservoirs_activity');
    }
};
