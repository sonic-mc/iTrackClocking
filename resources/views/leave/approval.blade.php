@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Leave Approval Dashboard</h2>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if($leaveRequests->isEmpty())
        <p>No leave requests found.</p>
    @else
        <table class="table table-bordered">
            <thead class="bg-gray-100">
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaveRequests as $leave)
                    <tr>
                        <td>{{ $leave->employee->user->name ?? 'Unknown' }}</td>
                        <td>{{ ucfirst($leave->leave_type) }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                        <td>
                            @if($leave->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($leave->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($leave->status == 'pending')
                                <form action="{{ route('leave.approve.action', $leave->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve ✅</button>
                                </form>
                                <form action="{{ route('leave.reject.action', $leave->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject ❌</button>
                                </form>
                            @else
                                <em>No action</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
