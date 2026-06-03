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
        Schema::create('fuel_consumed_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('registration');
            $table->double('start_period_liters')->nullable();
            $table->timestamp('start_period_timestamp')->nullable();
            $table->double('end_period_liters')->nullable();
            $table->timestamp('end_period_timestamp')->nullable();
            $table->double('estimated_fuel_used')->nullable();
            $table->timestamps();

            // Kunci unik untuk menghindari duplikasi data pada kendaraan dan rentang waktu yang sama
            $table->unique(['vehicle_id', 'start_period_timestamp', 'end_period_timestamp'], 'fuel_consumed_unique_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_consumed_levels');
    }
};
