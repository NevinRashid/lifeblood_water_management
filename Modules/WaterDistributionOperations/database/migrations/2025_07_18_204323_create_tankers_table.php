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
        Schema::create('tankers', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate')->unique();
            $table->decimal('max_capacity', 15, 4);
            $table->enum('status', ['available', 'on_route', 'in_maintenance', 'out_of_service']);
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('note')->nullable();
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tankers');
    }
};
