<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeShiftController extends Controller
{
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

        return view('employee.shifts', compact('upcomingShifts'));
    }

    /**
     * Show the form for assigning a shift to an employee.
     */
    public function create()
    {
        $employees = Employee::all();
        $shifts = Shift::orderBy('start_time')->get();

        return view('admin.employee_shifts.create', compact('employees', 'shifts'));
    }

    /**
     * Store a newly assigned shift.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
        ]);

        EmployeeShift::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
            ],
            [
                'shift_id' => $request->shift_id,
            ]
        );

        return redirect()->route('employee-shifts.index')->with('success', 'Shift assigned successfully.');
    }

    /**
     * Display a specific shift assignment.
     */
    public function show(EmployeeShift $employeeShift)
    {
        return view('admin.employee_shifts.show', compact('employeeShift'));
    }

    /**
     * Show the form for editing a shift assignment.
     */
    public function edit(EmployeeShift $employeeShift)
    {
        $employees = Employee::all();
        $shifts = Shift::orderBy('start_time')->get();

        return view('admin.employee_shifts.edit', compact('employeeShift', 'employees', 'shifts'));
    }

    /**
     * Update a shift assignment.
     */
    public function update(Request $request, EmployeeShift $employeeShift)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
        ]);

        $employeeShift->update([
            'employee_id' => $request->employee_id,
            'shift_id' => $request->shift_id,
            'date' => $request->date,
        ]);

        return redirect()->route('employee-shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove a shift assignment.
     */
    public function destroy(EmployeeShift $employeeShift)
    {
        $employeeShift->delete();

        return redirect()->route('employee-shifts.index')->with('success', 'Shift removed successfully.');
    }
}
