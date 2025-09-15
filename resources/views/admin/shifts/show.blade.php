@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">👁️ Shift Details</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $shift->name }}</dd>

                <dt class="col-sm-3">Start Time</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</dd>

                <dt class="col-sm-3">End Time</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</dd>

                <dt class="col-sm-3">Break Start</dt>
                <dd class="col-sm-9">{{ $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '—' }}</dd>

                <dt class="col-sm-3">Break End</dt>
                <dd class="col-sm-9">{{ $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '—' }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $shift->created_at->format('d M Y H:i') }}</dd>
            </dl>

            <div class="mt-4">
                <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-primary">✏️ Edit Shift</a>
                <a href="{{ route('shifts.index') }}" class="btn btn-secondary ms-2">↩️ Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
