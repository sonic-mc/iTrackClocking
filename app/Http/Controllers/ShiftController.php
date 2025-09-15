<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Models\EmployeeShift;
use App\Models\Employee;


class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $shifts = Shift::orderBy('start_time')->get(); // You can sort by name or start_time

    return view('admin.shifts.manage', compact('shifts'));
}


    public function assignShift(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id'    => 'required|exists:shifts,id',
            'date'        => 'required|date',
        ]);
    
        // Prevent duplicate assignment for same day
        EmployeeShift::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date'        => $request->date,
            ],
            [
                'shift_id' => $request->shift_id,
            ]
        );
        
    
        return redirect()->back()->with('success', 'Shift assigned successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
        ]);

        Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
        ]);

        return redirect()->route('shifts.manage')->with('success', 'Shift created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        return view('admin.shifts.show', compact('shift'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
{
    return view('admin.shifts.edit', compact('shift'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
        ]);
    
        $shift->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
        ]);
    
        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
   
    public function destroy(Shift $shift)
    {
    $shift->delete();

    return redirect()->route('shifts.manage')->with('success', 'Shift deleted successfully.');

}

}
