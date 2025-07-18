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
        Schema::create('valves', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->geometry('location', subtype: 'point');
            $table->boolean('is_open');
            $table->enum('valve_type', ['gate_valve', 'butterfly_valve', 'ball_valve']);
            $table->enum('status', ['active', 'inactive', 'damaged', 'under_repair']);
            $table->foreignId('distribution_network_id')->constrained('distribution_networks')->cascadeOnDelete();
            $table->decimal('current_flow', 10, 4)->nullable();
            $table->spatialIndex('location');
            $table->index('distribution_network_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valves');
    }
};
