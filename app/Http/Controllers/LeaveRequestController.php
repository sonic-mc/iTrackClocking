<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{

    // Show leave request form
    public function requestForm()
    {
        return view('leave.request');
    }

    // Store new leave request
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:sick,vacation,personal,other',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $employeeId = auth()->user()->employee->id;

        LeaveRequest::create([
            'employee_id' => $employeeId,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        return redirect()->route('leave.history')->with('success', 'Leave request submitted!');
    }

    // Show leave history for logged-in user
    public function history()
    {
        $employee = auth()->user()->employee;
    
        if (!$employee) {
            // If the user doesn't have an employee record, return empty collection
            $leaveRequests = collect();
        } else {
            // Fetch all leave requests for this employee
            $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                ->orderBy('start_date', 'desc') // most recent first
                ->get();
        }
    
        return view('leave.history', compact('leaveRequests'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
   

   
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        //
    }

    public function approveIndex()
    {
        // Fetch all leave requests with employee details
        $leaveRequests = LeaveRequest::with('employee.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leave.approval', compact('leaveRequests'));
    }

    /**
     * Approve a leave request
     */
    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'approved';
        $leave->save();

        return redirect()->route('leave.approve')->with('success', 'Leave request approved.');
    }

    /**
     * Reject a leave request
     */
    public function reject($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'rejected';
        $leave->save();

        return redirect()->route('leave.approve')->with('success', 'Leave request rejected.');
    }
}


