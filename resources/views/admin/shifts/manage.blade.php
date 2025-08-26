@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Shifts</h1>

    <form method="POST" action="{{ route('shifts.store') }}">
        @csrf
        <div class="mb-3">
            <label for="shift_name">Shift Name</label>
            <input type="text" name="shift_name" id="shift_name" class="form-control">
        </div>

        <div class="mb-3">
            <label for="start_time">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control">
        </div>

        <div class="mb-3">
            <label for="end_time">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Shift</button>
    </form>
</div>
@endsection
