<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Login') }}</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f4f5f7 0%, #f5f4f4 50%, #eceef2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Background decorative elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(128, 91, 214, 0.3) 0%, transparent 70%);
            border-radius: 20%;
            z-index: 1;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 60%;
            height: 100%;
            background: radial-gradient(ellipse, rgb(252, 253, 255) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 50px;
            padding: 10px;
            width: 100%;
            max-width: 500px;
            box-shadow:
                0 32px 64px rgba(0, 0, 0, 0.15),
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
        }

        .home-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }

        .home-icon i {
            font-size: 48px;
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .auth-header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1F2937;
            margin: 0;
        }

        .auth-subtext {
            text-align: center;
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 32px;
        }

      /* Center the form on the page */


            /* Form group styling */
            .form-group {
                position: relative;
                width: 100%;
                max-width: 400px;
                margin: 20px auto;
            }

            /* Input field styling */
            .form-control {
                width: 100%;
                padding: 16px 50px 16px 50px; /* space for icon on both sides */
                font-size: 16px;
                border: 2px solid #E5E7EB;
                border-radius: 25px;
                background: rgba(249, 250, 251, 0.8);
                backdrop-filter: blur(8px);
                transition: all 0.3s ease;
                font-family: 'Inter', sans-serif;
            }

            /* Focus state */
            .form-control:focus {
                outline: none;
                border-color: #3B82F6;
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            }

            /* Placeholder styling */
            .form-control::placeholder {
                color: #9CA3AF;
                font-weight: 400;
            }

            /* Icon styling inside input */
            .form-group .input-icon {
                position: absolute;
                top: 50%;
                left: 18px;
                transform: translateY(-50%);
                font-size: 18px;
                color: #9CA3AF;
                pointer-events: none;
            }

            /* Optional: right-side icon (e.g., eye toggle) */
            .form-group .input-icon-right {
                position: absolute;
                top: 50%;
                right: 18px;
                transform: translateY(-50%);
                font-size: 18px;
                color: #9CA3AF;
                cursor: pointer;
            }

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 18px;
            pointer-events: none;
        }

        .password-toggle {
            pointer-events: all;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #6B7280;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6B7280;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #3B82F6;
        }

        .forgot-password {
            color: #6B7280;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-password:hover {
            color: #3B82F6;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            border: none;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563EB 0%, #1E40AF 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 32px 0 24px;
            position: relative;
            color: #9CA3AF;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #E5E7EB;
            z-index: -1;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 16px;
        }

        .social-login {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .social-btn {
            width: 56px;
            height: 56px;
            border: 2px solid #E5E7EB;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: #D1D5DB;
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .social-btn i {
            font-size: 22px;
        }

        .social-btn.apple i {
            color: #000;
        }

        .social-btn.google i {
            background: linear-gradient(45deg, #EA4335, #FBBC05, #34A853, #4285F4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .social-btn.facebook i {
            color: #1877F2;
        }

        @media (max-width: 768px) {
            .auth-container {
                padding: 32px 24px;
                margin: 20px;
                border-radius: 24px;
            }

            .auth-header h1 {
                font-size: 28px;
            }

            body::before,
            body::after {
                opacity: 0.5;
            }
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 24px 20px;
            }

            .form-control {
                padding: 14px 16px;
                padding-right: 46px;
            }

            .btn-login {
                padding: 14px;
            }

            .social-btn {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="home-icon">
            <i class="fas fa-home"></i>
        </div>
        
        <div class="auth-header">
            <h1>iTrack Clocking</h1>
        </div>
        
        <p class="auth-subtext">Please enter your details.</p>

        @yield('content')

        <div class="divider">
            <span>or</span>
        </div>

        <div class="social-login">
            <a href="#" class="social-btn apple">
                <i class="fab fa-apple"></i>
            </a>
            <a href="#" class="social-btn google">
                <i class="fab fa-google"></i>
            </a>
            <a href="#" class="social-btn facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.querySelector('.password-toggle');
            const passwordInput = document.querySelector('input[type="password"]');
            
            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const icon = passwordToggle.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html>

