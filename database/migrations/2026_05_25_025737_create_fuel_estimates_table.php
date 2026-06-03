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
        Schema::create('fuel_estimates', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel vehicles. Sesuaikan tipe data ID jika menggunakan UUID.
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('registration'); 
            $table->dateTime('start_timestamp');
            $table->dateTime('end_timestamp');
            $table->decimal('start_liters', 8, 2)->nullable();
            $table->decimal('end_liters', 8, 2)->nullable();
            $table->decimal('estimated_fuel_used', 12, 4)->nullable();
            $table->boolean('calibrated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_estimates');
    }
};
