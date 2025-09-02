<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geofence;

class GeofenceController extends Controller
{
    public function index()
    {
        $geofences = Geofence::latest()->get();
        
        // Show the geofence management page
        return view('admin.geofence.manage', compact('geofences'));
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:1', // in meters
        ]);

        // Create the geofence
        $geofence = Geofence::create([
            'name'      => $validated['name'],
            'latitude'  => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius'    => $validated['radius'],
        ]);

        return redirect()->back()->with('success', 'Geofence created successfully.');
    }

    public function update(Request $request, $id)
    {
        // Update geofence data
    }

    public function destroy($id)
    {
        // Delete geofence entry
    }
}

