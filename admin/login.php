<?php
require_once __DIR__ . '/../includes/functions.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $config = getConfig();
    $password = $_POST['password'];
    
    // Default password is "password" (hash provided in config)
    if (password_verify($password, $config['admin_password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid password';
    }
}

// If already logged in, redirect
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .login-box {
            background-color: var(--bg-secondary);
            padding: 3rem;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 60px var(--shadow-color);
            max-width: 450px;
            width: 90%;
            animation: fadeIn 0.5s ease;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header i {
            font-size: 4rem;
            color: var(--accent-primary);
            margin-bottom: 1rem;
        }
        
        .login-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--text-muted);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--bg-tertiary);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            transition: all var(--transition-speed) ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .error-message {
            background-color: #ef4444;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            animation: fadeIn 0.3s ease;
        }
        
        .login-footer {
            margin-top: 2rem;
            text-align: center;
        }
        
        .login-footer a {
            color: var(--accent-primary);
            text-decoration: none;
            transition: all var(--transition-speed) ease;
        }
        
        .login-footer a:hover {
            color: var(--accent-secondary);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-shield-alt"></i>
                <h1>Admin Panel</h1>
                <p>Enter your password to continue</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autofocus placeholder="Enter admin password">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../">
                    <i class="fas fa-arrow-left"></i> Back to Documentation
                </a>
                <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.875rem;">
                    Default password: <code>password</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
