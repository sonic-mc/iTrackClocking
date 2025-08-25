<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;

class DashboardController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $role = Auth::user()->role;
  // Fetch recent attendance logs for this user
  $attendanceLogs = AttendanceLog::where('employee_id', $user->id)
  ->orderBy('created_at', 'desc')
  ->take(5) // limit to last 5
  ->get();

if ($role === 'admin') {
return view('admin.dashboard', compact('user', 'attendanceLogs'));
} elseif ($role === 'manager') {
return view('manager.dashboard', compact('user', 'attendanceLogs'));
} else {
return view('employee.dashboard', compact('user', 'attendanceLogs'));
}
}
}
