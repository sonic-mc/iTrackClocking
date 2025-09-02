<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\OvertimeLog;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'admin') {
            // Admin dashboard data
            $attendanceLogs = AttendanceLog::latest()->take(5)->get();
            $leaveRequests = LeaveRequest::latest()->take(5)->get();
            $notifications = Notification::where('user_id', $user->id)->latest()->take(5)->get();

            return view('admin.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
        } elseif ($role === 'manager') {
            // Manager dashboard data
            $employeeCount   = Employee::count();
            $presentCount    = AttendanceLog::whereDate('clock_in_time', today())->count();
            $pendingLeaves   = LeaveRequest::where('status', 'pending')->count();
            $newNotifications = Notification::where('user_id', $user->id)
                                ->where('status', 'unread')
                                ->count();

            $todayAttendance = AttendanceLog::with('employee.user')
                ->whereDate('clock_in_time', today())
                ->get();

            $recentLeaves = LeaveRequest::with('employee.user')
                ->latest()
                ->take(5)
                ->get();

            $notifications = Notification::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
                

            return view('manager.dashboard', compact(
                'user',
                'employeeCount',
                'presentCount',
                'pendingLeaves',
                'newNotifications',
                'todayAttendance',
                'recentLeaves',
                'notifications'
            ));
        } else {
            // Employee dashboard data
            $employee = Employee::where('user_id', $user->id)->firstOrFail();

            $attendanceLogs = AttendanceLog::where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            
            // assuming there's a relation employee->shift_id
            $shift = $employee->shift_id ? Shift::find($employee->shift_id) : null;

            // Get overtime logs
            $overtimeLogs = OvertimeLog::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->take(5)
                ->get();

            return view('employee.dashboard', compact(
                'user',
                'employee',
                'attendanceLogs',
                'leaveRequests',
                'notifications',
                'shift',
                'overtimeLogs'
            ));
        }
    }
}
