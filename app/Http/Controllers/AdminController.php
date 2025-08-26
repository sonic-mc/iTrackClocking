<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function settings()
    {
        return view('admin.settings');
    }

    public function biometric()
    {
        return view('admin.biometric');
    }

    public function audit()
    {
        return view('admin.audit');
    }
}
