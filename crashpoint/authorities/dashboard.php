<?php
session_start();

// Prevent caching to stop back button access after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once $_SERVER['DOCUMENT_ROOT'] . '/crashpoint/includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['authority_id']) || $_SESSION['user_type'] !== 'authority') {
    header("Location: login.php");
    exit();
}

// Session timeout - 3 minutes (180 seconds)
$timeout_duration = 180;

// Check if last activity timestamp exists
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    // If more than 3 minutes have passed, destroy session and redirect to login
    if ($elapsed_time > $timeout_duration) {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: login.php");
        exit();
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

$authority_name = $_SESSION['authority_name'];
$authority_role = $_SESSION['authority_role'];
$authority_id = $_SESSION['authority_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authority Dashboard - CrashPoint</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f4f7fa; }
        .navbar-custom {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .dashboard-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            padding: 30px;
            text-align: center;
        }
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #f5576c;
        }
        .stat-label {
            color: #6c757d;
            font-size: 16px;
            margin-top: 10px;
        }
        .badge-role {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">👮 CrashPoint Authority</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">All Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Analytics</a>
                    </li>
                    <?php if ($authority_role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Manage Users</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Welcome Section -->
        <div class="row">
            <div class="col-12">
                <h2>Welcome back, <?php echo htmlspecialchars($authority_name); ?>! 👮</h2>
                <p class="text-muted">
                    Role: <span class="badge-role"><?php echo strtoupper($authority_role); ?></span>
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Total Reports</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Investigating</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Crash Reports</h5>
                        <div class="alert alert-info">
                            No reports available yet. Reports from drivers will appear here.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <h4>📋 All Reports</h4>
                        <p class="text-muted">View and manage crash reports</p>
                        <a href="#" class="btn btn-primary">View Reports</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <h4>📊 Analytics</h4>
                        <p class="text-muted">View crash statistics and trends</p>
                        <a href="#" class="btn btn-outline-primary">View Analytics</a>
                    </div>
                </div>
            </div>
            <?php if ($authority_role === 'admin'): ?>
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <h4>👥 Manage Users</h4>
                        <p class="text-muted">Manage drivers and authorities</p>
                        <a href="#" class="btn btn-outline-primary">Manage Users</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Auto logout after 3 minutes of inactivity
        let inactivityTime = function () {
            let time;
            
            // Reset timer on user activity
            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;
            document.onclick = resetTimer;
            document.onscroll = resetTimer;
            
            function logout() {
                // Redirect directly to login page
                window.location.href = 'login.php';
            }
            
            function resetTimer() {
                clearTimeout(time);
                // 3 minutes = 180000 milliseconds
                time = setTimeout(logout, 180000);
            }
        };
        
        // Initialize inactivity timer
        inactivityTime();
        
        // Prevent back button after logout
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.history.pushState(null, null, window.location.href);
        };
    </script>
</body>
</html>