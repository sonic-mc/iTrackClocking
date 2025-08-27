@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Today's Breaks</h2>

    @if($breaks->isEmpty())
        <p>No breaks scheduled today.</p>
    @else
        <ul class="list-group">
            @foreach($breaks as $break)
                <li class="list-group-item">
                    <strong>{{ $break->type }}</strong> <br>
                    {{ $break->start_time }} - {{ $break->end_time }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
