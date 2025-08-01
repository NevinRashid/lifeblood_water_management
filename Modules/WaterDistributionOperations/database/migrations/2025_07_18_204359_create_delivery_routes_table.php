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
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('description')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled']);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->geometry('path', subtype: 'lineString')->nullable();
            $table->foreignId('user_tanker_id')->constrained('user_tanker')->cascadeOnDelete();
            $table->date('planned_date')->nullable();
            $table->index(['status', 'planned_date', 'user_tanker_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_routes');
    }
};
