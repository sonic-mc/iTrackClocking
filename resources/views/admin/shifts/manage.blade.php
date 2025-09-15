@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🕒 Manage Shifts</h2>

    <ul class="nav nav-tabs mb-3" id="shiftTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
                ➕ Create Shift
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab">
                📋 Available Shifts
            </button>
        </li>
    </ul>

    <div class="tab-content" id="shiftTabsContent">
        <!-- Create Shift Tab -->
        <div class="tab-pane fade show active" id="create" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Create New Shift</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('shifts.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Shift Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" name="end_time" id="end_time" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="break_start" class="form-label">Break Start</label>
                                <input type="time" name="break_start" id="break_start" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="break_end" class="form-label">Break End</label>
                                <input type="time" name="break_end" id="break_end" class="form-control">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">💾 Save Shift</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Available Shifts Tab -->
        <div class="tab-pane fade" id="available" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Available Shifts</h5>
                </div>
                <div class="card-body">
                    @if($shifts->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Break Start</th>
                                        <th>Break End</th>
                                        <th>Created</th>
                                        <th class="text-center">Actions</th>
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
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- View -->
                                                <a href="{{ route('shifts.show', $shift) }}" class="btn btn-sm btn-outline-secondary" title="View Shift">
                                                    👁️
                                                </a>
                            
                                                <!-- Edit -->
                                                <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-sm btn-outline-primary" title="Edit Shift">
                                                    ✏️
                                                </a>
                            
                                                <!-- Delete -->
                                                <form action="{{ route('shifts.destroy', $shift) }}" method="POST" onsubmit="return confirm('Delete this shift?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Shift">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                        </div>
                    @else
                        <p class="text-muted">No shifts available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
