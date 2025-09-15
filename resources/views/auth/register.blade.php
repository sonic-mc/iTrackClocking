@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <h2 class="auth-header">Create your account</h2>
    <p class="auth-subtext">Please fill in your details to get started.</p>

    <div class="form-group">
        <input 
            id="name"
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Full Name"
            value="{{ old('name') }}"
            required
            autocomplete="name"
            autofocus
        >
        <i class="fas fa-user input-icon"></i>
        @error('name')
            <div class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>

    <div class="form-group">
        <input 
            id="email"
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="Email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
        >
        <i class="fas fa-envelope input-icon"></i>
        @error('email')
            <div class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>

    <div class="form-group">
        <input 
            id="password"
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="Password"
            required
            autocomplete="new-password"
        >
        <i class="fas fa-lock input-icon"></i>
        @error('password')
            <div class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>

    <div class="form-group">
        <input 
            id="password-confirm"
            type="password"
            name="password_confirmation"
            class="form-control"
            placeholder="Confirm Password"
            required
            autocomplete="new-password"
        >
        <i class="fas fa-check-circle input-icon"></i>
    </div>

    <button type="submit" class="btn-login">
        {{ __('Register') }}
    </button>
</form>

<div class="register-section">
    <p class="register-text">
        {{ __('Already have an account?') }}
        @if (Route::has('login'))
            <a href="{{ route('login') }}" class="register-link">
                {{ __('Login') }}
            </a>
        @endif
    </p>
</div>
@endsection
