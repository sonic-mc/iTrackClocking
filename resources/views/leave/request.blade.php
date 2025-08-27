@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Request Leave</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('leave.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="leave_type">Leave Type</label>
            <select name="leave_type" id="leave_type" class="form-control" required>
                <option value="">Select type</option>
                <option value="sick">Sick</option>
                <option value="vacation">Vacation</option>
                <option value="personal">Personal</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="end_date">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>
@endsection
