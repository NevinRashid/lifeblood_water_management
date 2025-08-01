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
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->foreign('manager_id')->references('id')->on('users')->nullOnDelete();
            $table->json('name');
            $table->json('address')->nullable();
            $table->geometry('zone', subtype: 'polygon')->nullable();
            $table->foreignId('water_source_id')->constrained('water_sources')->cascadeOnDelete();
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
