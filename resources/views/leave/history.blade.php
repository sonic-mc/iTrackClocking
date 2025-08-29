@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Leave History</h2>

    @if($leaveRequests->isEmpty())
        <p>No leave requests found.</p>
    @else
        {{-- Separate leaves by status --}}
        @php
            $pendingLeaves = $leaveRequests->where('status', 'pending');
            $approvedLeaves = $leaveRequests->where('status', 'approved');
            $rejectedLeaves = $leaveRequests->where('status', 'rejected');
            $pastLeaves = $leaveRequests->filter(function($leave) {
                return $leave->end_date < now()->toDateString();
            });
        @endphp

        {{-- Pending Leaves --}}
        @if($pendingLeaves->isNotEmpty())
            <h3 class="mt-6 mb-2 font-semibold">Pending Leaves</h3>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingLeaves as $leave)
                        <tr>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Approved Leaves --}}
        @if($approvedLeaves->isNotEmpty())
            <h3 class="mt-6 mb-2 font-semibold">Approved Leaves</h3>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedLeaves as $leave)
                        <tr>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Rejected Leaves --}}
        @if($rejectedLeaves->isNotEmpty())
            <h3 class="mt-6 mb-2 font-semibold text-red-600">Rejected Leaves</h3>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rejectedLeaves as $leave)
                        <tr>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Past Leaves --}}
        @if($pastLeaves->isNotEmpty())
            <h3 class="mt-6 mb-2 font-semibold">Past Leaves</h3>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pastLeaves as $leave)
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
    @endif
</div>
@endsection
