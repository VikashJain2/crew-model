<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM employees WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO employees (username, password) VALUES (?, ?)");
                if ($stmt->execute([$username, $hashed_password])) {
                    header("Location: index.php");
                    exit;
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
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
        
        .password-info {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        
        .btn {
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
        
        .btn:hover {
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
        
        .login-link {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: #1a56db;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        .login-link a:hover {
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
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Easy Registration</h3>
                        <p>Join our professional crew management system</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Secure Accounts</h3>
                        <p>Your information is protected with enterprise security</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Get Started Quickly</h3>
                        <p>Begin managing your professional profile immediately</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-header">
                <h2>Create Your Account</h2>
                <p>Join the CrewSync Platform</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registrationForm" novalidate>
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" 
                               placeholder="Enter your username" required
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" 
                               placeholder="Create a password (min 8 characters)" required>
                    </div>
                    <div class="password-info">Password must be at least 8 characters long</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="index.php">Sign In</a></p>
            </div>
            
            <div class="footer">
                <p>Need help? Contact support at support@crewsync.com</p>
                <div class="copyright">
                    &copy; <?= date('Y') ?> CrewSync Management System
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            form.addEventListener('submit', function(e) {
                let valid = true;

                // Reset border colors
                passwordInput.style.borderColor = '#ddd';
                confirmPasswordInput.style.borderColor = '#ddd';

                // Password length validation
                if (passwordInput.value.length < 8) {
                    passwordInput.style.borderColor = '#e74c3c';
                    valid = false;
                }

                // Password match validation
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.style.borderColor = '#e74c3c';
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });

            // Real-time feedback for password length
            passwordInput.addEventListener('input', function() {
                this.style.borderColor = this.value.length >= 8 ? '#2ecc71' : '#e74c3c';
            });

            // Real-time feedback for password match
            confirmPasswordInput.addEventListener('input', function() {
                this.style.borderColor = (this.value === passwordInput.value) ? '#2ecc71' : '#e74c3c';
            });
        });
    </script>
</body>
</html>