<?php
/**
 * CrashPoint Setup Checker
 * Place this file in your crashpoint root folder
 * Visit: http://localhost/crashpoint/test_setup.php
 * This will verify your setup is working correctly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [];
$allGood = true;

// Test 1: Check if database connection file exists
$results[] = [
    'test' => 'Database File Exists',
    'status' => file_exists('includes/db.php'),
    'message' => file_exists('includes/db.php') ? 'includes/db.php found' : 'includes/db.php NOT found'
];

// Test 2: Try to connect to database
try {
    require_once 'includes/db.php';
    $results[] = [
        'test' => 'Database Connection',
        'status' => isset($conn) && $conn->ping(),
        'message' => isset($conn) && $conn->ping() ? 'Connected to database successfully' : 'Cannot connect to database'
    ];
    
    if (isset($conn) && $conn->ping()) {
        // Test 3: Check if tables exist
        $tables = ['drivers', 'authorities', 'crash_reports', 'report_updates', 'notifications'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            $exists = $result && $result->num_rows > 0;
            $results[] = [
                'test' => "Table: $table",
                'status' => $exists,
                'message' => $exists ? "Table '$table' exists" : "Table '$table' NOT found"
            ];
            if (!$exists) $allGood = false;
        }
        
        // Test 4: Check if default admin exists
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM authorities WHERE email = 'admin@crashpoint.co.ke'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $adminExists = $row['count'] > 0;
        $results[] = [
            'test' => 'Default Admin Account',
            'status' => $adminExists,
            'message' => $adminExists ? 'Default admin account exists' : 'Default admin account NOT found'
        ];
        
        // Test 5: Check record counts
        $tables_to_count = ['drivers', 'authorities', 'crash_reports'];
        foreach ($tables_to_count as $table) {
            $result = $conn->query("SELECT COUNT(*) as count FROM $table");
            if ($result) {
                $row = $result->fetch_assoc();
                $results[] = [
                    'test' => "Records in $table",
                    'status' => true,
                    'message' => "{$row['count']} record(s) found"
                ];
            }
        }
    }
    
} catch (Exception $e) {
    $results[] = [
        'test' => 'Database Connection',
        'status' => false,
        'message' => 'Error: ' . $e->getMessage()
    ];
    $allGood = false;
}

// Test 6: Check if required folders exist
$folders = ['drivers', 'authorities', 'includes', 'assets'];
foreach ($folders as $folder) {
    $exists = is_dir($folder);
    $results[] = [
        'test' => "Folder: $folder",
        'status' => $exists,
        'message' => $exists ? "Folder '$folder' exists" : "Folder '$folder' NOT found"
    ];
    if (!$exists) $allGood = false;
}

// Test 7: Check if key files exist
$files = [
    'drivers/login.php',
    'drivers/register.php',
    'drivers/dashboard.php',
    'authorities/login.php',
    'authorities/register.php',
    'authorities/dashboard.php'
];
foreach ($files as $file) {
    $exists = file_exists($file);
    $results[] = [
        'test' => "File: $file",
        'status' => $exists,
        'message' => $exists ? "File exists" : "File NOT found"
    ];
    if (!$exists) $allGood = false;
}

// Test 8: Check PHP version
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.0.0', '>=');
$results[] = [
    'test' => 'PHP Version',
    'status' => $phpOk,
    'message' => "PHP version: $phpVersion " . ($phpOk ? '(OK)' : '(Needs 7.0+)')
];

// Test 9: Check if sessions work
session_start();
$_SESSION['test'] = 'working';
$sessionWorks = isset($_SESSION['test']) && $_SESSION['test'] === 'working';
$results[] = [
    'test' => 'PHP Sessions',
    'status' => $sessionWorks,
    'message' => $sessionWorks ? 'Sessions working' : 'Sessions NOT working'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrashPoint Setup Checker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .checker-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .status-pass {
            color: #28a745;
            font-weight: bold;
        }
        .status-fail {
            color: #dc3545;
            font-weight: bold;
        }
        .test-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-item:last-child {
            border-bottom: none;
        }
        .overall-status {
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .overall-pass {
            background: #d4edda;
            color: #155724;
        }
        .overall-fail {
            background: #f8d7da;
            color: #721c24;
        }
        .icon-pass::before {
            content: "✓ ";
        }
        .icon-fail::before {
            content: "✗ ";
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="checker-container">
            <h1 class="text-center mb-4">🔍 CrashPoint Setup Checker</h1>
            
            <div class="overall-status <?php echo $allGood ? 'overall-pass' : 'overall-fail'; ?>">
                <?php if ($allGood): ?>
                    ✓ All Systems Ready!
                <?php else: ?>
                    ⚠ Some Issues Found
                <?php endif; ?>
            </div>
            
            <div class="test-results">
                <?php foreach ($results as $result): ?>
                    <div class="test-item">
                        <div>
                            <strong><?php echo htmlspecialchars($result['test']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($result['message']); ?></small>
                        </div>
                        <div class="<?php echo $result['status'] ? 'status-pass icon-pass' : 'status-fail icon-fail'; ?>">
                            <?php echo $result['status'] ? 'PASS' : 'FAIL'; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <hr class="my-4">
            
            <div class="alert alert-info">
                <h5>📋 Quick Links:</h5>
                <ul class="mb-0">
                    <li><a href="drivers/register.php">Driver Registration</a></li>
                    <li><a href="drivers/login.php">Driver Login</a></li>
                    <li><a href="authorities/register.php">Authority Registration</a></li>
                    <li><a href="authorities/login.php">Authority Login</a></li>
                </ul>
            </div>
            
            <div class="alert alert-warning">
                <h5>🔐 Default Login Credentials:</h5>
                <strong>Admin:</strong><br>
                Email: admin@crashpoint.co.ke<br>
                Password: Admin@123
            </div>
            
            <?php if (!$allGood): ?>
            <div class="alert alert-danger">
                <h5>⚠️ Troubleshooting:</h5>
                <ol>
                    <li>Make sure XAMPP Apache and MySQL are running</li>
                    <li>Run the SQL script in phpMyAdmin to create the database</li>
                    <li>Check that all files are in the correct folders</li>
                    <li>Verify database credentials in includes/db.php</li>
                    <li>Make sure your folder is named 'crashpoint' or update paths in files</li>
                </ol>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <button onclick="location.reload()" class="btn btn-primary">🔄 Re-run Tests</button>
                <a href="index.html" class="btn btn-secondary">🏠 Go to Home</a>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>


