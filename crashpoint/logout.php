<?php
session_start();

// Determine user type and redirect accordingly
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Redirect based on user type
if ($user_type === 'authority') {
    header("Location: /crashpoint/authorities/login.php");
} elseif ($user_type === 'driver') {
    header("Location: /crashpoint/drivers/login.php");
} else {
    header("Location: /crashpoint/index.html");
}
exit();
?>