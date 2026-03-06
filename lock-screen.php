<?php
include 'dbConnect.php';
session_start();
if(!isset($_SESSION['email'])){
    header("Location:login.php");
}
$email = $_SESSION['email'];
$userName = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Lock Screen | TLS CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="TLS Content Management System Lock Screen" name="description" />
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
            animation: move 25s infinite alternate;
        }

        .bg-circle-1 { width: 450px; height: 450px; top: -100px; left: -100px; }
        .bg-circle-2 { width: 350px; height: 350px; bottom: -80px; right: -80px; animation-delay: -10s; }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1.1); }
            100% { transform: translate(40px, 40px) scale(1); }
        }

        .lock-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            z-index: 10;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 28px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            padding: 40px;
            text-align: center;
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes bounceIn {
            from { opacity: 0; transform: scale(0.85); }
            to { opacity: 1; transform: scale(1); }
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            padding: 4px;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 0 auto 20px;
            border: 2px solid #E62B1E;
        }

        .user-name {
            font-size: 22px;
            font-weight: 700;
            color: var(--tls-primary);
            margin-bottom: 5px;
        }

        .lock-status {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 30px;
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
            text-align: center;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--tls-accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(230, 43, 30, 0.1);
        }

        .btn-unlock {
            background: var(--tls-primary);
            color: white;
            padding: 14px;
            border-radius: 14px;
            font-weight: 700;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            font-size: 16px;
        }

        .btn-unlock:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .return-link {
            display: block;
            margin-top: 25px;
            font-size: 14px;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .return-link:hover {
            color: var(--tls-accent);
        }

        .return-link b {
            color: var(--tls-primary);
        }
    </style>
</head>

<body>
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="lock-container">
        <div class="glass-card">
            <img src="assets/images/user.png" alt="user-image" class="user-avatar">
            
            <h3 class="user-name"><?php echo htmlspecialchars($userName); ?></h3>
            <p class="lock-status">Enter your password to resume</p>

            <form action="log.php" method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                
                <div class="input-group-custom">
                    <input type="password" name="password" class="form-control-custom" placeholder="••••••••" required autofocus>
                </div>

                <button type="submit" name="submit" class="btn-unlock">
                    Unlock Account
                </button>
            </form>

            <a href="login.php" class="return-link">
                Not you? <b>Sign in with another account</b>
            </a>
        </div>
    </div>

    <!-- Vendor -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>