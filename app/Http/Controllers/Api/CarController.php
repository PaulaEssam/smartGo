<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarLocation;
use Illuminate\Http\Request;
use App\Events\CarLocationUpdated;

class CarController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Car::query()->with('latestLocation');

    //     // Filters للفرونت
    //     if ($request->brand) $query->where('brand', 'like', '%' . $request->brand . '%');
    //     if ($request->min_price) $query->where('price_per_hour', '>=', $request->min_price);
    //     if ($request->max_price) $query->where('price_per_hour', '<=', $request->max_price);

    //     $cars = $query->where('available', true)->get();

    //     return response()->json($cars);
    // }

    public function index(Request $request)
{
    try {
        $query = Car::query();

        // لو عايز تحمل الـ location بس لو موجودة
        $query->with(['latestLocation' => function ($q) {
            $q->select('car_id', 'lat', 'lng', 'address'); // اختار الحقول اللي عايزها بس
        }]);

        // الفلاتر
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_hour', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }

        $cars = $query->where('available', true)->get();

        return response()->json($cars);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Cars API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'message' => 'حدث خطأ في جلب العربيات',
            'error' => $e->getMessage(),
            'cars' => Car::select('id', 'brand', 'model', 'color', 'plate_number', 'price_per_day', 'available')->where('available', true)->get()
        ], 200); // نرجع 200 ومعاه الداتا الأساسية حتى لو الـ relation فشل
    }
}

    public function show(Car $car)
    {
        $car->load('latestLocation');
        return response()->json($car);
    }


    // يُستدعى من التطبيق كل 10 ثواني أثناء الرحلة
    public function updateLocation(Request $request, Car $car)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        // حفظ في جدول التتبع
        $location = CarLocation::create([
            'car_id' => $car->id,
            'lat' => $request->lat,
            'lng' => $request->lng
        ]);

        // تحديث الموقع الحالي في جدول السيارات
        $car->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng
        ]);

        // Broadcast لكل اللي متابعين السيارة
        broadcast(new CarLocationUpdated($car, $request->lat, $request->lng));

        return response()->json(['message' => 'Location updated']);
    }



    public function store(Request $request)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
