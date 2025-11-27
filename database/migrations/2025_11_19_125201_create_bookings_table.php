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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pickup_location_id')->constrained('car_drop_locations');
            $table->foreignId('dropoff_location_id')->nullable()->constrained('car_drop_locations');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('distance_traveled', 8, 2)->nullable();
            $table->string('qr_code')->nullable(); // path to QR image
            $table->string('status')->default('pending'); // pending, active, finished, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
