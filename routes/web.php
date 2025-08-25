<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');
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

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee/dashboard', [App\Http\Controllers\EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::post('/employee/clock', [App\Http\Controllers\EmployeeController::class, 'clock'])->name('employee.clock');
});


// Employee-only dashboard
// Route::middleware(['auth', 'role:employee'])->group(function () {
//     Route::get('/employee/dashboard', function () {
//         return view('employee.dashboard');
//     })->name('employee.dashboard');
// });

// Manager-only dashboard
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager/dashboard', function () {
        return view('manager.dashboard');
    })->name('manager.dashboard');
});

// Admin-only dashboard
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Admin + Manager routes
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
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
    Route::get('/clock', [AttendanceController::class, 'showClock'])->name('clock');  // <--- matches route in Blade
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
    Route::get('/history', [AttendanceController::class, 'history'])->name('history');
});