<?php
session_start();

// Prevent caching to stop back button access after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once $_SERVER['DOCUMENT_ROOT'] . '/crashpoint/includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['driver_id']) || $_SESSION['user_type'] !== 'driver') {
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

$driver_name = $_SESSION['driver_name'];
$driver_id = $_SESSION['driver_id'];
?>

<!DOCTYPE html>
<html lang="en">  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - CrashPoint</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f4f7fa; }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
        }
        .stat-label {
            color: #6c757d;
            font-size: 16px;
            margin-top: 10px;
        }
        
        /* Emergency Report Button Styles */
        .emergency-report-section {
            margin: 40px 0;
            text-align: center;
        }
        
        .emergency-report-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 15px;
            padding: 40px 60px;
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: inline-block;
            text-decoration: none;
        }
        
        .emergency-report-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(220, 53, 69, 0.6);
            color: white;
            text-decoration: none;
        }
        
        .emergency-report-btn:active {
            transform: scale(0.98);
        }
        
        .emergency-report-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .emergency-report-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .emergency-report-btn .btn-content {
            position: relative;
            z-index: 1;
        }
        
        .emergency-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .emergency-subtitle {
            font-size: 14px;
            margin-top: 15px;
            color: #6c757d;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">🚗 CrashPoint</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">My Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="report_template.php">Submit Report</a>
                    </li>
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
                <h2>Welcome back, <?php echo htmlspecialchars($driver_name); ?>! 👋</h2>
                <p class="text-muted">Here's an overview of your crash reporting activity.</p>
            </div>
        </div>

        <!-- EMERGENCY REPORT BUTTON -->
        <div class="emergency-report-section">
            <a href="report_template.php" class="emergency-report-btn">
                <div class="btn-content">
                    <span class="emergency-icon">🚨</span>
                    Report Crash/Incident
                </div>
            </a>
            <p class="emergency-subtitle">Click here to immediately report a road accident or incident</p>
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
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Under Investigation</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Reports</h5>
                        <div class="alert alert-info">
                            No reports submitted yet. Click the red button above to file your first crash report.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-5">
                        <h3>📋 Submit New Report</h3>
                        <p class="text-muted">Report a road incident or crash</p>
                        <a href="report_template.php" class="btn btn-primary btn-lg">Submit Report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-5">
                        <h3>📊 View My Reports</h3>
                        <p class="text-muted">Track your submitted reports</p>
                        <a href="#" class="btn btn-outline-primary btn-lg">View Reports</a>
                    </div>
                </div>
            </div>
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
