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

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('household_size');
            $table->unsignedInteger('children_count')->nullable()->default(0);
            $table->unsignedInteger('elderly_count')->nullable()->default(0);
            $table->unsignedInteger('disabled_count')->nullable()->default(0);

            $table->enum('benefit_type', ['network', 'tanker', 'other'])->default('network');

            $table->foreignId('distribution_point_id')->constrained('distribution_points')->cascadeOnDelete();

            $table->geometry('location', subtype: 'point');
            $table->json('address');

            $table->enum('status', ['active', 'inactive', 'suspended', 'relocated'])->default('active');

            $table->json('additional_data')->nullable();
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
