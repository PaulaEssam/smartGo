<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // جلب كل اليوزرز مع بياناتهم كاملة
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'phone', 'license_number', 'license_photo', 'role', 'email_verified_at', 'created_at')
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json([
            'message' => 'All users retrieved successfully',
            'total_users' => $users->count(),
            'users' => $users
        ]);
    }

    // (اختياري) جلب يوزر واحد بالـ ID
    public function show(User $user)
    {
        return response()->json($user);
    }

    // (اختياري) حذف يوزر
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
