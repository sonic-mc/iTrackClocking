@extends('layouts.app')

@section('title', 'Biometric Setup')

@section('content')
<div class="container">
    <h2 class="mb-4">🔐 Biometric Setup</h2>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white">
            <h5 class="mb-0">Register Fingerprint</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('biometric.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Select Employee</label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">-- Choose --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->user->name }} ({{ $employee->employee_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="captureFingerprint()">
                            🧬 Capture Fingerprint
                        </button>
                    </div>
                </div>

                <input type="hidden" name="fingerprint_data" id="fingerprint_data">

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">💾 Save Biometric</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Registered Biometric Profiles</h5>
        </div>
        <div class="card-body">
            @if($biometrics->isEmpty())
                <p class="text-muted">No biometric profiles registered yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Employee Number</th>
                                <th>Registered At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($biometrics as $bio)
                                <tr>
                                    <td>{{ $bio->employee->user->name }}</td>
                                    <td>{{ $bio->employee->employee_number }}</td>
                                    <td>{{ $bio->created_at->format('d M Y H:i') }}</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function captureFingerprint() {
    // Simulate fingerprint capture
    const fakeFingerprint = 'sample-fingerprint-data-' + Date.now();
    document.getElementById('fingerprint_data').value = fakeFingerprint;
    alert('Fingerprint captured successfully!');
}
</script>
@endsection
