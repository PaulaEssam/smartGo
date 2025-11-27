<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Booking;

use App\Models\Car;
use App\Models\CarDropLocation;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\TripSummaryMail;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'car_id' => 'required|exists:cars,id',
        'pickup_location_id' => 'required|exists:car_drop_locations,id',
        'start_time' => 'required|date|after:now',
    ]);

    $car = Car::findOrFail($request->car_id);
    if (!$car->available) {
        return response()->json(['message' => 'Car not available'], 400);
    }

    $booking = Booking::create([
        'user_id' => auth()->id(),
        'car_id' => $car->id,
        'pickup_location_id' => $request->pickup_location_id,
        'start_time' => $request->start_time,
        'status' => 'pending'
    ]);

    // QR Code واضح جدًا ومضمون 100%
    $qrData = "SMARTGO|BOOKING:{$booking->id}|CAR:{$car->id}|USER:" . auth()->id();

    $qrPath = 'qrcodes/booking-' . $booking->id . '.png';
    $fullPath = storage_path('app/public/' . $qrPath);

    // تأكد إن المجلد موجود
    if (!file_exists(dirname($fullPath))) {
        mkdir(dirname($fullPath), 0755, true);
    }

    $options = new \chillerlan\QRCode\QROptions([
        'version'    => 5,
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'   => \chillerlan\QRCode\QRCode::ECC_L,
        'scale'      => 20,
        'imageBase64' => false,
        'bgColor'    => [255, 255, 255],
        'imageTransparent' => false,
    ]);

    $qrcode = new \chillerlan\QRCode\QRCode($options);
    $qrcode->render($qrData, $fullPath);

    $booking->qr_code = $qrPath;
    $booking->save();

    $car->available = false;
    $car->save();

    return response()->json([
        'message' => 'Booking created successfully',
        'booking' => $booking->load('car', 'pickupLocation'),
        'qr_code_url' => asset('storage/' . $qrPath)
    ], 201);
}

    public function startTrip(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'active', 'start_time' => now()]);
        return response()->json(['message' => 'Trip started']);
    }

    public function endTrip(Request $request, Booking $booking)
    {
        $request->validate([
            'dropoff_location_id' => 'required|exists:car_drop_locations,id',
            'distance_traveled' => 'required|numeric'
        ]);

        if ($booking->user_id !== auth()->id() || $booking->status !== 'active') {
            return response()->json(['message' => 'Cannot end this trip'], 403);
        }

        $endTime = now();
        $startTime = $booking->start_time;
        $durationInSeconds = $endTime->diffInSeconds($startTime);
        $durationInHours = $endTime->diffInHours($startTime);
        $durationInDays = $endTime->diffInDays($startTime);
        $durationInWeeks = floor($durationInDays / 7);
        $durationInMonths = floor($durationInDays / 30);

        $car = $booking->car;

        $totalPrice = 0;

        if ($durationInMonths > 0) {
            $totalPrice += $durationInMonths * $car->price_per_month;
            $durationInDays -= $durationInMonths * 30;
        }
        if ($durationInWeeks > 0) {
            $totalPrice += $durationInWeeks * $car->price_per_week;
            $durationInDays -= $durationInWeeks * 7;
        }
        if ($durationInDays > 0) {
            $totalPrice += $durationInDays * $car->price_per_day;
        }
        $totalPrice += ($durationInHours % 24 + 1) * $car->price_per_hour; // +1 عشان أول ساعة

        $booking->update([
            'end_time' => $endTime,
            'dropoff_location_id' => $request->dropoff_location_id,
            'distance_traveled' => $request->distance_traveled,
            'total_price' => $totalPrice,
            'status' => 'finished'
        ]);

        // إعادة السيارة للتوفر
        $car->available = true;
        $car->save();

        // إرسال الإيميل
        Mail::to($booking->user->email)->send(new TripSummaryMail($booking));

        return response()->json([
            'message' => 'Trip ended successfully',
            'total_price' => $totalPrice,
            'duration' => [
                'months' => $durationInMonths,
                'weeks' => $durationInWeeks,
                'days' => $durationInDays,
                'hours' => $durationInHours % 24
            ]
        ]);
    }

    public function myBookings()
{
    $bookings = Booking::with(['car', 'pickupLocation', 'dropoffLocation'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get();

    return response()->json($bookings);
}




public function adminBookings()
{
    $bookings = Booking::with(['user', 'car', 'pickupLocation', 'dropoffLocation'])
                    ->latest()
                    ->get();

    return response()->json($bookings);
}
}
