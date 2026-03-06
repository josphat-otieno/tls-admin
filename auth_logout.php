<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Logged Out | TLS CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="TLS Content Management System Logout" name="description" />
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
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.18);
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
            filter: blur(40px);
            animation: move 20s infinite alternate;
        }

        .bg-circle-1 { width: 400px; height: 400px; top: -100px; left: -100px; }
        .bg-circle-2 { width: 300px; height: 300px; bottom: -50px; right: -50px; animation-delay: -5s; }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 50px) scale(1.1); }
        }

        .logout-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
            padding: 40px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .logout-icon-wrapper {
            margin-bottom: 24px;
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .checkmark-circle i {
            font-size: 40px;
            color: #4caf50;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .logout-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--tls-primary);
            margin-bottom: 12px;
        }

        .logout-message {
            color: #64748b;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .btn-login {
            background: var(--tls-primary);
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
        }

        .btn-login:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            color: white;
        }

        .brand-logo {
            margin-bottom: 30px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .footer-links {
            margin-top: 24px;
            font-size: 14px;
            color: #94a3b8;
        }

        .footer-links a {
            color: var(--tls-accent);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="logout-container">
        <div class="brand-logo">
            <a href="index.php">
                <img src="assets/images/tls_logo.png" alt="TLS Logo" height="60">
            </a>
        </div>

        <div class="glass-card">
            <div class="logout-icon-wrapper">
                <div class="checkmark-circle">
                    <i class="mdi mdi-check"></i>
                </div>
            </div>

            <h3 class="logout-title">Successfully Logged Out</h3>
            <p class="logout-message">
                Thank you for using TLS CMS. You have been securely signed out of your account.
            </p>

            <a href="login.php" class="btn-login">
                Return to Sign In
            </a>
        </div>

        <div class="footer-links">
            <p>© <script>document.write(new Date().getFullYear())</script> TLS Advertising Agency. </p>
        </div>
    </div>

    <!-- Vendor -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>
</body>
</html>