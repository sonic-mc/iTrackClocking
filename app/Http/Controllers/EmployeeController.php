<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\GeofenceViolation;
use App\Models\Shift;
use App\Models\EmployeeShift;
use App\Models\Branch;
use App\Models\User;
use App\Models\Department;
use App\Traits\AuditLogger;
use App\Models\Geofence;

use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    use AuditLogger;

    public function search(Request $request)
    {
        $query = Employee::query()->with(['user', 'branch', 'department']);

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )->orWhere('employee_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('branch_id')) $query->where('branch_id', $request->branch_id);
        if ($request->filled('department_id')) $query->where('department_id', $request->department_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $filteredEmployees = $query->get();

        $this->logAudit('search_employees', 'Searched employees with filters: ' . json_encode($request->all()));

        // Prepare other view variables
        $employees = Employee::with(['user', 'branch', 'department'])->orderBy('created_at', 'desc')->get();
        $assignedEmployees = EmployeeShift::with(['employee.user', 'shift'])->orderBy('date')->get();
        $shifts = Shift::orderBy('start_time')->get();
        $today = now()->toDateString();
        $branches = Branch::all();
        $departments = Department::all();

        return view('manager.employees', compact(
            'employees', 'assignedEmployees', 'shifts', 'today', 
            'branches', 'departments', 'filteredEmployees'
        ));
    }

    public function index()
    {
        $employees = Employee::with(['user', 'branch', 'department'])->orderBy('created_at', 'desc')->get();
        $assignedEmployees = EmployeeShift::with(['employee.user', 'shift'])->orderBy('date')->get();
        $shifts = Shift::orderBy('start_time')->get();
        $today = now()->toDateString();
        $branches = Branch::all();
        $departments = Department::all();
        $filteredEmployees = $employees;

        $this->logAudit('view_employees', 'Viewed employee list');

        return view('manager.employees', compact(
            'employees', 'shifts', 'today', 'assignedEmployees', 'branches', 'departments', 'filteredEmployees'
        ));
    }

    public function create()
    {
        // Get only users who are registered but not yet linked to an employee
        $users = User::doesntHave('employee')->get();
        $branches = Branch::all();
        $departments = Department::all();

        return view('admin.employees.create', compact('users', 'branches', 'departments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_number' => 'required|unique:employees,employee_number',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
    
        $employee = Employee::create([
            'user_id' => $request->user_id,
            'employee_number' => $request->employee_number,
            'branch_id' => $request->branch_id,
            'department_id' => $request->department_id,
            'position' => $request->position,
            'status' => $request->status,
        ]);
    
        $this->logAudit('create_employee', "Created employee #{$employee->id} ({$employee->employee_number})");
    
        return redirect()
            ->route('admin.employees.create')
            ->with('success', 'Employee added successfully!');
    }
    

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_number' => 'required|unique:employees,employee_number,' . $employee->id,
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($validated);

        $this->logAudit('update_employee', "Updated employee #{$employee->id}");

        return redirect()->route('employees.edit', $employee->id)->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employeeId = $employee->id;
        $employee->delete();

        $this->logAudit('delete_employee', "Deleted employee #{$employeeId}");

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

   public function clock(Request $request)
{
    $user = Auth::user();
    $employee = $user->employee;

    if (!$employee) {
        return response()->json(['status' => 'error', 'message' => 'Employee record not found.'], 400);
    }

    $request->validate([
        'action' => 'required|in:in,out',
        'location_lat' => 'nullable|numeric|between:-90,90',
        'location_lng' => 'nullable|numeric|between:-180,180',
        'device_info' => 'nullable|string|max:255',
    ]);

    $action = $request->input('action');
    $locationLat = $request->input('location_lat');
    $locationLng = $request->input('location_lng');
    $deviceInfo = $request->input('device_info');

    $geofenceStatus = ($locationLat && $locationLng)
        ? $this->isWithinGeofence($locationLat, $locationLng, $employee->id)
        : true;

    $attendance = AttendanceLog::where('employee_id', $employee->id)
        ->whereDate('created_at', now()->toDateString())
        ->first();

    if ($action === 'in') {
        if ($attendance && $attendance->clock_in_time) {
            return response()->json(['status' => 'error', 'message' => 'You have already clocked in today.']);
        }

        if (!$attendance) {
            $attendance = new AttendanceLog();
            $attendance->employee_id = $employee->id;
        }

        $attendance->clock_in_time = now();
        $attendance->location_lat = $locationLat;
        $attendance->location_lng = $locationLng;
        $attendance->device_info = $deviceInfo;
        $attendance->geofence_status = $geofenceStatus;
        $attendance->save();

        $this->logAudit('clock_in', "Employee #{$employee->id} clocked in");

        return response()->json(['status' => 'success', 'message' => '✅ You have clocked in successfully.']);
    }

    if ($action === 'out') {
        if (!$attendance || !$attendance->clock_in_time) {
            return response()->json(['status' => 'error', 'message' => 'You must clock in before clocking out.']);
        }

        if ($attendance->clock_out_time) {
            return response()->json(['status' => 'error', 'message' => 'You have already clocked out today.']);
        }

        $attendance->clock_out_time = now();
        $attendance->location_lat = $locationLat;
        $attendance->location_lng = $locationLng;
        $attendance->device_info = $deviceInfo;
        $attendance->geofence_status = $geofenceStatus;
        $attendance->save();

        $this->logAudit('clock_out', "Employee #{$employee->id} clocked out");

        if (!$geofenceStatus) {
            GeofenceViolation::create([
                'employee_id' => $employee->id,
                'attendance_log_id' => $attendance->id,
                'latitude' => $locationLat,
                'longitude' => $locationLng,
                'device_info' => $deviceInfo,
                'violation_time' => now(),
                'violation_type' => 'clock_out',
            ]);

            $this->logAudit('geofence_violation', "Employee #{$employee->id} clocked out outside geofence");

            return response()->json(['status' => 'warning', 'message' => '⚠️ Clock-out recorded, but you were outside the geofence.']);
        }

        return response()->json(['status' => 'success', 'message' => '✅ You have clocked out successfully.']);
    }

    return response()->json(['status' => 'error', 'message' => 'Invalid action.'], 400);
}


        /**
     * Check if a given coordinate is within the geofence of a branch.
     *
     * @param  int    $branchId
     * @param  float  $latitude
     * @param  float  $longitude
     * @return bool
     */
    protected function isWithinGeofence($branchId, $latitude, $longitude)
    {
        $geofence = Geofence::where('branch_id', $branchId)->first();

        if (!$geofence) {
            return false; // No geofence set for this branch
        }

        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($geofence->latitude);
        $lonFrom = deg2rad($geofence->longitude);
        $latTo   = deg2rad($latitude);
        $lonTo   = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) *
            pow(sin($lonDelta / 2), 2)
        ));

        $distance = $earthRadius * $angle;

        return $distance <= $geofence->radius;
    }

}
