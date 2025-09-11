@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div class="container" style="max-width: 1100px; margin: auto;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h2 class="mb-0" style="font-size: 20px;">📅 Attendance History</h2>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="attendanceTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        History
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="attendanceTabContent">
                <div class="tab-pane fade show active" id="history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th>Geofence</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $log->clock_in_time ? \Carbon\Carbon::parse($log->clock_in_time)->format('H:i:s') : '-' }}</td>
                                        <td>{{ $log->clock_out_time ? \Carbon\Carbon::parse($log->clock_out_time)->format('H:i:s') : '-' }}</td>
                                        <td>
                                            @if($log->clock_in_time && $log->clock_out_time)
                                                ✅ Completed
                                            @elseif($log->clock_in_time)
                                                🟢 Active
                                            @else
                                                ⏳ Pending
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->location_lat && $log->location_lng)
                                                <a href="https://www.google.com/maps?q={{ $log->location_lat }},{{ $log->location_lng }}"
                                                   target="_blank" class="text-primary text-decoration-underline">
                                                    📍 View Map
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->geofence_status)
                                                ✅ Inside
                                            @else
                                                ❌ Outside
                                            @endif
                                        </td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $log->device_info ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $attendanceLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
