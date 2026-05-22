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
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            max-width: 950px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            min-height: 580px;
        }

        .login-image {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
        }

        .image-content {
            text-align: center;
            max-width: 100%;
        }

        .image-content img {
            width: 100%;
            height: auto;
            max-height: 260px;
            object-fit: contain;
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.2));
            margin-bottom: 24px;
        }

        .image-text {
            color: white;
        }

        .image-text h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 1.5px;
        }

        .image-text .clock {
            font-size: 20px;
            font-weight: 500;
            opacity: 0.95;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
        }

        .login-form {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 14px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .invalid-feedback {
            display: block;
            color: #ef4444;
            font-size: 12px;
            margin-top: 6px;
            font-weight: 500;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
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

        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 420px;
            }

            .login-image {
                padding: 35px 30px;
            }

            .image-content img {
                max-height: 150px;
                margin-bottom: 18px;
            }

            .image-text h2 {
                font-size: 24px;
            }

            .image-text .clock {
                font-size: 18px;
            }

            .login-form {
                padding: 40px 30px;
            }

            .login-header h1 {
                font-size: 22px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-form {
                padding: 35px 25px;
            }

            .login-header h1 {
                font-size: 20px;
            }

            .form-control {
                padding: 11px 12px;
                font-size: 13px;
            }

            .btn-login {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-image">
            <div class="image-content">
                <img src="{{ asset('images/jenius-main.png') }}" alt="Jenius">
                <div class="image-text">
                    <h2>JENIUS APP</h2>
                    <div class="clock" id="clock"></div>
                </div>
            </div>
        </div>

        <div class="login-form">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Please login to your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="employee_id_number">Employee ID</label>
                    <input id="employee_id_number" type="text"
                        class="form-control @error('employee_id_number') is-invalid @enderror"
                        name="employee_id_number"
                        value="{{ old('employee_id_number') }}"
                        required
                        autofocus
                        placeholder="Enter your Employee ID">

                    @error('employee_id_number')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
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

                <button type="submit" class="btn-login" id="loginBtn">
                    <span>Login</span>
                    <div class="spinner"></div>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Clock function
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        // Update clock every second
        updateClock();
        setInterval(updateClock, 1000);

        // Login form handling
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</body>

</html>