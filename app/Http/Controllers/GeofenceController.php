<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geofence;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\AuditLogger;


class GeofenceController extends Controller
{
    use AuditLogger;

    public function indexx()
    {
        // Load geofences with branch
        $geofences = Geofence::with('branch')->get();

        // Attach active employees per geofence via branch_id
        foreach ($geofences as $geofence) {
            $geofence->activeEmployees = Employee::with('user')
                ->where('branch_id', $geofence->branch_id)
                ->where('status', 'active')
                ->get();
        }
        return view('geofence.index', compact('geofences'));
    }


    public function index()
    {
        $geofences = Geofence::latest()->get();
        $branches = Branch::orderBy('name')->get();
        
        // Show the geofence management page
        return view('admin.geofence.manage', compact('geofences', 'branches'));
    }

            public function create()
        {
            $branches = Branch::orderBy('name')->get();
            return view('admin.geofence.manage', compact('branches'));
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
    $request->validate([
        'branch_id' => 'required|exists:branches,id',
        'name' => 'required|string|max:255',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'radius' => 'required|integer|min:1',
    ]);

    $geofence = Geofence::findOrFail($id);

    $geofence->update([
        'branch_id' => $request->branch_id,
        'name' => $request->name,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'radius' => $request->radius,
    ]);

    return redirect()->route('geofence.manage')->with('success', 'Geofence updated successfully.');
}


    public function destroy($id)
    {
        $geofence = Geofence::findOrFail($id);
        $geofence->delete();

        return redirect()->route('geofence.manage')->with('success', 'Geofence deleted successfully.');
    }

    public function edit(Geofence $geofence)
        {
            $branches = Branch::orderBy('name')->get();
            return view('admin.geofence.edit', compact('geofence', 'branches'));
        }

}

