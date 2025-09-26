<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAttestationResponse;
use Illuminate\Support\Str;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialParameters;
use App\Models\AttendanceLog;
use App\Traits\AuditLogger;


class BiometricController extends Controller
{

    use AuditLogger;

    public function setup()
    {
        $user = Auth::user();

        // Relying Party (your app)
        $rp = new PublicKeyCredentialRpEntity(
            'iTrackClocking',        // Name of your app
            'itrackclocking.local',  // Your domain
            null                     // Icon URL (optional)
        );
    
        // User entity
        $userEntity = new PublicKeyCredentialUserEntity(
            $user->name,                     // User display name
            (string)$user->id,               // User ID
            $user->email ?? $user->name,     // User name or email
            null                             // Icon URL (optional)
        );
    
        // Challenge (random string, base64url)
        $challenge = random_bytes(32);
    
        // Credential parameters (public key algorithms)
        $pubKeyCredParams = [
            new PublicKeyCredentialParameters('public-key', -7),   // ES256
            new PublicKeyCredentialParameters('public-key', -257), // RS256
        ];
    
        $creationOptions = new PublicKeyCredentialCreationOptions(
            $rp,
            $userEntity,
            $challenge,
            $pubKeyCredParams
        );
    
        return view('biometric.setup', [
            'creationOptions' => $creationOptions,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([
            'passcode' => 'nullable|string|min:4',
            'attestation_response' => 'nullable|array', // WebAuthn attestation
        ]);
    
        $biometricData = $user->biometric_data ?? [];
    
        // Save passcode if provided
        if ($request->filled('passcode')) {
            $biometricData['passcode'] = $request->passcode;
        }
    
        // Save fingerprint / face WebAuthn credential if provided
        if ($request->has('attestation_response')) {
            // The attestation_response comes from the JS WebAuthn API
            $biometricData['webauthn'][] = $request->attestation_response;
        }
    
        $user->biometric_data = $biometricData;
        $user->save();
    
        return redirect()->route('home')->with('success', 'Biometric setup completed.');
    }
    

    public function register(Request $request)
    {
        $user = Auth::user();
        $attestationResponse = $request->input('attestation_response');

        // Verify attestation
        $authenticator = new AuthenticatorAttestationResponse();
        $credentialData = $authenticator->getData($attestationResponse);

        // Save credential to user's biometric_data
        $user->biometric_data = [
            'credential_id' => $credentialData->getCredentialId(),
            'public_key' => $credentialData->getCredentialPublicKey(),
        ];
        $user->save();

        return response()->json(['success' => true]);
    }

    public function authenticate(Request $request)
    {
        $user = Auth::user();
        $assertionResponse = $request->input('assertion_response');

        // Verify assertion with stored public key
        $authenticator = new AuthenticatorAssertionResponse();
        $verified = $authenticator->verify(
            $assertionResponse,
            $user->biometric_data['public_key'],
            session('webauthn_challenge')
        );

        if ($verified) {
            // Clock in logic
            $employee = $user->employee;
            $attendance = AttendanceLog::firstOrCreate(
                ['employee_id' => $employee->id, 'clock_in_time' => today()],
                ['clock_in_time' => now()]
            );

            return response()->json(['success' => true, 'message' => 'Clocked in successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Authentication failed']);
    }

    public function registerOptions()
{
    $user = Auth::user();

    $rp = new PublicKeyCredentialRpEntity('iTrackClocking', 'itrackclocking.local');
    $userEntity = new PublicKeyCredentialUserEntity(
        $user->name,
        (string) $user->id,
        $user->email ?? $user->name
    );

    $challenge = random_bytes(32);

    $pubKeyCredParams = [
        new PublicKeyCredentialParameters('public-key', -7),
        new PublicKeyCredentialParameters('public-key', -257),
    ];

    $creationOptions = new PublicKeyCredentialCreationOptions(
        $rp,
        $userEntity,
        $challenge,
        $pubKeyCredParams
    );

    // Return JSON for front-end
    return response()->json($creationOptions);
}
}


