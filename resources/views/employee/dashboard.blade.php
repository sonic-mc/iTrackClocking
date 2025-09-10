@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')

@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 10px 20px; border-radius: 5px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<h1 style="font-size: 24px; margin-bottom: 20px;">Welcome, {{ $user->name }}</h1>

<!-- Clock In / Clock Out -->
{{-- <div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:20px;">
    <h2 style="font-size:18px; margin-bottom:10px;">Clock In / Clock Out</h2>
    <p style="color:#6b7280; margin-bottom:10px;">Click below to log your attendance</p>
    <form action="{{ route('employee.clock') }}" method="POST">
        @csrf
        <button type="submit" style="padding:10px 20px; background:#1E3A8A; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            Clock In / Out
        </button>
    </form>
</div> --}}

<!-- Recent Attendance Logs -->
<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:20px;">
    <h2 style="font-size:18px; margin-bottom:10px;">Recent Attendance</h2>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th style="border:1px solid #e5e7eb; padding:10px;">Date</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Clock In</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Clock Out</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceLogs as $log)
            <tr>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ \Carbon\Carbon::parse($log->clock_in_time)->format('Y-m-d') }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $log->clock_in_time ? \Carbon\Carbon::parse($log->clock_in_time)->format('H:i') : '-' }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $log->clock_out_time ? \Carbon\Carbon::parse($log->clock_out_time)->format('H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center; padding:10px;">No attendance logs found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Leave Requests -->
<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:20px;">
    <h2 style="font-size:18px; margin-bottom:10px;">Recent Leave Requests</h2>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th style="border:1px solid #e5e7eb; padding:10px;">Leave Type</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Start Date</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">End Date</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaveRequests as $leave)
            <tr>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $leave->type }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $leave->start_date }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $leave->end_date }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ ucfirst($leave->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:10px;">No leave requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Shift Info -->
<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:20px;">
    <h2 style="font-size:18px; margin-bottom:10px;">My Shift</h2>
    @if($shift)
        <p><strong>{{ $shift->name }}</strong></p>
        <p>Start: {{ $shift->start_time }} | End: {{ $shift->end_time }}</p>
        @if($shift->break_start && $shift->break_end)
            <p>Break: {{ $shift->break_start }} - {{ $shift->break_end }}</p>
        @endif
    @else
        <p style="color:#6b7280;">No shift assigned.</p>
    @endif
</div>

<!-- Overtime Logs -->
<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:20px;">
    <h2 style="font-size:18px; margin-bottom:10px;">Recent Overtime</h2>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th style="border:1px solid #e5e7eb; padding:10px;">Date</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Hours</th>
                <th style="border:1px solid #e5e7eb; padding:10px;">Approved By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overtimeLogs as $ot)
            <tr>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $ot->date }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">{{ $ot->hours }}</td>
                <td style="border:1px solid #e5e7eb; padding:10px;">
                    {{ $ot->approved_by ? \App\Models\User::find($ot->approved_by)->name : 'Pending' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center; padding:10px;">No overtime records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Notifications -->
<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
    <h2 style="font-size:18px; margin-bottom:10px;">Recent Notifications</h2>
    <ul style="list-style:none; margin:0; padding:0;">
        @forelse($notifications as $note)
        <li style="padding:10px; border-bottom:1px solid #e5e7eb;">{{ $note->message }}</li>
        @empty
        <li style="padding:10px; text-align:center; color:#6b7280;">No notifications found.</li>
        @endforelse
    </ul>
</div>

@endsection
