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

class AdminController extends Controller
{
    use AuditLogger;

    /**
     * Show admin settings page.
     */
    public function settings()
    {
        $this->logAudit('view_settings', 'Admin viewed the settings page');
        return view('admin.settings');
    }

    /**
     * Show biometric data page.
     */
    public function biometric()
    {
        $employees = Employee::with(['user', 'branch', 'department'])
                             ->orderBy('created_at', 'desc')
                             ->get();

        $biometrics = Biometric::with('employee.user')
                               ->orderBy('created_at', 'desc')
                               ->get();

        $this->logAudit('view_biometrics', 'Admin accessed biometric records');

        return view('admin.biometric', compact('employees', 'biometrics'));
    }

    /**
     * Show audit logs page.
     */
    public function audit()
    {
        $this->logAudit('view_audit_logs', 'Admin accessed the audit logs page');
        return view('admin.audit');
    }
}
