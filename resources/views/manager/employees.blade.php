@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 h4 fw-bold">Employee Shift Management</h2>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="shiftTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned" type="button" role="tab">
                👥 Employees with Shifts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="assign-tab" data-bs-toggle="tab" data-bs-target="#assign" type="button" role="tab">
                📅 Assign Shifts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                🛠️ Edit Employee Details
            </button>
        </li>
        
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="shiftTabContent">

        <!-- Tab 1: Employees with Assigned Shifts -->
        <div class="tab-pane fade show active" id="assigned" role="tabpanel">
            @if($assignedEmployees->isEmpty())
                <div class="alert alert-info">No shift assignments found.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Shift Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedEmployees as $assignment)
                        <tr>
                            <td>{{ $assignment->employee->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($assignment->date)->format('Y-m-d') }}</td>
                            <td>{{ $assignment->shift->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($assignment->shift->start_time)->format('H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($assignment->shift->end_time)->format('H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Tab 2: Assign Shifts -->
        <div class="tab-pane fade" id="assign" role="tabpanel">
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary mb-3">
                Add Employee
            </a>

            @if($employees->isEmpty())
                <div class="alert alert-warning">No employees found.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Number</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th class="text-center">Assign Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->user->name ?? 'N/A' }}</td>
                            <td>{{ $employee->employee_number }}</td>
                            <td>{{ $employee->branch->name ?? 'N/A' }}</td>
                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>{{ ucfirst($employee->status) }}</td>
                            <td class="text-center">
                                <form action="{{ route('employees.assignShift') }}" method="POST" class="d-flex flex-column align-items-center">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                    <input type="date" name="date" value="{{ $today ?? now()->format('Y-m-d') }}" class="form-control form-control-sm mb-1" required>
                                    <select name="shift_id" class="form-select form-select-sm mb-1" required>
                                        <option value="">Assign Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">
                                                {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Tab 3: Edit Employee Details -->
        <div class="tab-pane fade" id="edit" role="tabpanel">
            <form method="GET" action="{{ route('employees.search') }}" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search by Name or Number</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="e.g. John Doe or EMP123">
                    </div>
                    <div class="col-md-2">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-select">
                            <option value="">All</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select">
                            <option value="">All</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            @if($filteredEmployees->isEmpty())
                <div class="alert alert-info">No employees match your criteria.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Employee #</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredEmployees as $employee)
                        <tr>
                            <td>{{ $employee->user->name ?? 'N/A' }}</td>
                            <td>{{ $employee->employee_number }}</td>
                            <td>{{ $employee->branch->name ?? 'N/A' }}</td>
                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>{{ ucfirst($employee->status) }}</td>
                            <td class="text-center">
                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info me-1">View</a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>


    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabEl = document.querySelectorAll('button[data-bs-toggle="tab"]');

    // Restore last active tab on page load
    const lastTab = localStorage.getItem('activeTab');
    if (lastTab) {
        const someTab = document.querySelector(`button[data-bs-target="${lastTab}"]`);
        if (someTab) new bootstrap.Tab(someTab).show();
    }

    // When switching tabs, store active tab id
    tabEl.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            localStorage.setItem('activeTab', event.target.getAttribute('data-bs-target'));
        });
    });
});
</script>
@endpush
