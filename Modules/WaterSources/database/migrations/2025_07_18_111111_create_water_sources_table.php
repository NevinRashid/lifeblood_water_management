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
        Schema::create('water_sources', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->enum('source', ['well', 'river', 'lake', 'dam', 'spring', 'desalination', 'imported']);
            $table->geometry('location', subtype: 'point');
            $table->decimal('capacity_per_day', 15, 4)->nullable();
            $table->decimal('capacity_per_hour', 15, 4)->nullable();
            $table->enum('status', ['active', 'inactive', 'damaged', 'under_repair']);
            $table->date('operating_date')->nullable();
            $table->spatialIndex('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_sources');
    }
};
