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
        Schema::create('cartrack_trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id')->unique(); // ID dari Cartrack
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('registration')->nullable();
            
            // Waktu
            $table->timestamp('start_timestamp')->nullable();
            $table->timestamp('end_timestamp')->nullable();
            $table->string('trip_duration')->nullable();
            $table->integer('trip_duration_seconds')->nullable();
            $table->string('idle_time')->nullable();
            $table->integer('idle_time_seconds')->nullable();
            $table->bigInteger('clock_start')->nullable();
            $table->bigInteger('clock_end')->nullable();
            
            // Lokasi & Jarak
            $table->text('start_location')->nullable();
            $table->text('end_location')->nullable();
            $table->bigInteger('start_odometer')->nullable();
            $table->bigInteger('end_odometer')->nullable();
            $table->integer('trip_distance')->nullable();
            $table->string('start_geofence_name')->nullable();
            $table->string('end_geofence_name')->nullable();
            
            // Performa
            $table->integer('max_speed')->nullable();
            
            // Driver
            $table->string('driver_id')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_surname')->nullable();
            
            // Koordinat (Menggunakan JSONB khusus PostgreSQL)
            $table->jsonb('start_coordinates')->nullable();
            $table->jsonb('end_coordinates')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartrack_trips');
    }
};
