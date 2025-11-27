<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarDropLocation;
use Illuminate\Http\Request;

class AdminDropLocationController extends Controller
{
    /**
     * Display a listing of all drop locations (for admin dashboard)
     */
    public function index()
    {
        return response()->json(CarDropLocation::all());
    }

    /**
     * Store a newly created drop location
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'lat'     => 'required|numeric|between:-90,90',
            'lng'     => 'required|numeric|between:-180,180',
        ]);

        $location = CarDropLocation::create($validated);

        return response()->json([
            'message' => 'Drop location added successfully',
            'location' => $location
        ], 201);
    }

    /**
     * Display the specified drop location
     */
    public function show(CarDropLocation $dropLocation)
    {
        return response()->json($dropLocation);
    }

    /**
     * Update the specified drop location
     */
    public function update(Request $request, CarDropLocation $dropLocation)
    {
        $validated = $request->validate([
            'name'    => 'string|max:255',
            'address' => 'string|max:255',
            'lat'     => 'numeric|between:-90,90',
            'lng'     => 'numeric|between:-180,180',
        ]);

        // ده اللي كان ناقص يا وحش
        $dropLocation->update($validated);

        return response()->json([
            'message'   => 'Drop location updated successfully',
            'location'  => $dropLocation->fresh()
        ]);
    }
    /**
     * Remove the specified drop location
     */
    public function destroy(CarDropLocation $dropLocation)
    {
        $dropLocation->delete();

        return response()->json([
            'message' => 'Drop location deleted successfully'
        ]);
    }
}
