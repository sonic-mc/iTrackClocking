@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">✏️ Edit Geofence</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Update Geofence Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('geofence.update', $geofence) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="branch_id" class="form-label">Geofence Branch</label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $geofence->branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="name" class="form-label">Geofence Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $geofence->name }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="0.000001" name="latitude" id="latitude" class="form-control" value="{{ $geofence->latitude }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="0.000001" name="longitude" id="longitude" class="form-control" value="{{ $geofence->longitude }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="radius" class="form-label">Radius (meters)</label>
                        <input type="number" name="radius" id="radius" class="form-control" value="{{ $geofence->radius }}" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">💾 Update Geofence</button>
                    <a href="{{ route('geofence.index') }}" class="btn btn-secondary ms-2">↩️ Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
