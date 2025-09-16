@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 h4 fw-bold">Leave Approval Dashboard</h2>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="leaveTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                ⏳ Pending
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                ✅ Approved
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                ❌ Rejected
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="leaveTabContent">

        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            @php $pending = $leaveRequests->where('status', 'pending'); @endphp
            @if($pending->isEmpty())
                <div class="alert alert-info">No pending leave requests.</div>
            @else
                <table class="table table-bordered">
                    <thead class="table-light">
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
                        @foreach($pending as $leave)
                        <tr>
                            <td>{{ $leave->employee->user->name ?? 'Unknown' }}</td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <form action="{{ route('leave.approve.action', $leave->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve ✅</button>
                                </form>
                                <form action="{{ route('leave.reject.action', $leave->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject ❌</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            @php $approved = $leaveRequests->where('status', 'approved'); @endphp
            @if($approved->isEmpty())
                <div class="alert alert-success">No approved leave requests.</div>
            @else
                <table class="table table-bordered">
                    <thead class="table-light">
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
                        @foreach($approved as $leave)
                        <tr>
                            <td>{{ $leave->employee->user->name ?? 'Unknown' }}</td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td><em>No action</em></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @php $rejected = $leaveRequests->where('status', 'rejected'); @endphp
            @if($rejected->isEmpty())
                <div class="alert alert-danger">No rejected leave requests.</div>
            @else
                <table class="table table-bordered">
                    <thead class="table-light">
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
                        @foreach($rejected as $leave)
                        <tr>
                            <td>{{ $leave->employee->user->name ?? 'Unknown' }}</td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                            <td><span class="badge bg-danger">Rejected</span></td>
                            <td><em>No action</em></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>
@endsection
