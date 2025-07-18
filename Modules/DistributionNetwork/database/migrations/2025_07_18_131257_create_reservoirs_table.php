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
        Schema::create('reservoirs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->geometry('location', subtype: 'point');
            $table->enum('tank_type', ['main', 'sub']);
            $table->decimal('maximum_capacity', 15, 4);
            $table->decimal('minimum_critical_level', 15, 4);
            $table->enum('status', ['active', 'inactive', 'damaged', 'under_repair']);
            $table->foreignId('distribution_network_id')->constrained('distribution_networks')->cascadeOnDelete();
            $table->spatialIndex('location');
            $table->index(['status', 'tank_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservoirs');
    }
};
