@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Geofence</h1>

    <button onclick="getLocation()">Get GeoLocation</button>
    <p id="location"></p>

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
        document.getElementById('location').innerText = 
            `Latitude: ${latitude}, Longitude: ${longitude}`;
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


    {{-- Create Geofence Form --}}
    <form action="{{ route('geofence.store') }}" method="POST" style="margin-bottom: 30px;">
        @csrf
        <div>
            <input type="text" name="name" placeholder="Geofence Name" required>
        </div>
        <div>
            <input type="number" step="0.000001" name="latitude" placeholder="Latitude" required>
        </div>
        <div>
            <input type="number" step="0.000001" name="longitude" placeholder="Longitude" required>
        </div>
        <div>
            <input type="number" name="radius" placeholder="Radius (meters)" required>
        </div>
        <button type="submit">Save Geofence</button>
    </form>

    {{-- Display Geofences --}}
    <h2>Available Geofences</h2>
    @if($geofences->isEmpty())
        <p>No geofences created yet.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
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
    @endif
</div>
@endsection
