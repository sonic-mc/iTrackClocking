<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Models\EmployeeShift;
use App\Traits\AuditLogger;

class ShiftController extends Controller
{
    use AuditLogger;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shift::orderBy('start_time')->get();
        $this->logAudit('view_shifts', 'Viewed list of all shifts');
        return view('admin.shifts.manage', compact('shifts'));
    }

    /**
     * Assign a shift to an employee.
     */
    public function assignShift(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id'    => 'required|exists:shifts,id',
            'date'        => 'required|date',
        ]);

        $existing = EmployeeShift::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            $existing->update(['shift_id' => $validated['shift_id']]);
            $this->logAudit('update_shift_assignment', "Updated shift assignment for employee #{$validated['employee_id']} on {$validated['date']}");
        } else {
            EmployeeShift::create($validated);
            $this->logAudit('assign_shift', "Assigned shift #{$validated['shift_id']} to employee #{$validated['employee_id']} on {$validated['date']}");
        }

        return redirect()->back()->with('success', 'Shift assigned successfully.');
    }

    /**
     * Store a newly created shift.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
        ]);

        $shift = Shift::create($request->all());
        $this->logAudit('create_shift', "Created shift #{$shift->id} ({$shift->name})");

        return redirect()->route('shifts.manage')->with('success', 'Shift created successfully.');
    }

    /**
     * Update a shift.
     */
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
        ]);

        $shift->update($request->all());
        $this->logAudit('update_shift', "Updated shift #{$shift->id} ({$shift->name})");

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Delete a shift.
     */
    public function destroy(Shift $shift)
    {
        $shiftName = $shift->name;
        $shiftId = $shift->id;
        $shift->delete();

        $this->logAudit('delete_shift', "Deleted shift #{$shiftId} ({$shiftName})");

        return redirect()->route('shifts.manage')->with('success', 'Shift deleted successfully.');
    }
}
