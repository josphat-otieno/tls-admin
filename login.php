<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Log In | TLS CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="TLS Content Management System Login" name="description" />
    <meta content="Usalama Technology" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
    
    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <!-- icons -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --tls-primary: #1a1a1a;
            --tls-accent: #E62B1E;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.2);
            --input-bg: rgba(248, 250, 252, 0.8);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* Abstract Background Elements */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(230, 43, 30, 0.1), rgba(230, 43, 30, 0.05));
            z-index: -1;
            filter: blur(60px);
            animation: move 20s infinite alternate;
        }

        .bg-circle-1 { width: 500px; height: 500px; top: -150px; left: -150px; }
        .bg-circle-2 { width: 400px; height: 400px; bottom: -100px; right: -100px; animation-delay: -7s; }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(60px, 60px) scale(1.15); }
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            z-index: 10;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 28px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            padding: 40px;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header h3 {
            font-size: 26px;
            font-weight: 800;
            color: var(--tls-primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #64748b;
            font-size: 15px;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 24px;
        }

        .form-control-custom {
            background: var(--input-bg);
            border: 1px solid #e2e8f0;
            padding: 14px 16px;
            border-radius: 14px;
            width: 100%;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--tls-accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(61, 90, 254, 0.1);
        }

        .btn-login {
            background: var(--tls-primary);
            color: white;
            padding: 14px;
            border-radius: 14px;
            font-weight: 700;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            font-size: 16px;
        }

        .btn-login:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            padding: 8px;
        }

        .password-toggle:hover {
            color: var(--tls-accent);
        }

        /* Error States (Standard color instead of generic red) */
        .error-message {
            background: #fff1f2;
            color: #e11d48;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #ffe4e6;
            display: none; /* Controlled via PHP/JS if needed */
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #94a3b8;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="login-container">
        <div class="brand-logo">
            <a href="index.php">
                <img src="assets/images/tls_logo.png" alt="TLS Logo" height="70">
            </a>
        </div>

        <div class="glass-card">
            <div class="login-header">
                <h3>Welcome Back</h3>
                <p>Enter your credentials to access the CMS</p>
            </div>

            <form action="log.php" method="post" id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group-custom">
                        <input type="email" name="email" id="email" class="form-control-custom" placeholder="name@tls.co.ke" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group-custom password-group">
                        <input type="password" name="password" id="password" class="form-control-custom" placeholder="••••••••" required>
                        <span class="password-toggle" id="togglePassword">
                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-login">
                    Sign In to Dashboard
                </button>
            </form>
        </div>

        <div class="footer-text">
            © <script>document.write(new Date().getFullYear())</script> TLS Advertising Agency. CMS
        </div>
    </div>

    <!-- Vendor -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password Visibility Toggle Logic
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleIcon = document.querySelector('#toggleIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle icons
            if (type === 'password') {
                toggleIcon.classList.remove('mdi-eye-off-outline');
                toggleIcon.classList.add('mdi-eye-outline');
            } else {
                toggleIcon.classList.remove('mdi-eye-outline');
                toggleIcon.classList.add('mdi-eye-off-outline');
            }
        });
    </script>
</body>
</html>