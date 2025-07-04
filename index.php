<?php
include './includes/auth.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include './config/db.php';
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM employees WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch();

    if ($result && verifyPassword($password, $result["password"])) {
        $_SESSION["employee_id"] = $result["id"];
        header("location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrewSync | Professional Crew Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a4a, #2c3e50);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }
        
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(to bottom right, #1a56db, #3498db);
            padding: 50px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .brand {
            margin-bottom: 40px;
        }
        
        .brand h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .brand h1 i {
            background: rgba(255, 255, 255, 0.15);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .brand p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 400px;
        }
        
        .features {
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .feature-icon {
            background: rgba(255, 255, 255, 0.15);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .feature-text h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .feature-text p {
            font-size: 0.95rem;
            opacity: 0.85;
        }
        
        .login-right {
            flex: 1;
            padding: 50px 40px;
            background: white;
        }
        
        .login-header {
            margin-bottom: 40px;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            color: #1a2a4a;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #666;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert.error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .alert i {
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 20px 14px 45px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input {
            accent-color: #3498db;
        }
        
        .forgot-password {
            color: #3498db;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .forgot-password:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
        
        button {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            background: #1a56db;
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        
        button:hover {
            background: #1648b8;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .copyright {
            margin-top: 15px;
            color: #888;
        }
         .register-link {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 0.95rem;
        }
        
        .register-link a {
            color: #1a56db;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        .register-link a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-left {
                padding: 30px 25px;
            }
            
            .login-right {
                padding: 40px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="brand">
                <h1><i class="fas fa-users"></i> CrewSync</h1>
                <p>Professional Crew Management Platform</p>
            </div>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Efficient Scheduling</h3>
                        <p>Manage crew assignments and schedules with precision</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Performance Analytics</h3>
                        <p>Track crew performance and optimize operations</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Secure Access</h3>
                        <p>Enterprise-grade security for all your crew data</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-header">
                <h2>Employee Sign In</h2>
                <p>Access your crew management dashboard</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="register-link">
                <p>Don't have an account? <a href="registration.php">Register Now</a></p>
            </div>
            
            <div class="footer">
                <p>Need help? Contact support at support@crewsync.com</p>
                <div class="copyright">
                    &copy; <?= date('Y') ?> CrewSync Management System
                </div>
            </div>
        </div>
    </div>
</body>
</html>