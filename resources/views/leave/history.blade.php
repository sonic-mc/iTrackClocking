@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Leave History</h2>

    @if($leaveRequests->isEmpty())
        <p>No leave requests found.</p>
    @else
        @php
            $pendingLeaves = $leaveRequests->where('status', 'pending');
            $approvedLeaves = $leaveRequests->where('status', 'approved');
            $rejectedLeaves = $leaveRequests->where('status', 'rejected');
            $pastLeaves = $leaveRequests->filter(fn($leave) => $leave->end_date < now()->toDateString());
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

        {{-- Tabbed Sections --}}
        <ul class="nav nav-tabs mt-4" id="leaveTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">Approved</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">Rejected</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="leaveTabsContent">
            {{-- Approved Tab --}}
            <div class="tab-pane fade show active" id="approved" role="tabpanel">
                @if($approvedLeaves->isNotEmpty())
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
                @else
                    <p class="text-muted">No approved leaves.</p>
                @endif
            </div>

            {{-- Rejected Tab --}}
            <div class="tab-pane fade" id="rejected" role="tabpanel">
                @if($rejectedLeaves->isNotEmpty())
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
                @else
                    <p class="text-muted">No rejected leaves.</p>
                @endif
            </div>
        </div>

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
