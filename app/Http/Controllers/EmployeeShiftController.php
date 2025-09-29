<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\AuditLogger;

class EmployeeShiftController extends Controller
{
    use AuditLogger;

    /**
     * Display a listing of upcoming shifts for the authenticated employee.
     */
    public function index()
    {
        $user = Auth::user();
        $employeeId = $user->employee->id;

        $upcomingShifts = EmployeeShift::with('shift')
            ->where('employee_id', $employeeId)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get();

        $this->logAudit('view_shifts', "Viewed upcoming shifts for employee #{$employeeId}");
        return view('employee.shifts', compact('upcomingShifts'));
    }

    /**
     * Show the form for assigning a shift to an employee.
     */
    public function create()
    {
        $employees = Employee::all();
        $shifts = Shift::orderBy('start_time')->get();

        $this->logAudit('view_assign_shift_form', 'Accessed shift assignment form');
        return view('admin.employee_shifts.create', compact('employees', 'shifts'));
    }

    /**
     * Store a newly assigned shift.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id'    => 'required|exists:shifts,id',
            'date'        => 'required|date',
        ]);

        $employeeShift = EmployeeShift::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
            ],
            [
                'shift_id' => $request->shift_id,
            ]
        );

        $this->logAudit(
            'assign_shift',
            "Assigned shift #{$employeeShift->shift_id} to employee #{$employeeShift->employee_id} on {$employeeShift->date}"
        );

        return redirect()->route('employee-shifts.index')->with('success', 'Shift assigned successfully.');
    }

    /**
     * Display a specific shift assignment.
     */
    public function show(EmployeeShift $employeeShift)
    {
        $this->logAudit('view_shift', "Viewed shift #{$employeeShift->id} for employee #{$employeeShift->employee_id}");
        return view('admin.employee_shifts.show', compact('employeeShift'));
    }

    /**
     * Show the form for editing a shift assignment.
     */
    public function edit(EmployeeShift $employeeShift)
    {
        $employees = Employee::all();
        $shifts = Shift::orderBy('start_time')->get();

        $this->logAudit('view_edit_shift_form', "Accessed edit form for shift #{$employeeShift->id}");
        return view('admin.employee_shifts.edit', compact('employeeShift', 'employees', 'shifts'));
    }

    /**
     * Update a shift assignment.
     */
    public function update(Request $request, EmployeeShift $employeeShift)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id'    => 'required|exists:shifts,id',
            'date'        => 'required|date',
        ]);

        $employeeShift->update([
            'employee_id' => $request->employee_id,
            'shift_id'    => $request->shift_id,
            'date'        => $request->date,
        ]);

        $this->logAudit(
            'update_shift',
            "Updated shift #{$employeeShift->id} for employee #{$employeeShift->employee_id}"
        );

        return redirect()->route('employee-shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove a shift assignment.
     */
    public function destroy(EmployeeShift $employeeShift)
    {
        $employeeShift->delete();

        $this->logAudit(
            'delete_shift',
            "Deleted shift #{$employeeShift->id} for employee #{$employeeShift->employee_id}"
        );

        return redirect()->route('employee-shifts.index')->with('success', 'Shift removed successfully.');
    }
}
