<?php
session_start();

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/crashpoint/includes/db.php';

$success = "";
$error = "";

if (isset($_POST['register'])) {
    // Retrieve and trim form data
    $full_name      = trim($_POST['full_name']);
    $email          = trim($_POST['email']);
    $phone          = trim($_POST['phone']);
    $license_number = trim($_POST['license_number']);
    $password       = trim($_POST['password']);
    $confirm_pass   = trim($_POST['confirm_pass']);

    // Server-side validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($license_number) || empty($password) || empty($confirm_pass)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm_pass) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Check if email or license already exists
        $checkStmt = $conn->prepare("SELECT driver_id FROM drivers WHERE email = ? OR license_number = ?");
        $checkStmt->bind_param("ss", $email, $license_number);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $error = "Email or License Number already exists.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO drivers (full_name, email, phone, license_number, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $full_name, $email, $phone, $license_number, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Registration successful. Redirecting to login...";
            } else {
                $error = handleDatabaseError($stmt->error);
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}

// Connection will be closed automatically at end of script
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration - CrashPoint</title>
    <?php
    if (!empty($success)) {
        echo '<meta http-equiv="refresh" content="3;url=login.php">';
    }
    ?>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 30px 0;
        }
        .register-container {
            width: 100%;
            max-width: 600px;
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
        .required { color: red; }
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container register-container">
        <div class="card">
            <div class="card-header text-center">
                <h3 class="mb-0"><i class="fas fa-user-plus"></i> Driver Registration</h3>
                <p class="mb-0 small">Register to report road incidents</p>
            </div>
            <div class="card-body p-4">
                <?php
                if (!empty($error)) {
                    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> {$error}</div>";
                }
                if (!empty($success)) {
                    echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> {$success}</div>";
                }
                ?>
                
                <form id="registerForm" method="POST" action="register.php" novalidate>
                    <div class="form-group">
                        <label for="full_name">Full Name: <span class="required">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               placeholder="Enter your full name"
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        <span id="fullNameError" class="error"></span>
                    </div>

                    <div class="form-row"> 
                        <div class="form-group col-md-6">
                            <label for="email">Email: <span class="required">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="your@email.com"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <span id="emailError" class="error"></span>
                        </div>
                        <div class="form-group col-md-6">       
                            <label for="phone">Phone Number: <span class="required">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   placeholder="+254700000000"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            <span id="phoneError" class="error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="license_number">Driver's License Number: <span class="required">*</span></label>
                        <input type="text" class="form-control" id="license_number" name="license_number" 
                               placeholder="e.g., DL123456789"
                               value="<?php echo isset($_POST['license_number']) ? htmlspecialchars($_POST['license_number']) : ''; ?>">
                        <span id="licenseError" class="error"></span>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password: <span class="required">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Min. 8 characters">
                            <span id="passwordError" class="error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="confirm_pass">Confirm Password: <span class="required">*</span></label>
                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" 
                                   placeholder="Re-enter password">
                            <span id="confirmPasswordError" class="error"></span>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn btn-custom btn-block btn-lg mt-3">
                        <i class="fas fa-user-plus"></i> Register Account
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p>Already have an account? <a href="login.php"><strong>Login here</strong></a></p>
                    <a href="../index.html" class="text-muted"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            // Clear previous errors
            document.getElementById('fullNameError').innerText = '';
            document.getElementById('emailError').innerText = '';
            document.getElementById('phoneError').innerText = '';
            document.getElementById('licenseError').innerText = '';
            document.getElementById('passwordError').innerText = '';
            document.getElementById('confirmPasswordError').innerText = '';

            let valid = true;
            let full_name = document.getElementById('full_name').value.trim();
            let email = document.getElementById('email').value.trim();
            let phone = document.getElementById('phone').value.trim();
            let license_number = document.getElementById('license_number').value.trim();
            let password = document.getElementById('password').value.trim();
            let confirm_pass = document.getElementById('confirm_pass').value.trim();

            // Validate full name
            if (full_name === '') {
                document.getElementById('fullNameError').innerText = 'Full name is required.';
                valid = false;
            }

            // Validate email
            if (email === '') {
                document.getElementById('emailError').innerText = 'Email is required.';
                valid = false;
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    document.getElementById('emailError').innerText = 'Enter a valid email address.';
                    valid = false;
                }
            }

            // Validate phone
            if (phone === '') {
                document.getElementById('phoneError').innerText = 'Phone number is required.';
                valid = false;
            }

            // Validate license
            if (license_number === '') {
                document.getElementById('licenseError').innerText = 'License number is required.';
                valid = false;
            }

            // Validate password
            if (password === '') {
                document.getElementById('passwordError').innerText = 'Password is required.';
                valid = false;
            } else if (password.length < 8) {
                document.getElementById('passwordError').innerText = 'Password must be at least 8 characters.';
                valid = false;
            }

            // Validate confirm password
            if (confirm_pass === '') {
                document.getElementById('confirmPasswordError').innerText = 'Please confirm your password.';
                valid = false;
            } else if (password !== confirm_pass) {
                document.getElementById('confirmPasswordError').innerText = 'Passwords do not match.';
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>