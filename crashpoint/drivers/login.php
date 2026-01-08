<?php
session_start();

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/crashpoint/includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Prepare statement to select driver by email
        $stmt = $conn->prepare("SELECT driver_id, full_name, email, password, is_active FROM drivers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($driver_id, $full_name, $dbEmail, $hashedPassword, $is_active);
            $stmt->fetch();
            
            // Check if account is active
            if (!$is_active) {
                $error = "Your account has been deactivated. Please contact support.";
            } elseif (password_verify($password, $hashedPassword)) {
                // Set session variables
                $_SESSION['driver_id'] = $driver_id;
                $_SESSION['driver_name'] = $full_name;
                $_SESSION['driver_email'] = $dbEmail;
                $_SESSION['user_type'] = 'driver';
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - CrashPoint</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0px 10px 40px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: #fff;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card">
            <div class="card-header text-center">
                <div class="icon-wrapper">
                    <i class="fas fa-car"></i>
                </div>
                <h3 class="mb-0">Driver Login</h3>
                <p class="mb-0 small">Access your CrashPoint account</p>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-custom btn-block btn-lg mt-4">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="mb-2">Don't have an account? <a href="register.php"><strong>Register here</strong></a></p>
                    <p class="mb-2"><a href="#"><i class="fas fa-key"></i> Forgot Password?</a></p>
                    <hr>
                    <p class="mb-0">
                        <a href="../authorities/login.php" class="text-muted"><i class="fas fa-shield-alt"></i> Authority Login</a> | 
                        <a href="../index.html" class="text-muted"><i class="fas fa-home"></i> Home</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>