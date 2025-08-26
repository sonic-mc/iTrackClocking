@extends('layouts.app')

@section('title', 'Clock In/Out')

@section('content')
<div style="max-width:800px; margin:auto;">

    <h1 style="font-size:24px; margin-bottom:20px;">⏰ Clock In / Clock Out</h1>

    {{-- Success / Error messages --}}
    @if(session('success'))
        <div style="background:#d1fae5; color:#065f46; padding:10px; border-radius:5px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:5px; margin-bottom:20px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Clock Button --}}
    <form action="{{ route('attendance.clock') }}" method="POST">
        @csrf
        <button type="submit" 
            style="padding:12px 25px; background:#1E3A8A; color:white; font-size:16px; border:none; border-radius:8px; cursor:pointer;">
            {{ auth()->user()->isClockedIn() ? 'Clock Out' : 'Clock In' }}
        </button>
    </form>

    {{-- Recent Logs --}}
    <div style="margin-top:30px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
        <h2 style="font-size:18px; margin-bottom:10px;">Recent Attendance</h2>

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
                        <td style="border:1px solid #e5e7eb; padding:10px;">{{ $log->created_at->format('Y-m-d') }}</td>
                        <td style="border:1px solid #e5e7eb; padding:10px;">{{ $log->clock_in_time ?? '-' }}</td>
                        <td style="border:1px solid #e5e7eb; padding:10px;">{{ $log->clock_out_time ?? '-' }}</td>
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
                            No attendance logs yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
