@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Geofence</h1>

    <form method="POST" action="{{ route('geofence.store') }}">
        @csrf
        <div class="mb-3">
            <label for="lat">Latitude</label>
            <input type="text" name="lat" id="lat" class="form-control">
        </div>

        <div class="mb-3">
            <label for="lng">Longitude</label>
            <input type="text" name="lng" id="lng" class="form-control">
        </div>

        <div class="mb-3">
            <label for="radius">Radius (meters)</label>
            <input type="number" name="radius" id="radius" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Geofence</button>
    </form>
</div>
@endsection
