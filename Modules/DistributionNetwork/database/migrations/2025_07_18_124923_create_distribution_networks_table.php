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
        Schema::create('distribution_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 255)->nullable();
            $table->geometry('zone', subtype: 'polygon')->nullable();
            $table->foreignId('water_source_id')->constrained('water_sources')->cascadeOnDelete();
            $table->index('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_networks');
    }
};
