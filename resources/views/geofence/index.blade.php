@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-3">Geofencing Overview</h4>
    <div id="map" style="height: 600px; border-radius: 12px; overflow: hidden;"></div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-17.8252, 31.0335], 13); // Default to Harare

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const geofences = @json($geofences);

    geofences.forEach(geofence => {
        const circle = L.circle([geofence.latitude, geofence.longitude], {
            color: '#007bff',
            fillColor: '#cce5ff',
            fillOpacity: 0.4,
            radius: geofence.radius
        }).addTo(map);

        let popupContent = `
            <strong>${geofence.name}</strong><br>
            Branch: ${geofence.branch.name}<br>
            <hr>
            <strong>Active Employees:</strong><br>
        `;

        if (geofence.active_employees.length > 0) {
            geofence.active_employees.forEach(emp => {
                popupContent += `- ${emp.user.name}<br>`;
            });
        } else {
            popupContent += `<em>No active employees</em>`;
        }

        circle.bindPopup(popupContent);
    });
</script>
@endpush
