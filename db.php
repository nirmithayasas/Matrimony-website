<?php
// Start session
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Database configuration
$servername = "localhost";  // Usually localhost
$username = "root";         // Your DB username
$password = "#Dell123";             // Your DB password
$dbname = "matrimony_website"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to UTF-8
$conn->set_charset("utf8");

// Function to sanitize inputs
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags($conn->real_escape_string($data)));
}

// Example: check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
