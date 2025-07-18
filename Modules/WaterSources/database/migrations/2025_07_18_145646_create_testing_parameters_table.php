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
        Schema::create('testing_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('minimum_level', 10, 4)->nullable();
            $table->decimal('maximum_level', 10, 4)->nullable();
            $table->index('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_parameters');
    }
};
