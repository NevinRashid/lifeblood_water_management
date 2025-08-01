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
        Schema::create('distribution_points', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->geometry('location', subtype: 'point');
            $table->enum('type', ['tanker', 'water tap']);
            $table->enum('status', ['active', 'inactive', 'damaged', 'under_repair']);
            $table->foreignId('distribution_network_id')->constrained('distribution_networks')->cascadeOnDelete();
            $table->spatialIndex('location');
            $table->index(['type', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_points');
    }
};
