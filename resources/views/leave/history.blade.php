@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Leave History</h2>

    @if($leaveRequests->isEmpty())
        <p>No leave requests found.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaveRequests as $leave)
                    <tr>
                        <td>{{ ucfirst($leave->leave_type) }}</td>
                        <td>{{ $leave->start_date }}</td>
                        <td>{{ $leave->end_date }}</td>
                        <td>
                            @if($leave->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($leave->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
