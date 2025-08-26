<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Fetch all logs for this user (paginate for large sets)
        $attendanceLogs = AttendanceLog::where('employee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.history', compact('user', 'attendanceLogs'));
    }
}
