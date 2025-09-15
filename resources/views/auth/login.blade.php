@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    
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
            autofocus
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
            autocomplete="current-password"
        >
        <i class="fas fa-eye password-toggle input-icon"></i>
        @error('password')
            <div class="invalid-feedback">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>

    <div class="form-options">
        <label class="remember-me">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            {{ __('Remember for 30 days') }}
        </label>
        
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-password">
                {{ __('Forgot password?') }}
            </a>
        @endif
    </div>

    <button type="submit" class="btn-login">
        {{ __('Login') }}
    </button>
</form>

<div class="register-section">
    <p class="register-text">
        {{ __("Don't have an account?") }}
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="register-link">
                {{ __('Sign up') }}
            </a>
        @endif
    </p>
</div>
@endsection