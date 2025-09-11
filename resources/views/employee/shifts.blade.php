@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Your Upcoming Shifts</h1>

    @if($upcomingShifts->count())
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Shift Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Break</th>
            </tr>
        </thead>
        <tbody>
            @foreach($upcomingShifts as $entry)
            <tr>
                <td>{{ \Carbon\Carbon::parse($entry->date)->format('D, M d') }}</td>
                <td>{{ $entry->shift->name }}</td>
                <td>{{ \Carbon\Carbon::parse($entry->shift->start_time)->format('H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($entry->shift->end_time)->format('H:i') }}</td>
                <td>
                    @if($entry->shift->break_start && $entry->shift->break_end)
                        {{ \Carbon\Carbon::parse($entry->shift->break_start)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($entry->shift->break_end)->format('H:i') }}
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="alert alert-info">You have no upcoming shifts scheduled.</div>
    @endif
</div>
@endsection
