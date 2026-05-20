<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Jenius</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(34, 197, 94, 0.15);
            min-height: 600px;
        }

        .login-image {
            flex: 1;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-image::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .login-image::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }

        .login-image img {
            max-width: 100%;
            height: auto;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.2));
        }

        .login-form {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 16px;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .invalid-feedback {
            display: block;
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            font-weight: 500;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
            accent-color: #10b981;
        }

        .form-check-label {
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #10b981;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #059669;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-image {
                padding: 40px;
                min-height: 250px;
            }

            .login-form {
                padding: 40px 30px;
            }

            .login-header h1 {
                font-size: 26px;
            }
        }

        /* Loading spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-login.loading .spinner {
            display: block;
        }

        .btn-login.loading span {
            display: none;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Image -->
        <div class="login-image">
            <img src="{{ asset('images/jenius-main.png') }}" alt="Jenius">
        </div>

        <!-- Right Side - Form -->
        <div class="login-form">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Please login to your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                            placeholder="Enter your email">

                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password">

                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span>Login</span>
                    <div class="spinner"></div>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Add loading state to button on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // Add focus effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>