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

    </div>
</div>
@endsection
