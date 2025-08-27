@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Overtime History</h2>

    @if($overtimes->isEmpty())
        <p>No overtime recorded.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overtimes as $ot)
                    <tr>
                        <td>{{ $ot->date }}</td>
                        <td>{{ $ot->start_time }}</td>
                        <td>{{ $ot->end_time }}</td>
                        <td>{{ $ot->hours }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
