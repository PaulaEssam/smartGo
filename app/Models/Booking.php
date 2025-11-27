<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\BroadcastsEvents;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'car_id', 'pickup_location_id', 'dropoff_location_id',
        'start_time', 'end_time', 'total_price', 'distance_traveled',
        'qr_code', 'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_price' => 'decimal:2',
        'distance_traveled' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(CarDropLocation::class, 'pickup_location_id');
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(CarDropLocation::class, 'dropoff_location_id');
    }
}
