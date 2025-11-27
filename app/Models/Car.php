<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand', 'model', 'year', 'color', 'plate_number',
        'price_per_hour', 'price_per_day', 'price_per_week', 'price_per_month',
        'image', 'available', 'current_lat', 'current_lng'
    ];

    protected $casts = [
        'available' => 'boolean',
        'current_lat' => 'decimal:8',
        'current_lng' => 'decimal:8',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function locations()
    {
        return $this->hasMany(CarLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(CarLocation::class)->latestOfMany();
    }
}
