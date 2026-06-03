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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // Jadikan vehicle_id sebagai unique key untuk keperluan upsert PostgreSQL
            $table->unsignedBigInteger('vehicle_id')->unique(); 
            $table->string('registration')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->integer('max_speed')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->integer('model_year')->nullable();
            $table->string('colour')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
