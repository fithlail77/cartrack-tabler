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
        Schema::create('fuel_fills', function (Blueprint $table) {
            $table->id();
            // Asumsi Anda memiliki tabel vehicles
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('registration');
            // Catatan: Anda menggunakan 'ammount' (double m), saya mengikuti penamaan dari Anda
            $table->decimal('fill_ammount_litres', 8, 2)->nullable(); 
            $table->dateTime('fill_timestamp')->nullable();
            $table->decimal('fill_odometer', 15, 2)->nullable();
            $table->text('fill_location')->nullable();
            // Presisi desimal yang optimal untuk PostgreSQL (PostGIS standar)
            $table->decimal('latitude', 10, 8)->nullable(); 
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('accurate')->default(false);
            $table->timestamps();

            // Membuat kombinasi unik agar data yang sama di-update, bukan duplikat
            $table->unique(['registration', 'fill_timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_fills');
    }
};
