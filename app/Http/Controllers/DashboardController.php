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
    
        // Fetch recent attendance logs for this user
        $attendanceLogs = AttendanceLog::where('employee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    
        // Fetch recent leave requests for this user
        $leaveRequests = LeaveRequest::where('employee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    
        // Fetch recent notifications for this user
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    
        if ($role === 'admin') {
            return view('admin.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
        } elseif ($role === 'manager') {
            return view('manager.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
        } else {
            return view('employee.dashboard', compact('user', 'attendanceLogs', 'leaveRequests', 'notifications'));
        }
    }
  }
