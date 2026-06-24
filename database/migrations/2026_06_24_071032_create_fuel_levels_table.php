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
        Schema::create('fuel_levels', function (Blueprint $table) {
            $table->id();
            // Asumsi Anda sudah memiliki tabel 'vehicles' dengan field 'registration_number'
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('registration')->nullable();
            $table->decimal('start_liters', 8, 2)->nullable();
            $table->timestampTz('start_timestamp')->nullable(); // Menyesuaikan format waktu +07:00
            $table->decimal('end_liters', 8, 2)->nullable();
            $table->timestampTz('end_timestamp')->nullable();
            $table->decimal('estimated_fuel_used', 10, 4)->nullable();
            $table->boolean('calibrated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_levels');
    }
};
