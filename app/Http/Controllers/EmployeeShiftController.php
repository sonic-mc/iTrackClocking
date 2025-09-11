<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;


class EmployeeShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $employeeId = $user->employee->id;

        $upcomingShifts = EmployeeShift::with('shift')
        ->where('employee_id', $employeeId)
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->get();

    return view('employee.shifts', compact('upcomingShifts'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeShift $employeeShift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeShift $employeeShift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeShift $employeeShift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeShift $employeeShift)
    {
        //
    }
}
