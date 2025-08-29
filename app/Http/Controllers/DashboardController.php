<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Common: user-specific data
        $attendanceLogs = AttendanceLog::where('employee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $leaveRequests = LeaveRequest::where('employee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($role === 'admin') {
            return view('admin.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
        } elseif ($role === 'manager') {
            // Manager-specific aggregated data
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

            return view('manager.dashboard', compact(
                'user',
                'attendanceLogs',
                'leaveRequests',
                'notifications',
                'employeeCount',
                'presentCount',
                'pendingLeaves',
                'newNotifications',
                'todayAttendance',
                'recentLeaves'
            ));
        } else {
            return view('employee.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
        }
    }
}
