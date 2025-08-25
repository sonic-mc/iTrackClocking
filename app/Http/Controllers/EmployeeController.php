<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{

    public function dashboard()
    {
        $user = Auth::user();

        $attendanceLogs = AttendanceLog::where('employee_id', $user->id)
                                        ->latest()
                                        ->take(5)
                                        ->get();

        $leaveRequests = LeaveRequest::where('employee_id', $user->id)
                                     ->latest()
                                     ->take(5)
                                     ->get();

        $notifications = Notification::where('user_id', $user->id)
                                     ->latest()
                                     ->take(5)
                                     ->get();

        return view('employee.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
    }

    public function clock(Request $request)
    {
        $user = Auth::user();
        $log = AttendanceLog::firstOrCreate(
            ['employee_id' => $user->id, 'date' => now()->format('Y-m-d')],
            ['clock_in' => now()]
        );

        // If clock_in exists, update clock_out
        if ($log->clock_in && !$log->clock_out) {
            $log->clock_out = now();
            $log->save();
        }

        return redirect()->route('employee.dashboard')->with('success', 'Attendance logged successfully!');
    }
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
