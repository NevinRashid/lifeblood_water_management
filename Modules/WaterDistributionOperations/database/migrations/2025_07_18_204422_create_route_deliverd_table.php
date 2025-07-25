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
        Schema::create('route_deliveries', function (Blueprint $table) {
            $table->id();
            $table->decimal('water_amount_delivered', 15, 4);
            $table->dateTime('arrival_time')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('delivery_route_id')->constrained('delivery_routes')->cascadeOnDelete();
            $table->foreignId('distribution_point_id')->constrained('distribution_points')->cascadeOnDelete();
            $table->index('distribution_point_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_deliverd');
    }
};
