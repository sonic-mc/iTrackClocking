@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Shifts</h1>

    <!-- Shift Creation Form -->
    <form method="POST" action="{{ route('shifts.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name">Shift Name</label>
            <input type="text" name="name" id="name" class="form-control">
        </div>

        <div class="mb-3">
            <label for="start_time">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control">
        </div>

        <div class="mb-3">
            <label for="end_time">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control">
        </div>

        <div class="mb-3">
            <label for="break_start">Break Start (optional)</label>
            <input type="time" name="break_start" id="break_start" class="form-control">
        </div>

        <div class="mb-3">
            <label for="break_end">Break End (optional)</label>
            <input type="time" name="break_end" id="break_end" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Shift</button>
    </form>

    <!-- Available Shifts Table -->
    @if($shifts->count())
    <div class="mt-5">
        <h2>Available Shifts</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Break Start</th>
                    <th>Break End</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $shift)
                <tr>
                    <td>{{ $shift->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                    <td>{{ $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '—' }}</td>
                    <td>{{ $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '—' }}</td>
                    <td>{{ $shift->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="mt-5 text-muted">No shifts available yet.</div>
    @endif
</div>
@endsection
