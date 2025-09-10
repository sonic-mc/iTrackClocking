<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\EmployeeShiftController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\OvertimeLogController;
use App\Http\Controllers\LeaveRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth', 'role:employee,manager,admin'])->group(function () {
    Route::post('/employee/clock', [EmployeeController::class, 'clock'])->name('employee.clock');
    Route::get('/biometric/setup', [App\Http\Controllers\BiometricController::class, 'setup'])->name('biometric.setup');
    Route::post('/biometric/setup', [App\Http\Controllers\BiometricController::class, 'store'])->name('biometric.store');
    Route::post('/biometric/register', [BiometricController::class, 'register'])->name('biometric.register');
    Route::get('/biometric/register/options', [BiometricController::class, 'registerOptions'])
    ->name('biometric.register.options');

Route::post('/biometric/register/complete', [BiometricController::class, 'registerComplete'])
    ->name('biometric.register.complete');

   // WebAuthn authentication (clock in)
    Route::post('/biometric/authenticate', [BiometricController::class, 'authenticate'])->name('biometric.authenticate');

});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/attendance/history', function () {
        return view('attendance.history');
    })->name('attendance.history');

    Route::get('/reports', function () {
        return view('reports.index');
    })->middleware('can:isManager')->name('reports');
});


  // Attendance routes
  Route::prefix('attendance')->name('attendance.')->group(function () {
  Route::get('/history', [AttendanceLogController::class, 'history'])->name('history');
});



Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/biometric', [AdminController::class, 'biometric'])->name('admin.biometric');
    Route::get('/audit', [AdminController::class, 'audit'])->name('admin.audit');
    Route::resource('employees', EmployeeController::class);
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/geofence/manage', [GeofenceController::class, 'index'])->name('geofence.manage');
    Route::post('/geofence/store', [GeofenceController::class, 'store'])->name('geofence.store');
    Route::put('/geofence/{id}', [GeofenceController::class, 'update'])->name('geofence.update');
    Route::delete('/geofence/{id}', [GeofenceController::class, 'destroy'])->name('geofence.destroy');
});



Route::middleware(['auth', 'role:admin, manager'])->group(function () {
    Route::get('/shifts/manage', [ShiftController::class, 'index'])->name('shifts.manage');
    Route::post('/shifts/employees/assign-shift', [ShiftController::class, 'assignShift'])->name('employees.assignShift');
    Route::post('/shifts/store', [ShiftController::class, 'store'])->name('shifts.store');
    Route::put('/shifts/{id}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [EmployeeShiftController::class, 'index'])->name('index');
    
    });

    Route::prefix('breaks')->name('breaks.')->group(function () {
        Route::get('/', [BreakController::class, 'index'])->name('index');
    });

    Route::prefix('overtime')->name('overtime.')->group(function () {
        Route::get('/', [OvertimeLogController::class, 'index'])->name('index');
    });

});


Route::middleware(['auth'])->group(function () {
    Route::get('/leave/request', [LeaveRequestController::class, 'requestForm'])
        ->name('leave.request');

    // Submit leave request
    Route::post('/leave/request', [LeaveRequestController::class, 'store'])
        ->name('leave.store');

    // View leave history
    Route::get('/leave/history', [LeaveRequestController::class, 'history'])
        ->name('leave.history');
});

// Manager Leave Approvals
Route::middleware(['auth'])->group(function () {
    Route::get('/leave/approval', [LeaveRequestController::class, 'approveIndex'])->name('leave.approve');
    Route::post('/leave/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve.action');
    Route::post('/leave/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave.reject.action');
});

Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
   
});

Route::get('/attendance/manage', [AttendanceLogController::class, 'overview'])->name('employees.attendance');

Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
});
