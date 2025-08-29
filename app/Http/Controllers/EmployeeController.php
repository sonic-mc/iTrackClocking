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
        //
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
}
