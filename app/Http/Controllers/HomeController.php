<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // If user is not logged in, show public home
        if (! Auth::check()) {
            return view('home');
        }

        $user = Auth::user();

        // If the project uses a role package (e.g. Spatie) with hasRole()
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('manager')) {
                return redirect()->route('manager.dashboard');
            }

            if ($user->hasRole('employee')) {
                return redirect()->route('employee.dashboard');
            }
        }

        // Fallback: check a plain "role" attribute on the users table
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($role === 'employee') {
            return redirect()->route('employee.dashboard');
        }

        // Default behaviour if role is unknown
        return view('home');
    }
}

