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
        // Prepare statement to select authority by email
        $stmt = $conn->prepare("SELECT authority_id, full_name, email, password, role, is_active FROM authorities WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($authority_id, $full_name, $dbEmail, $hashedPassword, $role, $is_active);
            $stmt->fetch();
            
            // Check if account is active
            if (!$is_active) {
                $error = "Your account has been deactivated. Please contact the administrator.";
            } elseif (password_verify($password, $hashedPassword)) {
                // Set session variables
                $_SESSION['authority_id'] = $authority_id;
                $_SESSION['authority_name'] = $full_name;
                $_SESSION['authority_email'] = $dbEmail;
                $_SESSION['authority_role'] = $role;
                $_SESSION['user_type'] = 'authority';
                
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
    <title>Authority Login - CrashPoint</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
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
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card">
            <div class="card-header text-center">
                <div class="icon-wrapper">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="mb-0">Authority Login</h3>
                <p class="mb-0 small">Law Enforcement Access Only</p>
            </div>
            <div class="card-body p-4">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> Authority accounts are created by administrators only. If you need an account, please contact your system administrator.
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Official Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Enter your official email" required>
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
                    <p class="mb-2"><a href="#"><i class="fas fa-key"></i> Forgot Password?</a></p>
                    <p class="mb-2"><small class="text-muted">Need an account? Contact your administrator</small></p>
                    <hr>
                    <p class="mb-0">
                        <a href="../drivers/login.php" class="text-muted"><i class="fas fa-user"></i> Driver Login</a> | 
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