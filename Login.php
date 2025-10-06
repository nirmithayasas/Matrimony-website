<?php
session_start();

// Database connection (replace with your DB credentials)
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "#Dell123";
$dbname = "matrimony_website";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simple sanitization function
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Security: regenerate session ID after login
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: dashboard.php"); // redirect after login
            exit;
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Tamil Matrimony</title>
<style>
/* ===== GENERAL STYLES ===== */
body { font-family: Arial, sans-serif; background: #f9f9f9; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
.login-box { background:#fff; padding:40px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2); width:100%; max-width:400px; text-align:center; }
.login-box h2 { margin-bottom:25px; color:#4CAF50; }
.login-box input { width:100%; padding:12px; margin:10px 0; border-radius:5px; border:1px solid #ccc; font-size:16px; }
.login-box button { width:100%; padding:12px; border:none; border-radius:5px; background:#4CAF50; color:white; font-weight:bold; cursor:pointer; transition:0.3s; }
.login-box button:hover { background:#45a049; }
.login-box a { color:#ff9800; text-decoration:none; font-weight:bold; }
.login-box .message { color:red; margin-bottom:10px; }
</style>
<script>
function togglePassword() {
    var pwd = document.getElementById("password");
    if(pwd.type === "password") { pwd.type="text"; }
    else { pwd.type="password"; }
}
</script>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php if($message != "") { echo "<div class='message'>$message</div>"; } ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" id="password" required>
            <input type="checkbox" onclick="togglePassword()"> Show Password
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
