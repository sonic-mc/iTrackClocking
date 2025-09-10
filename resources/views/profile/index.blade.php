@extends('layouts.app')

@section('title', 'Profile Overview')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">👤 Profile Overview</h2>
        <p class="text-muted">Your current account and employee details</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-semibold">
                    Account Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <span class="text-muted">{{ $user->email }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                            <p><strong>Status:</strong> {{ $employee->status }}</p>
                            <p><strong>Position:</strong> {{ $employee->position }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Branch:</strong> {{ $employee->branch->name ?? 'N/A' }}</p>
                            <p><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
