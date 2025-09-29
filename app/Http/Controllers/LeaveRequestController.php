<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Traits\AuditLogger;

class LeaveRequestController extends Controller
{
    use AuditLogger;

    // Show leave request form
    public function requestForm()
    {
        $this->logAudit('view_leave_request_form', 'User accessed the leave request form');
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

        $leave = LeaveRequest::create([
            'employee_id' => $employeeId,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        $this->logAudit('create_leave_request', "Leave request #{$leave->id} submitted for {$request->leave_type}");

        return redirect()->route('leave.history')->with('success', 'Leave request submitted!');
    }

    // Show leave history for logged-in user
    public function history()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            $leaveRequests = collect();
        } else {
            $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                ->orderBy('start_date', 'desc')
                ->get();
        }

        $this->logAudit('view_leave_history', "User viewed their leave history");

        return view('leave.history', compact('leaveRequests'));
    }

    // Show all leave requests for approval
    public function approveIndex()
    {
        $leaveRequests = LeaveRequest::with('employee.user')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->logAudit('view_leave_approvals', "Admin viewed leave requests for approval");

        return view('leave.approval', compact('leaveRequests'));
    }

    // Approve a leave request
    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'approved';
        $leave->save();

        $this->logAudit('approve_leave_request', "Leave request #{$id} approved");

        return redirect()->route('leave.approve')->with('success', 'Leave request approved.');
    }

    // Reject a leave request
    public function reject($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'rejected';
        $leave->save();

        $this->logAudit('reject_leave_request', "Leave request #{$id} rejected");

        return redirect()->route('leave.approve')->with('success', 'Leave request rejected.');
    }
}
