<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'branch', 'department'])->orderBy('created_at', 'desc')->get();
        return view('manager.employees', compact('employees'));
    }
    
   
    public function create()
    {
        $users = User::doesntHave('employee')->get(); // Only users not yet employees
        $branches = Branch::all();
        $departments = Department::all();

        return view('admin.employees.create', compact('users', 'branches', 'departments'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'employee_number' => 'required|unique:employees,employee_number',
        'branch_name' => 'required|string|max:255',
        'department_name' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ]);

    // Create or find branch
    
    $branch = Branch::firstOrCreate(
        ['name' => $request->branch_name],
        ['address' => 'Not Provided'] // fallback address
    );

    // Create or find department
    $department = Department::firstOrCreate(
        ['name' => $request->department_name, 'branch_id' => $branch->id]
    );

    Employee::create([
        'user_id' => $request->user_id,
        'employee_number' => $request->employee_number,
        'branch_id' => $branch->id,
        'department_id' => $department->id,
        'position' => $request->position,
        'status' => $request->status,
    ]);

    return redirect()->route('employees.create')->with('success', 'Employee added successfully!');
}
    

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }

    public function clock(Request $request)
    {
        $user = Auth::user();
    
        // Get the employee record linked to this user
        $employee = $user->employee; // assuming you have User->employee() relation
    
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }
    
        // Find today's attendance log if it exists
        $attendance = AttendanceLog::where('employee_id', $employee->id)
            ->whereDate('clock_in_time', today())
            ->first();
    
        if (!$attendance) {
            // Clock In
            AttendanceLog::create([
                'employee_id'   => $employee->id,
                'clock_in_time' => now(),
            ]);
    
            return redirect()->back()->with('success', 'You have clocked in successfully.');
        } else {
            // If already clocked in but no clock out, clock out
            if (is_null($attendance->clock_out_time)) {
                $attendance->update([
                    'clock_out_time' => now(),
                ]);
    
                return redirect()->back()->with('success', 'You have clocked out successfully.');
            } else {
                return redirect()->back()->with('success', 'You already clocked in and out today.');
            }
        }
    }
}
