<?php


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'crashpoint_db');

// Create MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // Log error (in production, log to file instead of displaying)
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Connection failed. Please contact the administrator.");
}

// Set charset to utf8mb4 for proper character handling
$conn->set_charset("utf8mb4");

// Optional: Set timezone (adjust to your timezone)
date_default_timezone_set('Africa/Nairobi');

// Function to close database connection (call this at end of scripts if needed)
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// Error handling function
function handleDatabaseError($error) {
    error_log("Database Error: " . $error);
    return "An error occurred. Please try again later.";
}

?>


