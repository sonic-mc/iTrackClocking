<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>iTrack Clocking - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Reset */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        :root {
            --primary-blue: #1e40af;
            --primary-light: #3b82f6;
            --accent-blue: #6366f1;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --text-light: #94a3b8;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --border-light: #e2e8f0;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Main container */
        .auth-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left side - Animation */
        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0f9ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        /* Animation container */
        .animation-container {
            width: 100%;
            max-width: 600px;
            height: 400px;
            position: relative;
            z-index: 2;
        }

        /* Lottie animation styles */
        #lottie-animation {
            width: 100%;
            height: 100%;
        }

        /* Right side - Form */
        .auth-right {
            flex: 1;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            box-shadow: -10px 0 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Form container */
        .auth-form-container {
            width: 100%;
            max-width: 400px;
        }

        /* Logo/Brand */
        .auth-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

        .brand-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Form title */
        .form-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-height: 1.2;
        }

        /* Form subtitle */
        .form-subtitle {
            color: var(--text-gray);
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .form-subtitle a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .form-subtitle a:hover {
            text-decoration: underline;
        }

        /* Form styles */
        .auth-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: 12px;
            font-size: 16px;
            background: var(--white);
            transition: all 0.2s ease;
            outline: none;
            color: var(--text-dark);
        }

        .form-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-light);
        }

        /* Password field with show/hide */
        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            padding: 4px;
        }

        .password-toggle:hover {
            color: var(--primary-blue);
        }

        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-blue);
        }

        .checkbox-group label {
            color: var(--text-gray);
            font-size: 14px;
            cursor: pointer;
            line-height: 1.4;
        }

        /* Submit button */
        .submit-btn {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            background: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }

        .submit-btn::after {
            content: '→';
            font-size: 18px;
            margin-left: 8px;
            transition: transform 0.2s ease;
        }

        .submit-btn:hover::after {
            transform: translateX(4px);
        }

        /* Links */
        .auth-links {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: center;
            margin-top: 24px;
        }

        .auth-links a {
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-links a:hover {
            color: var(--accent-blue);
            text-decoration: underline;
        }

        .signin-link {
            color: var(--text-gray);
            font-size: 14px;
            text-align: center;
            margin-top: 32px;
        }

        .signin-link a {
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        /* Error messages */
        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 8px;
            padding: 8px 12px;
            background: #fef2f2;
            border-radius: 8px;
            border-left: 3px solid #ef4444;
        }

        /* Success messages */
        .success-message {
            color: #10b981;
            font-size: 13px;
            margin-top: 8px;
            padding: 8px 12px;
            background: #ecfdf5;
            border-radius: 8px;
            border-left: 3px solid #10b981;
        }

        /* Loading state */
        .loading {
            position: relative;
            color: transparent;
        }

        .loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading::after {
            content: '';
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Floating elements */
        .floating-element {
            position: absolute;
            opacity: 0.6;
            animation: float 4s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            top: 20%;
            left: 20%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 60%;
            right: 20%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 30%;
            animation-delay: 1s;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .auth-wrapper {
                flex-direction: column;
            }

            .auth-left {
                min-height: 300px;
                padding: 40px 20px;
            }

            .auth-right {
                padding: 40px 20px;
                box-shadow: none;
            }

            .animation-container {
                height: 250px;
            }
        }

        @media (max-width: 640px) {
            .auth-left {
                min-height: 200px;
                padding: 20px;
            }

            .auth-right {
                padding: 30px 20px;
            }

            .form-title {
                font-size: 28px;
            }

            .animation-container {
                height: 180px;
            }
        }

        /* Form animations */
        .form-group {
            opacity: 0;
            animation: slideUp 0.6s ease-out forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus states for accessibility */
        .form-input:focus,
        .submit-btn:focus,
        .checkbox-group input:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- Left side with animation -->
        <div class="auth-left">
            <!-- Floating decorative elements -->
            <div class="floating-element" style="width: 60px; height: 60px; background: linear-gradient(45deg, #3b82f6, #8b5cf6); border-radius: 50%; opacity: 0.2;"></div>
            <div class="floating-element" style="width: 40px; height: 40px; background: linear-gradient(45deg, #06b6d4, #3b82f6); border-radius: 30%; opacity: 0.3;"></div>
            <div class="floating-element" style="width: 80px; height: 80px; background: linear-gradient(45deg, #8b5cf6, #ec4899); border-radius: 40%; opacity: 0.2;"></div>

            <!-- Animation container -->
            <div class="animation-container">
                <div id="lottie-animation"></div>
            </div>
        </div>

        <!-- Right side with form -->
        <div class="auth-right">
            <div class="auth-form-container">
                <!-- Brand -->
                <div class="auth-brand">
                    <div class="brand-icon">⏰</div>
                    <div class="brand-text">iTrack</div>
                </div>

                <!-- Form title -->
                <h1 class="form-title">Sign up</h1>
                <p class="form-subtitle">
                    Already have an account? 
                    <a href="{{ route('login') }}">Sign in</a>
                </p>

                @yield('content')

                <!-- Additional links -->
                <div class="auth-links">
                    <a href="#help">Need help?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lottie Animation Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lottie animation
            const animation = lottie.loadAnimation({
                container: document.getElementById('lottie-animation'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                // Using a professional business/office animation from LottieFiles
                path: 'https://lottie.host/4f7c5c7c-7e6e-4c6f-9f4d-8b5c5c5c5c5c/4f7c5c7c7e6e.json'
            });

            // Fallback if Lottie fails to load
            animation.addEventListener('error', function() {
                createFallbackAnimation();
            });

            // Alternative: Create CSS-based animation if Lottie fails
            function createFallbackAnimation() {
                const container = document.getElementById('lottie-animation');
                container.innerHTML = `
                    <div style="
                        width: 100%;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-direction: column;
                        gap: 20px;
                    ">
                        <div style="
                            width: 200px;
                            height: 200px;
                            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            animation: pulse 2s ease-in-out infinite;
                        ">
                            <div style="
                                width: 120px;
                                height: 120px;
                                background: white;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 48px;
                            ">⏰</div>
                        </div>
                        <div style="
                            text-align: center;
                            color: #64748b;
                            font-size: 18px;
                            font-weight: 500;
                        ">
                            Track your time efficiently
                        </div>
                    </div>
                    <style>
                        @keyframes pulse {
                            0%, 100% { transform: scale(1); }
                            50% { transform: scale(1.05); }
                        }
                    </style>
                `;
            }

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
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        
                        // Re-enable if form submission fails (fallback)
                        setTimeout(() => {
                            submitBtn.classList.remove('loading');
                            submitBtn.disabled = false;
                        }, 5000);
                    }
                });
            });

            // Form validation enhancement
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.style.borderColor = '#ef4444';
                    } else if (this.type === 'email' && this.value && !isValidEmail(this.value)) {
                        this.style.borderColor = '#ef4444';
                    } else {
                        this.style.borderColor = '#10b981';
                    }
                });

                input.addEventListener('focus', function() {
                    this.style.borderColor = '#3b82f6';
                });
            });

            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
        });
    </script>
</body>
</html>