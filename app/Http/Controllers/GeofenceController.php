<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    public function index()
    {
        // Show the geofence management page
        return view('admin.geofence.manage');
    }

    public function store(Request $request)
    {
        // Save geofence data
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

