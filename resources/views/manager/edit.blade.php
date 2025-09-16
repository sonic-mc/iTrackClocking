@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Employee</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            ← Back
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="employee_number" class="form-label">Employee Number</label>
                    <input type="text" name="employee_number" id="employee_number" class="form-control" value="{{ old('employee_number', $employee->employee_number) }}" required>
                </div>

                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $employee->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select" required>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" name="position" id="position" class="form-control" value="{{ old('position', $employee->position) }}" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Employee</button>
            </form>
        </div>
    </div>
</div>
@endsection
