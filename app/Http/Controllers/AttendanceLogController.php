<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class AttendanceLogController extends Controller
{

     // Show the clock page
     public function showClock()
     {
         $user = Auth::user();
         $attendanceLogs = AttendanceLog::where('employee_id', $user->id)
             ->orderBy('created_at', 'desc')
             ->take(5)
             ->get();
 
         return view('employee.clock', compact('user', 'attendanceLogs'));
     }
 
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function overview()
    {
         // Fetch employees with attendance logs ordered by clock in time
    $employees = Employee::with(['user', 'attendanceLogs' => function($q) {
        $q->orderBy('clock_in_time', 'desc');
    }])->get();

        return view('employee.attendance', compact('employees'));
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
    public function show(AttendanceLog $attendanceLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceLog $attendanceLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceLog $attendanceLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceLog $attendanceLog)
    {
        //
    }

    public function history()
    {
        $user = Auth::user();
        $employeeId = $user->employee->id ?? null;
    
        if (!$employeeId) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }
    
        $attendanceLogs = AttendanceLog::where('employee_id', $employeeId)
            ->select([
                'id',
                'clock_in_time',
                'clock_out_time',
                'location_lat',
                'location_lng',
                'geofence_status',
                'device_info',
                'created_at',
                'updated_at'
            ])
            ->orderByDesc('created_at')
            ->paginate(10);
    
        return view('employee.history', compact('user', 'attendanceLogs'));
    }
    
}
