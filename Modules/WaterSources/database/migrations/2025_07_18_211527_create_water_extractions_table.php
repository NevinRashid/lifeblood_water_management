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
        Schema::create('water_extractions', function (Blueprint $table) {
            $table->id();
            $table->decimal('extracted', 15, 4);
            $table->dateTime('extraction_date');

            $table->decimal('delivered_amount', 15, 4)->default(0); // Actual quantity that arrived (extracted - loss)
            $table->decimal('lost_amount', 15, 4)->default(0); // Quantity that was waste

            $table->foreignId('distribution_network_id')->constrained('distribution_networks')->cascadeOnDelete();
            $table->foreignId('water_source_id')->constrained('water_sources')->cascadeOnDelete();

            $table->index(['water_source_id', 'extraction_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_extractions');
    }
};
