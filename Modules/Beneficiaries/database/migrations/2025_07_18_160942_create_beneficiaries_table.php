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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('family_name')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->geometry('location', subtype: 'point')->nullable();
            $table->integer('number_of_individuals')->nullable();
            $table->enum('benefit_type', ['network', 'tanker']);
            $table->foreignId('distribution_point_id')->constrained('distribution_points')->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive', 'relocated']);
            $table->text('notes')->nullable();
            $table->index(['benefit_type', 'distribution_point_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
