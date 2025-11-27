<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarLocation extends Model
{
    use HasFactory;

    protected $fillable = ['car_id', 'lat', 'lng'];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
