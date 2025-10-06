<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $full_name = sanitize($_POST['full_name']);
    $gender = sanitize($_POST['gender']);
    $dob = sanitize($_POST['dob']);
    $religion = sanitize($_POST['religion']);
    $caste = sanitize($_POST['caste']);
    $horoscope = sanitize($_POST['horoscope']);
    $profession = sanitize($_POST['profession']);
    $city = sanitize($_POST['city']);
    $country = sanitize($_POST['country']);
    $about_me = sanitize($_POST['about_me']);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email or username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR username=?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Username or Email already exists!";
    } else {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into profiles table
            $stmt = $conn->prepare("INSERT INTO profiles (user_id, full_name, gender, dob, religion, caste, horoscope, profession, city, country, about_me) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssss", $user_id, $full_name, $gender, $dob, $religion, $caste, $horoscope, $profession, $city, $country, $about_me);
            $stmt->execute();

            // Insert Rs. 500 registration payment (Pending)
            $registration_fee = 500;
            $stmt = $conn->prepare("INSERT INTO payments (user_id, type, amount, payment_status) VALUES (?, 'Registration', ?, 'Pending')");
            $stmt->bind_param("id", $user_id, $registration_fee);
            $stmt->execute();

            // Redirect to payment page (replace with actual payment integration)
            header("Location: payment.php?user_id=$user_id&type=Registration");
            exit;

        } else {
            $message = "Registration failed. Try again!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Matrimony</title>
    <style>
        body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f0f0; }
        .register-box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc; width: 400px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 10px; width: 100%; border: none; background: #4CAF50; color: white; border-radius: 4px; cursor: pointer; }
        .message { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Register</h2>
        <?php if($message != "") { echo "<div class='message'>$message</div>"; } ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <input type="date" name="dob" placeholder="Date of Birth" required>
            <input type="text" name="religion" placeholder="Religion">
            <input type="text" name="caste" placeholder="Caste">
            <input type="text" name="horoscope" placeholder="Horoscope">
            <input type="text" name="profession" placeholder="Profession">
            <input type="text" name="city" placeholder="City">
            <input type="text" name="country" placeholder="Country">
            <textarea name="about_me" placeholder="About Me"></textarea>
            <button type="submit">Register & Pay Rs. 500</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
