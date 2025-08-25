<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>iTrack Clocking - @yield('title')</title>

    <style>
        /* Reset */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Auth container */
        .auth-container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            border: 1px solid #e0e0e0;
        }

        /* Login title */
        .login-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 8px;
            font-weight: 400;
        }

        /* Subtitle */
        .login-subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .login-subtitle a {
            color: #17a2b8;
            text-decoration: none;
        }

        .login-subtitle a:hover {
            text-decoration: underline;
        }

        /* Form styles */
        .auth-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 4px;
            font-size: 16px;
            background: white;
            transition: border-color 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #17a2b8;
        }

        /* Password field with show/hide */
        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #17a2b8;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .password-toggle:hover {
            text-decoration: underline;
        }

        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 20px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .checkbox-group label {
            color: #333;
            font-size: 14px;
            cursor: pointer;
        }

        /* Submit button */
        .submit-btn {
            background: #17a2b8;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 20px;
            align-self: flex-start;
        }

        .submit-btn:hover {
            background: #138496;
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Links */
        .auth-links {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .auth-links a {
            color: #17a2b8;
            text-decoration: none;
            font-size: 14px;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .help-link {
            display: block;
            color: #17a2b8;
            text-decoration: none;
            font-size: 14px;
        }

        .help-link:hover {
            text-decoration: underline;
        }

        /* Error messages */
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        /* Loading state */
        .loading {
            position: relative;
            color: transparent;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Mobile responsive */
        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
            }

            .login-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="login-title">Log in</h1>
        <p class="login-subtitle">
            Need a {{ config('app.name', 'iTrack') }} account? 
            <a href="{{ route('register') }}">Create an account</a>
        </p>

        @yield('content')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password show/hide toggle
            const passwordToggles = document.querySelectorAll('.password-toggle');
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = this.previousElementSibling;
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });
            });

            // Form submission loading state
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('.submit-btn, button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }
                });
            });
        });
    </script>
</body>
</html>