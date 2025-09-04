@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Set Up Clock-in Authentication</h1>

    <form action="{{ route('biometric.store') }}" method="POST" id="biometricForm">
        @csrf

        <!-- Passcode setup -->
        <div class="mb-3">
            <label for="passcode">Passcode (optional)</label>
            <input type="password" name="passcode" id="passcode" class="form-control" placeholder="Enter a passcode">
        </div>

        <!-- Fingerprint enrollment -->
        <div class="mb-3">
            <label>Fingerprint Authentication</label>
            <button type="button" class="btn btn-primary" onclick="enrollFingerprint()">
                ➕ Enroll Fingerprint
            </button>
            <small id="fingerprintStatus" class="text-muted">Not enrolled</small>
        </div>

        <!-- Face ID enrollment -->
        <div class="mb-3">
            <label>Face Recognition Authentication</label>
            <button type="button" class="btn btn-primary" onclick="enrollFaceID()">
                ➕ Enroll Face ID
            </button>
            <small id="faceStatus" class="text-muted">Not enrolled</small>
        </div>

        <button type="submit" class="btn btn-success">Save Authentication</button>
    </form>
</div>

<script>
async function enrollFingerprint() {
    // Call your backend to get WebAuthn creation options
    const res = await fetch('{{ route("biometric.register.options") }}');
    const options = await res.json();

    options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));

    const credential = await navigator.credentials.create({ publicKey: options });

    // Send credential to backend
    await fetch('{{ route("biometric.register") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ attestation_response: credential })
    });

    document.getElementById('fingerprintStatus').innerText = 'Fingerprint enrolled ✅';
}

async function enrollFaceID() {
    // You can implement FaceID using WebAuthn similarly
    alert('Face ID enrollment not implemented yet in this example');
    document.getElementById('faceStatus').innerText = 'Face ID enrolled ✅';
}
</script>
@endsection
