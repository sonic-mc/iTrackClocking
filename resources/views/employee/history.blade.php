@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div style="max-width:900px; margin:auto;">
    <h1 style="font-size:24px; margin-bottom:20px;">📅 Attendance History</h1>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th style="border:1px solid #e5e7eb; padding:10px;">Date</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Clock In</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Clock Out</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceLogs as $log)
                <tr>
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        {{ $log->created_at->format('Y-m-d') }}
                    </td>
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        {{ $log->clock_in_time ?? '-' }}
                    </td>
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        {{ $log->clock_out_time ?? '-' }}
                    </td>
                    <td style="border:1px solid #e5e7eb; padding:10px;">
                        @if($log->clock_in_time && $log->clock_out_time)
                            ✅ Completed
                        @elseif($log->clock_in_time)
                            🟢 Active
                        @else
                            ⏳ Pending
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:10px; color:#6b7280;">
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
