@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Employee Profile</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            ← Back
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">
            {{ $employee->user->name }}
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Employee #:</strong> {{ $employee->employee_number }}</p>
                    <p><strong>Email:</strong> {{ $employee->user->email ?? 'N/A' }}</p>
                    <p><strong>Branch:</strong> {{ $employee->branch->name ?? 'N/A' }}</p>
                    <p><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Position:</strong> {{ $employee->position }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </p>
                    <p><strong>Created At:</strong> {{ $employee->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>Last Updated:</strong> {{ $employee->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
