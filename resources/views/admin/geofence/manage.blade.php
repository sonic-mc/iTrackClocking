@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Manage Geofence</h1>

    {{-- Geolocation Button --}}
    <div class="mb-4">
        <button class="btn btn-outline-primary" onclick="getLocation()">📍 Get Current Location</button>
        <p id="location" class="mt-2 text-muted"></p>
    </div>

    {{-- Geofence Form Card --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white">
            <h5 class="mb-0">Create New Geofence</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('geofence.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Geofence Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Main Office" required>
                    </div>
                    <div class="col-md-4">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="0.000001" name="latitude" id="latitude" class="form-control" placeholder="-17.8252" required>
                    </div>
                    <div class="col-md-4">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="0.000001" name="longitude" id="longitude" class="form-control" placeholder="31.0335" required>
                    </div>
                    <div class="col-md-4">
                        <label for="radius" class="form-label">Radius (meters)</label>
                        <input type="number" name="radius" id="radius" class="form-control" placeholder="e.g. 100" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">💾 Save Geofence</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-white">
                <h5 class="mb-0">📍 Available Geofences</h5>
            </div>
    
            <div class="card-body">
                @if($geofences->isEmpty())
                    <p class="text-muted">No geofences created yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Radius (m)</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($geofences as $geofence)
                                    <tr>
                                        <td>{{ $geofence->name }}</td>
                                        <td>{{ $geofence->latitude }}</td>
                                        <td>{{ $geofence->longitude }}</td>
                                        <td>{{ $geofence->radius }}</td>
                                        <td>{{ $geofence->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    

{{-- Geolocation Script --}}
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        document.getElementById('location').innerText = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    document.getElementById('location').innerText = `Latitude: ${latitude}, Longitude: ${longitude}`;
    document.getElementById('latitude').value = latitude;
    document.getElementById('longitude').value = longitude;
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        default:
            alert("An unknown error occurred.");
            break;
    }
}
</script>
@endsection
