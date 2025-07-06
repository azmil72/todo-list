<?php
require_once '../controllers/AuthController.php';
$auth = new AuthController();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->handleLogin($_POST);
    if ($result['status']) {
        header('Location: ../public/index.php');
        exit;
    } else {
        $message = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ToDo App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
            animation: fadeInUp 0.6s ease-out;
            overflow: hidden;
            position: relative;
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0) 60%
            );
            transform: rotate(30deg);
            pointer-events: none;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo {
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .alert {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from { 
                opacity: 0; 
                transform: translateX(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }
        
        .alert-danger {
            background: rgba(247, 37, 133, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1.2rem 0.8rem 3rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 2.6rem;
            color: var(--gray);
        }
        
        .btn {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(67, 97, 238, 0.3);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Floating animation for decorative elements */
        .floating {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .floating:nth-child(1) {
            width: 100px;
            height: 100px;
            top: -30px;
            right: -30px;
            animation: float 8s ease-in-out infinite;
        }
        
        .floating:nth-child(2) {
            width: 60px;
            height: 60px;
            bottom: 20px;
            left: -20px;
            animation: float 6s ease-in-out infinite 2s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
        
        /* Responsive design */
        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }
            
            .login-logo {
                font-size: 2.5rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative floating elements -->
    <div class="floating"></div>
    <div class="floating"></div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-tasks"></i>
            </div>
            <h1 class="login-title">Masuk ke ToDo App</h1>
            <p class="login-subtitle">Kelola tugas Anda dengan mudah dan efisien</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $message ?></span>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>
        
        <div class="login-footer">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>

    <script>
        // Add animation to form inputs when focused
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--primary)';
                this.parentElement.querySelector('label').style.color = 'var(--primary)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--gray)';
                this.parentElement.querySelector('label').style.color = 'var(--dark)';
            });
        });
        
        // Add subtle animation to form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;
        });
    </script>
</body>
</html>