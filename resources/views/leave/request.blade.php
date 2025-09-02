@extends('layouts.app')

@section('styles')
<style>
    .leave-request-container {
        max-width: 768px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .page-title {
        color: #1e293b;
        font-size: 1.875rem;
        font-weight: 600;
        margin-bottom: 2rem;
        position: relative;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -0.5rem;
        left: 0;
        width: 3rem;
        height: 0.25rem;
        background: #3b82f6;
        border-radius: 0.25rem;
    }

    .alert {
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        animation: slideIn 0.3s ease-out;
    }

    .alert-success {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control:hover {
        border-color: #94a3b8;
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        padding-right: 2.5rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
        gap: 0.5rem;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .date-inputs-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-1rem);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 640px) {
        .leave-request-container {
            margin: 1rem;
            padding: 1.5rem;
        }

        .date-inputs-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="leave-request-container">
    <h2 class="page-title">Request Leave</h2>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('leave.store') }}" method="POST" class="leave-form">
        @csrf
        <div class="form-group">
            <label for="leave_type" class="form-label">Leave Type</label>
            <select name="leave_type" id="leave_type" class="form-control" required>
                <option value="" disabled selected>Select type of leave</option>
                <option value="sick">Sick Leave</option>
                <option value="vacation">Vacation Leave</option>
                <option value="personal">Personal Leave</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="date-inputs-container">
            <div class="form-group">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>

            <div class="form-group">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Submit Leave Request
        </button>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
});
</script>
@endsection
@endsection
