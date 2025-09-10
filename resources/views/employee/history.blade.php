@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div style="max-width:1100px; margin:auto;">
    <h1 style="font-size:24px; margin-bottom:20px;">📅 Attendance History</h1>

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th style="border:1px solid #e5e7eb; padding:10px;">Date</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Clock In</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Clock Out</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Status</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Location</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Geofence</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Device</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceLogs as $log)
                <tr>
                    {{-- Date --}}
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        {{ $log->created_at->format('Y-m-d') }}
                    </td>

                    {{-- Clock In --}}
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        {{ $log->clock_in_time ? \Carbon\Carbon::parse($log->clock_in_time)->format('H:i:s') : '-' }}
                    </td>

                    {{-- Clock Out --}}
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        {{ $log->clock_out_time ? \Carbon\Carbon::parse($log->clock_out_time)->format('H:i:s') : '-' }}
                    </td>

                    {{-- Status --}}
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        @if($log->clock_in_time && $log->clock_out_time)
                            ✅ Completed
                        @elseif($log->clock_in_time)
                            🟢 Active
                        @else
                            ⏳ Pending
                        @endif
                    </td>

                    {{-- Location --}}
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        @if($log->location_lat && $log->location_lng)
                            <a href="https://www.google.com/maps?q={{ $log->location_lat }},{{ $log->location_lng }}"
                               target="_blank" style="color:#2563eb; text-decoration:underline;">
                                📍 View Map
                            </a>
                        @else
                            -
                        @endif
                    </td>

                    {{-- Geofence Status --}}
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        @if($log->geofence_status)
                            ✅ Inside
                        @else
                            ❌ Outside
                        @endif
                    </td>

                    {{-- Device Info --}}
                    <td style="border:1px solid #e5e7eb; padding:10px; max-width:200px; overflow:hidden; text-overflow:ellipsis;">
                        {{ $log->device_info ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:10px; color:#6b7280;">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $attendanceLogs->links() }}
    </div>
</div>
@endsection
