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
            padding: 30px 20px;
        }

        .login-wrapper {
            max-width: 1300px;
            width: 100%;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 0;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            min-height: 650px;
        }

        .login-image {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 60px;
            position: relative;
        }

        .image-content {
            text-align: center;
            max-width: 550px;
        }

        .image-content img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: contain;
            filter: drop-shadow(0 15px 35px rgba(0, 0, 0, 0.25));
            margin-bottom: 30px;
        }

        .image-text {
            color: white;
        }

        .image-text h2 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: 2px;
        }

        .image-text .clock {
            font-size: 32px;
            font-weight: 500;
            opacity: 0.95;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .login-form {
            padding: 80px 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-header {
            margin-bottom: 45px;
        }

        .login-header h1 {
            font-size: 34px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 16px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 26px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
        }

        .form-control {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
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
            margin-top: 8px;
            font-weight: 500;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
        }

        .btn-login:active {
            transform: translateY(0);
        }

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

        @media (max-width: 1024px) {
            .login-wrapper {
                grid-template-columns: 1fr 1fr;
            }

            .login-image {
                padding: 60px 40px;
            }

            .image-content img {
                max-height: 400px;
            }

            .image-text h2 {
                font-size: 38px;
            }

            .image-text .clock {
                font-size: 26px;
            }

            .login-form {
                padding: 60px 50px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-image {
                padding: 50px 30px;
            }

            .image-content img {
                max-height: 280px;
                margin-bottom: 20px;
            }

            .image-text h2 {
                font-size: 32px;
            }

            .image-text .clock {
                font-size: 22px;
            }

            .login-form {
                padding: 50px 35px;
            }

            .login-header h1 {
                font-size: 26px;
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