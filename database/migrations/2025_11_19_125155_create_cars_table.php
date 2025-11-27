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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('color');
            $table->string('plate_number')->unique();
            $table->decimal('price_per_hour', 8, 2);
            $table->decimal('price_per_day', 8, 2);
            $table->decimal('price_per_week', 8, 2);
            $table->decimal('price_per_month', 8, 2);
            $table->string('image')->nullable();
            $table->boolean('available')->default(true);
            $table->decimal('current_lat', 10, 8)->nullable();
            $table->decimal('current_lng', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
