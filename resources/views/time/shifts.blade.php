@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Shift Schedule</h2>

    @if($shifts->isEmpty())
        <p>No upcoming shifts scheduled.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Start</th>
                    <th>End</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $shift)
                    <tr>
                        <td>{{ $shift->date }}</td>
                        <td>{{ $shift->name }}</td>
                        <td>{{ $shift->start_time }}</td>
                        <td>{{ $shift->end_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
