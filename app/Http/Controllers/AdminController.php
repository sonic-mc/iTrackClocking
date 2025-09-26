<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Biometric;
use App\Traits\AuditLogger;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{

    use AuditLogger;

    public function settings()
    {
        return view('admin.settings');
    }

    public function biometric()
{
    $employees = Employee::with(['user', 'branch', 'department'])->orderBy('created_at', 'desc')->get();
    $biometrics = Biometric::with('employee.user')->orderBy('created_at', 'desc')->get();

    return view('admin.biometric', compact('employees', 'biometrics'));
}

    public function audit()
    {
        return view('admin.audit');
    }
}
