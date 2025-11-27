<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class AdminCarController extends Controller
{
    public function index()
    {
        return Car::with('latestLocation')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'brand' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'color' => 'required|string|max:255',
        'plate_number' => 'required|string|unique:cars,plate_number|max:20',
        'price_per_hour' => 'required|numeric|min:0',
        'price_per_day' => 'required|numeric|min:0',
        'price_per_week' => 'required|numeric|min:0',
        'price_per_month' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5048',
    ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('cars', 'public');
        }

        $car = Car::create($data);

        return response()->json($car, 201);
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
        'brand' => 'string|max:255',
        'model' => 'string|max:255',
        'color' => 'string|max:255',
        'plate_number' => 'string|max:20|unique:cars,plate_number,' . $car->id,
        'price_per_hour' => 'numeric|min:0',
        'price_per_day' => 'numeric|min:0',
        'price_per_week' => 'numeric|min:0',
        'price_per_month' => 'numeric|min:0',
        'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5048',
    ]);
        $car->update($request->all());
        return $car;
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return response()->json(['message' => 'Car deleted']);
    }
}
