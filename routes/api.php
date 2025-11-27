<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\DropLocationController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\AdminCarController;
use App\Http\Controllers\Api\AdminDropLocationController;
use App\Http\Controllers\Api\AdminUserController;

// ==================== غير محمية ====================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// تحقق الإيميل
Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware('auth:sanctum');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed');

// ==================== محمية بـ Sanctum (يوزر عادي) ====================
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // معلومات اليوزر الحالي
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });

    // أماكن الركن (للكل)
    Route::get('/drop-locations', [DropLocationController::class, 'index']);

    // السيارات (للكل - عرض فقط)
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    // حجوزات اليوزر
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/bookings/{booking}/start', [BookingController::class, 'startTrip']);
    Route::post('/bookings/{booking}/end', [BookingController::class, 'endTrip']);

    // تحديث موقع السيارة أثناء الرحلة (من التطبيق)
    Route::post('/cars/{car}/update-location', [CarController::class, 'updateLocation']);
});

// ==================== أدمن فقط ====================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // إدارة السيارات (CRUD كامل ما عدا index و show لأنهم للكل)
    Route::apiResource('cars', AdminCarController::class);

    // إدارة أماكن الركن (CRUD كامل ما عدا index لأنه للكل)
    Route::apiResource('drop-locations', AdminDropLocationController::class);


    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

    Route::get('/bookings', [BookingController::class, 'adminBookings']);
});
