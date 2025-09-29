<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geofence;
use App\Models\Branch;
use App\Models\Employee;
use App\Traits\AuditLogger;

class GeofenceController extends Controller
{
    use AuditLogger;

    /**
     * Display geofences with branches and active employees.
     */
    public function indexx()
    {
        $geofences = Geofence::with('branch')->get();

        foreach ($geofences as $geofence) {
            $geofence->activeEmployees = Employee::with('user')
                ->where('branch_id', $geofence->branch_id)
                ->where('status', 'active')
                ->get();
        }

        $this->logAudit('view_geofences', 'Viewed geofences with active employees');
        return view('geofence.index', compact('geofences'));
    }

    /**
     * Show geofence management page.
     */
    public function index()
    {
        $geofences = Geofence::latest()->get();
        $branches = Branch::orderBy('name')->get();

        $this->logAudit('view_geofence_management', 'Viewed geofence management page');
        return view('admin.geofence.manage', compact('geofences', 'branches'));
    }

    /**
     * Show form to create a new geofence.
     */
    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.geofence.manage', compact('branches'));
    }

    /**
     * Store a new geofence.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:1',
        ]);

        $geofence = Geofence::create($validated);
        $this->logAudit('create_geofence', "Created geofence #{$geofence->id} ({$geofence->name})");

        return redirect()->back()->with('success', 'Geofence created successfully.');
    }

    /**
     * Show form to edit a geofence.
     */
    public function edit(Geofence $geofence)
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.geofence.edit', compact('geofence', 'branches'));
    }

    /**
     * Update an existing geofence.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:1',
        ]);

        $geofence = Geofence::findOrFail($id);
        $geofence->update($request->only('branch_id', 'name', 'latitude', 'longitude', 'radius'));

        $this->logAudit('update_geofence', "Updated geofence #{$geofence->id} ({$geofence->name})");

        return redirect()->route('geofence.manage')->with('success', 'Geofence updated successfully.');
    }

    /**
     * Delete a geofence.
     */
    public function destroy($id)
    {
        $geofence = Geofence::findOrFail($id);
        $name = $geofence->name;
        $geofence->delete();

        $this->logAudit('delete_geofence', "Deleted geofence #{$id} ({$name})");

        return redirect()->route('geofence.manage')->with('success', 'Geofence deleted successfully.');
    }
}
