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
        Schema::create('reforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trouble_ticket_id')->constrained('trouble_tickets')->cascadeOnDelete();
            $table->text('description');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed']);
            $table->decimal('reform_cost', 15, 4)->nullable();
            $table->text('materials_used')->nullable();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->dateTime('expected_start_date');
            $table->dateTime('expected_end_date');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->index(['trouble_ticket_id', 'team_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reforms');
    }
};
