<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change if necessary
define('DB_PASS', ''); // Change if necessary
define('DB_NAME', 'event_management');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start session (Requirement 11: Session should work properly)
session_start();

// Function to sanitize input (Requirement 10: Validations on forms)
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// User Role Checks
function is_admin() { return isset($_SESSION['admin_id']); }
function is_vendor() { return isset($_SESSION['vendor_id']); }
function is_user() { return isset($_SESSION['user_id']); }

// Function to redirect unauthorized access
function redirect_unauthorized($role_check, $redirect_to = 'index.php') {
    if (!$role_check()) {
        header("Location: $redirect_to");
        exit();
    }
}
?>