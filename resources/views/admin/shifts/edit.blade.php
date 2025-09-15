@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">✏️ Edit Shift</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Update Shift Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('shifts.update', $shift) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Shift Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $shift->name) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $shift->start_time) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $shift->end_time) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="break_start" class="form-label">Break Start</label>
                        <input type="time" name="break_start" id="break_start" class="form-control" value="{{ old('break_start', $shift->break_start) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="break_end" class="form-label">Break End</label>
                        <input type="time" name="break_end" id="break_end" class="form-control" value="{{ old('break_end', $shift->break_end) }}">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">💾 Update Shift</button>
                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary ms-2">↩️ Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
