<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarDropLocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'lat', 'lng'];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];
}
