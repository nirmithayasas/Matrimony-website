<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "#Dell123";
$dbname = "matrimony_website";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT full_name, customer_id FROM users WHERE id='$user_id'";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Fetch appointments
$appointments_sql = "SELECT title, status, date, start_time, end_time, counselor, location FROM appointments WHERE user_id='$user_id' ORDER BY date ASC";
$appointments_result = $conn->query($appointments_sql);

// Fetch documents
$documents_sql = "SELECT doc_type, name, uploaded_date FROM documents WHERE user_id='$user_id' ORDER BY uploaded_date DESC";
$documents_result = $conn->query($documents_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Information Center - Dashboard</title>
    <link rel="stylesheet" href="Clientdashboard.css">
    <link rel="script" href="Client_dashboard.js">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💍 Marriage Information Center</h1>
        <div class="user-info">
            <div>
                <div class="user-names"><?= htmlspecialchars($user['full_name']); ?></div>
                <div class="customer-id">Customer ID: <?= htmlspecialchars($user['customer_id']); ?></div>
            </div>
            <div class="user-avatar"><?= strtoupper(substr($user['full_name'],0,1)) . strtoupper(substr(explode(' ', $user['full_name'])[1],0,1)); ?></div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Appointments</h3>
            <div class="stat-value"><?= $appointments_result->num_rows; ?></div>
            <div class="stat-change">↑ This month</div>
        </div>
        <div class="stat-card">
            <h3>Completed Sessions</h3>
            <div class="stat-value">
                <?php
                $completed_count = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE user_id='$user_id' AND status='Completed'")->fetch_assoc()['count'];
                echo $completed_count;
                ?>
            </div>
            <div class="stat-change">↑ Recently</div>
        </div>
        <div class="stat-card">
            <h3>Upcoming Events</h3>
            <div class="stat-value">
                <?php
                $upcoming_count = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE user_id='$user_id' AND status='Upcoming'")->fetch_assoc()['count'];
                echo $upcoming_count;
                ?>
            </div>
            <div class="stat-change">Next: Tomorrow</div>
        </div>
        <div class="stat-card">
            <h3>Days Until Wedding</h3>
            <div class="stat-value">
                <?php
                $wedding_date = $conn->query("SELECT wedding_date FROM users WHERE id='$user_id'")->fetch_assoc()['wedding_date'];
                $today = new DateTime();
                $wedding = new DateTime($wedding_date);
                echo $today->diff($wedding)->days;
                ?>
            </div>
            <div class="stat-change"><?= date("M d, Y", strtotime($wedding_date)); ?></div>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>Upcoming Appointments</h2>
            <div class="appointments-list" id="appointmentsList">
                <?php while($row = $appointments_result->fetch_assoc()): ?>
                    <div class="appointment-item">
                        <div class="appointment-header">
                            <span class="appointment-title"><?= htmlspecialchars($row['title']); ?></span>
                            <span class="status-badge status-<?= strtolower($row['status']); ?>"><?= htmlspecialchars($row['status']); ?></span>
                        </div>
                        <div class="appointment-time"><?= date("M d, h:i A", strtotime($row['date'].' '.$row['start_time'])); ?> - <?= date("h:i A", strtotime($row['end_time'])); ?></div>
                        <div class="appointment-details">With <?= htmlspecialchars($row['counselor']); ?> | <?= htmlspecialchars($row['location']); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="card">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <button class="action-btn" onclick="bookAppointment()">📅 Book Appointment</button>
                <button class="action-btn" onclick="viewDocuments()">📄 View Documents</button>
                <button class="action-btn" onclick="contactSupport()">💬 Contact Counselor</button>
                <button class="action-btn" onclick="makePayment()">💳 Make Payment</button>
                <button class="action-btn" onclick="viewResources()">📚 View Resources</button>
            </div>
        </div>
    </div>

    <div class="card documents-section">
        <h2>Your Documents</h2>
        <div class="documents-grid">
            <?php while($doc = $documents_result->fetch_assoc()): ?>
                <div class="document-card" onclick="viewDocument('<?= htmlspecialchars($doc['doc_type']); ?>')">
                    <div class="document-icon">📄</div>
                    <div class="document-name"><?= htmlspecialchars($doc['name']); ?></div>
                    <div class="document-date">Uploaded: <?= date("M d, Y", strtotime($doc['uploaded_date'])); ?></div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>


</body>
</html>
