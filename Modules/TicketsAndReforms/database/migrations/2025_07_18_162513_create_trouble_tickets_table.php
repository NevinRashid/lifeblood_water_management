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
        Schema::create('trouble_tickets', function (Blueprint $table) {
            $table->id();
            $table->enum('subject', ['leak','pipe_breaks','water_outages','low_pressure', 'overflow', 'sensor_failure', 'other']);
            $table->text('body');
            $table->geometry('location', subtype: 'point')->nullable();
            $table->enum('status', ['new', 'waiting_assignment', 'assigned', 'in_progress', 'fixed', 'rejected']);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->index(['user_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trouble_tickets');
    }
};
