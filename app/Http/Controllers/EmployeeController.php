<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Geofence;
use App\Models\GeofenceViolation;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'branch', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        $shifts = Shift::orderBy('start_time')->get();
        $today = now()->toDateString();
    
        return view('manager.employees', compact('employees', 'shifts', 'today'));
    }
    
    
   
    // Show form to create an employee from an existing user
    public function create()
    {
        $auth = Auth::user();

        // Only allow admin or manager to access this page
        if (! in_array($auth->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized.');
        }

        // Only users who do not already have an employee record AND are regular users (employee role)
        $users = User::doesntHave('employee')
            ->orderBy('name')
            ->get();

        // If manager, scope branches to manager's branch (if available)
        if ($auth->role === 'manager') {
           
            $managerEmployee = $auth->employee;
            if ($managerEmployee && $managerEmployee->branch_id) {
                $branches = Branch::where('id', $managerEmployee->branch_id)->get();
            } else {
                
                $branches = collect();
            }
        } else {
        
            $branches = Branch::orderBy('name')->get();
        }

        $departments = Department::orderBy('name')->get();

        return view('admin.employees.create', compact('users', 'branches', 'departments'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'employee_number' => 'required|unique:employees,employee_number',
        'branch_name' => 'required|string|max:255',
        'department_name' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ]);

    // Create or find branch
    
    $branch = Branch::firstOrCreate(
        ['name' => $request->branch_name],
        ['address' => 'Not Provided'] // fallback address
    );

    // Create or find department
    $department = Department::firstOrCreate(
        ['name' => $request->department_name, 'branch_id' => $branch->id]
    );

    Employee::create([
        'user_id' => $request->user_id,
        'employee_number' => $request->employee_number,
        'branch_id' => $branch->id,
        'department_id' => $department->id,
        'position' => $request->position,
        'status' => $request->status,
    ]);

    return redirect()->route('admin.employees.create')->with('success', 'Employee added successfully!');
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
    
    public function clock(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
    
        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee record not found.'], 400);
        }
    
        $request->validate([
            'action'        => 'required|in:in,out',
            'location_lat'  => 'nullable|numeric|between:-90,90',
            'location_lng'  => 'nullable|numeric|between:-180,180',
            'device_info'   => 'nullable|string|max:255',
        ]);
    
        $action      = $request->input('action');
        $locationLat = $request->input('location_lat');
        $locationLng = $request->input('location_lng');
        $deviceInfo  = $request->input('device_info');
    
        // Check geofence
        $geofenceStatus = ($locationLat && $locationLng)
            ? $this->isWithinGeofence($locationLat, $locationLng, $employee->id)
            : true;
    
        // Get today's attendance record
        $attendance = AttendanceLog::where('employee_id', $employee->id)
            ->whereDate('created_at', now()->toDateString())
            ->first();
    
        /** ---------------- CLOCK IN ---------------- **/
        if ($action === 'in') {
            if ($attendance && $attendance->clock_in_time) {
                if (!$attendance->clock_out_time) {
                    return response()->json([
                        'status'  => 'confirm',
                        'message' => 'You are already clocked in. Do you want to clock out instead?'
                    ]);
                }
    
                return response()->json(['status' => 'info', 'message' => 'You already clocked in today.']);
            }
    
            if (!$attendance) {
                $attendance = new AttendanceLog();
                $attendance->employee_id     = $employee->id;
                $attendance->location_lat    = $locationLat;
                $attendance->location_lng    = $locationLng;
                $attendance->device_info     = $deviceInfo;
                $attendance->geofence_status = $geofenceStatus;
            }
    
            $attendance->clock_in_time = now();
            $attendance->save();
    
            return response()->json(['status' => 'success', 'message' => '✅ You have clocked in successfully.']);
        }
    
        /** ---------------- CLOCK OUT ---------------- **/
        if ($action === 'out') {
            if (!$attendance || !$attendance->clock_in_time) {
                return response()->json(['status' => 'error', 'message' => 'You must clock in before clocking out.']);
            }
    
            if ($attendance->clock_out_time) {
                return response()->json(['status' => 'info', 'message' => 'You already clocked out today.']);
            }
    
            $attendance->clock_out_time    = now();
            $attendance->location_lat      = $locationLat;
            $attendance->location_lng      = $locationLng;
            $attendance->device_info       = $deviceInfo;
            $attendance->geofence_status   = $geofenceStatus;
            $attendance->save();
    
            // Log geofence violation if outside bounds
            if (!$geofenceStatus) {
                GeofenceViolation::create([
                    'employee_id'       => $employee->id,
                    'attendance_log_id' => $attendance->id,
                    'latitude'          => $locationLat,
                    'longitude'         => $locationLng,
                    'device_info'       => $deviceInfo,
                    'violation_time'    => now(),
                    'violation_type'    => 'clock_out',
                ]);
    
                return response()->json([
                    'status'  => 'warning',
                    'message' => '⚠️ Clock-out recorded, but you were outside the geofence.'
                ]);
            }
    
            return response()->json(['status' => 'success', 'message' => '✅ You have clocked out successfully.']);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Invalid action.'], 400);
    }
    
    
    protected function isWithinGeofence($lat, $lng, $employeeId)
    {
        $employee = Employee::with('branch.geofence')->find($employeeId);
    
        if (!$employee || !$employee->branch || !$employee->branch->geofence) {
            \Log::warning("No geofence found for employee ID {$employeeId}");
            return true; // fallback: allow if no geofence defined
        }
    
        $geofence = $employee->branch->geofence;
    
        // Example: geofence has center (lat,lng) and radius in meters
        $distance = $this->haversineDistance(
            $lat,
            $lng,
            $geofence->latitude,
            $geofence->longitude
        );
    
        return $distance <= $geofence->radius; // true if within radius
    }
    

    protected function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meters
    
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
    
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c; // distance in meters
    }
    

    
}
