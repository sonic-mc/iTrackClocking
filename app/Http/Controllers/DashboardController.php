<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\OvertimeLog;
use App\Models\Geofence;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'admin') {
            // 📊 Core Employee Stats
            $totalEmployees = Employee::count();
            $newEmployeesThisMonth = Employee::whereMonth('created_at', now()->month)
                                             ->whereYear('created_at', now()->year)
                                             ->count();
        
            // 🕒 Attendance Metrics
            $today = today();
            $workStart = Carbon::today()->setTime(8, 0, 0);
        
            $currentlyClocked = AttendanceLog::whereDate('clock_in_time', $today)
                                             ->whereNull('clock_out_time')
                                             ->count();
        
            $attendanceRate = $totalEmployees > 0
                ? round(($currentlyClocked / $totalEmployees) * 100, 1)
                : 0;
        
            $lateArrivals = AttendanceLog::whereDate('clock_in_time', $today)
                                         ->where('clock_in_time', '>', $workStart)
                                         ->count();
        
            $presentCount = AttendanceLog::whereDate('clock_in_time', $today)->count();
        
            // ⏱️ Weekly Overtime
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd   = Carbon::now()->endOfWeek();
        
            $logs = AttendanceLog::whereBetween('clock_in_time', [$weekStart, $weekEnd])
                                 ->whereNotNull('clock_out_time')
                                 ->get();
        
            $overtimeHours = $logs->sum(function ($log) {
                $worked = Carbon::parse($log->clock_in_time)->diffInHours(Carbon::parse($log->clock_out_time));
                return $worked > 8 ? $worked - 8 : 0;
            });
        
            // 📍 Geofence Analysis
            $zones = Geofence::select('name', 'latitude as lat', 'longitude as lng', 'radius')->get();
            $todayLogs = AttendanceLog::whereDate('clock_in_time', $today)
                                      ->whereNotNull('location_lat')
                                      ->whereNotNull('location_lng')
                                      ->with('employee.user')
                                      ->get();
        
            $inZone = $outOfZone = $violations = 0;
            $employeesInZoneData = [];
        
            foreach ($todayLogs as $log) {
                $insideAnyZone = $zones->contains(function ($zone) use ($log) {
                    return $this->isInsideZone($log->location_lat, $log->location_lng, $zone->lat, $zone->lng, $zone->radius);
                });
                
        
                $employeesInZoneData[] = [
                    'name' => $log->employee->user->name ?? 'Unknown',
                    'lat' => $log->location_lat,
                    'lng' => $log->location_lng,
                    'in_zone' => $insideAnyZone
                ];
        
                $insideAnyZone ? $inZone++ : $outOfZone++;
                if (!$log->geofence_status) $violations++;
            }
        
            $geofenceViolations = AttendanceLog::whereDate('clock_in_time', $today)
                                               ->where('geofence_status', true)
                                               ->count();
        
            // 📥 Notifications & Leaves
            $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
            $newNotifications = Notification::where('user_id', $user->id)
                                            ->where('status', 'unread')
                                            ->count();
        
            $notifications = Notification::where('user_id', $user->id)
                                         ->latest()
                                         ->take(5)
                                         ->get();
        
            $todayAttendance = AttendanceLog::with('employee.user')
                                            ->whereDate('clock_in_time', $today)
                                            ->get();
        
            $recentLeaves = LeaveRequest::with('employee.user')
                                        ->latest()
                                        ->take(5)
                                        ->get();
        
            $recentActivities = AuditLog::with('user.employee')
                                        ->latest()
                                        ->take(10)
                                        ->get();
        
            // 🧬 Biometric Enrollment
            $fingerprintUsers = User::whereNotNull('biometric_data->fingerprint')->count();
            $faceIdUsers = User::whereNotNull('biometric_data->face_id')->count();
        
            $pendingEnrollment = User::where(function ($query) {
                $query->whereNull('biometric_data')
                      ->orWhere('biometric_data->fingerprint', null)
                      ->orWhere('biometric_data->face_id', null);
            })->count();
        
            // 📅 Weekly Attendance Graph
            $dates = collect();
            $attendanceCounts = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dates->push($date->format('D, M d'));
                $attendanceCounts->push(
                    AttendanceLog::whereDate('clock_in_time', $date)->count()
                );
            }
        
            return view('admin.dashboard', [
                'user' => $user,
                'dates' => $dates,
                'attendanceCounts' => $attendanceCounts,
                'totalEmployees' => $totalEmployees,
                'newEmployeesThisMonth' => $newEmployeesThisMonth,
                'currentlyClocked' => $currentlyClocked,
                'attendanceRate' => $attendanceRate,
                'lateArrivals' => $lateArrivals,
                'overtimeHours' => $overtimeHours,
                'presentCount' => $presentCount,
                'pendingLeaves' => $pendingLeaves,
                'newNotifications' => $newNotifications,
                'notifications' => $notifications,
                'todayAttendance' => $todayAttendance,
                'recentLeaves' => $recentLeaves,
                'recentActivities' => $recentActivities,
                'fingerprintUsers' => $fingerprintUsers,
                'faceIdUsers' => $faceIdUsers,
                'pendingEnrollment' => $pendingEnrollment,
                'geofenceViolations' => $geofenceViolations,
                'zones' => $zones,
                'employeesInZoneData' => $employeesInZoneData,
                'inZone' => $inZone,
                'outOfZone' => $outOfZone,
                'violations' => $violations
            ]);
        
        
           
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


    private function isInsideZone($lat1, $lng1, $lat2, $lng2, $radius)
{
    $earthRadius = 6371000; // meters

    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng / 2) * sin($dLng / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    return $distance <= $radius;
}

}

