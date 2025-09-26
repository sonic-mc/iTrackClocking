<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\AuditLogger;
use App\Models\User;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;


class ProfileController extends Controller
{

    use AuditLogger;
   // ProfileController.php

        public function index()
        {
            $user = auth()->user();
            $employee = $user->employee;

            return view('profile.index', compact('user', 'employee'));
        }

        public function edit()
        {
            $user = auth()->user();
            $employee = $user->employee;

            // Optional: preload dropdowns
            $branches = \App\Models\Branch::all();
            $departments = \App\Models\Department::all();

            return view('profile.edit', compact('user', 'employee', 'branches', 'departments'));
        }

        public function update(Request $request)
        {
            $user = auth()->user();
            $employee = $user->employee;

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'position' => 'required|string|max:255',
                'branch_id' => 'required|exists:branches,id',
                'department_id' => 'required|exists:departments,id',
                'status' => 'required|in:active,inactive',
            ]);

            // Update user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update employee
            $employee->update([
                'position' => $validated['position'],
                'branch_id' => $validated['branch_id'],
                'department_id' => $validated['department_id'],
                'status' => $validated['status'],
            ]);

            return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
        }


}
