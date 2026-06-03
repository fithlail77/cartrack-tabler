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
        Schema::create('vehicle_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->date('activity_date'); // Menyimpan tanggal filter aktivitas
            $table->string('registration')->nullable();
            $table->string('chassis_number')->nullable();
            
            // Menggunakan string/timestamp untuk waktu mesin hidup/mati
            $table->timestamp('first_ignition_on')->nullable();
            $table->timestamp('last_ignition_off')->nullable();
            
            // Waktu dalam hitungan detik (integer)
            $table->integer('idle_time_seconds')->default(0);
            $table->integer('driving_time_seconds')->default(0);
            
            // Format waktu durasi kerja berupa string (HH:MM) dari JSON
            $table->string('total_working_hours')->nullable();
            $table->string('total_break_hours')->nullable();
            $table->string('total_break_time_trimmed')->nullable();
            
            // Data Driver (Bisa bernilai null di JSON)
            $table->string('driver_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();

            // Menambahkan index unik untuk sinkronisasi data yang akurat
            $table->unique(['vehicle_id', 'activity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_activities');
    }
};
