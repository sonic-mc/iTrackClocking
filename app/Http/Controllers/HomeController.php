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
use App\Models\Geofence;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditLogger;

class HomeController extends Controller
{
    use AuditLogger;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index()
    {

    $user = Auth::user();

    // Check if the user is linked to an employee record
    if ($user && $user->employee) {
        return redirect()->route('dashboard');
    }
    
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    }
   
    