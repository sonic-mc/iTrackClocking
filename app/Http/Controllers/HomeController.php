<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\OvertimeLog;
use Carbon\Carbon;
use App\Models\AuditLog;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'admin') {
            // Admin dashboard data
            $totalEmployees = Employee::count();
             // Example: employees added this month
            $newEmployeesThisMonth = Employee::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

             // Currently clocked in (clocked in today but no clock out yet)
            $currentlyClocked = AttendanceLog::whereDate('clock_in_time', today())
            ->whereNull('clock_out_time')
            ->count();

             // Attendance rate (just an example: % of employees clocked in today)
            $attendanceRate = $totalEmployees > 0 
            ? round(($currentlyClocked / $totalEmployees) * 100, 1)
            : 0;

             // Define work start time (09:00 for example)
            $workStart = Carbon::today()->setTime(8, 0, 0);

            // Count late arrivals today
            $lateArrivals = AttendanceLog::whereDate('clock_in_time', today())
                            ->where('clock_in_time', '>', $workStart)
                            ->count();

              // Get start & end of current week
            $weekStart = Carbon::now()->startOfWeek(); // Monday
            $weekEnd   = Carbon::now()->endOfWeek();   // Sunday

            // Total employees
            $totalEmployees = Employee::count();

            // Currently clocked in
            $currentlyClocked = AttendanceLog::whereDate('clock_in_time', today())
                                ->whereNull('clock_out_time')
                                ->count();

            // Attendance rate
            $attendanceRate = $totalEmployees > 0 
                ? round(($currentlyClocked / $totalEmployees) * 100, 1)
                : 0;

            // Late arrivals
            $workStart = Carbon::today()->setTime(8, 0, 0);
            $lateArrivals = AttendanceLog::whereDate('clock_in_time', today())
                            ->where('clock_in_time', '>', $workStart)
                            ->count();

            // Overtime calculation for this week
            $overtimeHours = 0;

            $logs = AttendanceLog::whereBetween('clock_in_time', [$weekStart, $weekEnd])
                    ->whereNotNull('clock_out_time')
                    ->get();

            foreach ($logs as $log) {
                $workedHours = Carbon::parse($log->clock_in_time)
                                ->diffInHours(Carbon::parse($log->clock_out_time));

                if ($workedHours > 8) {
                    $overtimeHours += ($workedHours - 8);
                }
            }
               

            $presentCount    = AttendanceLog::whereDate('clock_in_time', today())->count();
            $pendingLeaves   = LeaveRequest::where('status', 'pending')->count();
            // $geofenceViolations = AttendanceLog::whereDate('clock_in_time', today())
            //             ->where('geofence_status', true)
            //             ->count();

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
            
             
           // Count biometric enrollments
            $fingerprintUsers = \App\Models\User::whereNotNull('biometric_data->fingerprint')->count();
            $faceIdUsers = \App\Models\User::whereNotNull('biometric_data->face_id')->count();

            $pendingEnrollment = \App\Models\User::where(function($query) {
                $query->whereNull('biometric_data')
                      ->orWhere('biometric_data->fingerprint', null)
                      ->orWhere('biometric_data->face_id', null);
            })->count();
            

            // Get last 7 days
            $dates = collect();
            $attendanceCounts = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dates->push($date->format('D, M d')); // e.g., Mon, Sep 02
                $attendanceCounts->push(
                    AttendanceLog::whereDate('clock_in_time', $date)->count()
                );
            }

            $recentActivities = AuditLog::with('user.employee')
            ->latest()
            ->take(10)
            ->get();

            return view('admin.dashboard',['dates' => $dates,
            'attendanceCounts' => $attendanceCounts], compact(
                'user',
                'totalEmployees',
                'newEmployeesThisMonth',
                'currentlyClocked',
                'attendanceRate',
                'lateArrivals',
                'overtimeHours',
                'presentCount',
                'pendingLeaves',
                // 'geofenceStatus',
                'newNotifications',
                'todayAttendance',
                'recentLeaves',
                'notifications',
                'fingerprintUsers',
                'faceIdUsers',
                'pendingEnrollment',
                'recentActivities'
                
            ));

           
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

