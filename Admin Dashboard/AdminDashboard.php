<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
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

// Fetch dashboard stats
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$activeProfiles = $conn->query("SELECT COUNT(*) as active FROM users WHERE status='Active'")->fetch_assoc()['active'];
$successfulMatches = $conn->query("SELECT COUNT(*) as matched FROM matches WHERE status='Accepted'")->fetch_assoc()['matched'];
$pendingApprovals = $conn->query("SELECT COUNT(*) as pending FROM users WHERE status='Pending'")->fetch_assoc()['pending'];

// Fetch recent users
$recentUsersResult = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// Fetch recent matches
$recentMatchesResult = $conn->query("SELECT m.*, u1.name AS user1, u2.name AS user2
                                     FROM matches m
                                     JOIN users u1 ON m.user1_id=u1.id
                                     JOIN users u2 ON m.user2_id=u2.id
                                     ORDER BY m.created_at DESC
                                     LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="script" href="admin_dashboard.js">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>💍 Tamil Matrimony</h2>
                <span>Admin Dashboard</span>
            </div>
            <nav>
                <div class="nav-item active" onclick="showPage('dashboard')">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </div>
                <div class="nav-item" onclick="showPage('profiles')">
                    <span class="nav-icon">👥</span>
                    <span>User Profiles</span>
                </div>
                <div class="nav-item" onclick="showPage('matches')">
                    <span class="nav-icon">💑</span>
                    <span>Matches</span>
                </div>
                <div class="nav-item" onclick="showPage('settings')">
                    <span class="nav-icon">⚙️</span>
                    <span>Settings</span>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Page -->
            <div class="page active" id="dashboard">
                <div class="header">
                    <h1>Dashboard Overview</h1>
                    <div class="user-info">
                        <div>
                            <div style="font-weight: 600; color: #2d3748;">Admin User</div>
                            <div style="font-size: 12px; color: #718096;">administrator</div>
                        </div>
                        <div class="user-avatar">A</div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>TOTAL USERS</h3>
                        <div class="stat-value">2,847</div>
                        <div class="stat-change positive">↑ 12% from last month</div>
                    </div>
                    <div class="stat-card">
                        <h3>ACTIVE PROFILES</h3>
                        <div class="stat-value">1,923</div>
                        <div class="stat-change positive">↑ 8% from last month</div>
                    </div>
                    <div class="stat-card">
                        <h3>SUCCESSFUL MATCHES</h3>
                        <div class="stat-value">432</div>
                        <div class="stat-change positive">↑ 15% from last month</div>
                    </div>
                    <div class="stat-card">
                        <h3>PENDING APPROVALS</h3>
                        <div class="stat-value">67</div>
                        <div class="stat-change negative">↓ 3% from last month</div>
                    </div>
                </div>

                <div class="chart-container">
                    <h2 style="margin-bottom: 20px; color: #2d3748;">User Registration Trends</h2>
                    <div class="chart-placeholder">📈 Chart: Monthly User Registrations</div>
                </div>

                <div class="data-section">
                    <div class="section-header">
                        <h2>Recent Registrations</h2>
                        <button class="btn btn-primary">View All</button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Location</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentUsers">
                            <tr>
                                <td>Priya Sharma</td>
                                <td>26</td>
                                <td>Chennai</td>
                                <td>Oct 3, 2025</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-success btn-small">Approve</button>
                                        <button class="btn btn-danger btn-small">Reject</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Rajesh Kumar</td>
                                <td>29</td>
                                <td>Coimbatore</td>
                                <td>Oct 2, 2025</td>
                                <td><span class="status-badge status-active">Active</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">View</button>
                                        <button class="btn btn-warning btn-small">Edit</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Lakshmi Devi</td>
                                <td>24</td>
                                <td>Madurai</td>
                                <td>Oct 1, 2025</td>
                                <td><span class="status-badge status-active">Active</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">View</button>
                                        <button class="btn btn-warning btn-small">Edit</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Profiles Page -->
            <div class="page" id="profiles">
                <div class="header">
                    <h1>User Profiles</h1>
                    <div class="user-info">
                        <button class="btn btn-primary">Add New Profile</button>
                    </div>
                </div>

                <div class="data-section">
                    <div class="section-header">
                        <h2>All User Profiles</h2>
                        <input type="text" class="search-bar" placeholder="Search profiles..." id="profileSearch">
                    </div>

                    <div class="profile-grid">
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">A</div>
                                <h3 style="margin: 0;">Anitha Raj</h3>
                                <p style="font-size: 14px; margin-top: 5px;">28 years, Chennai</p>
                            </div>
                            <div class="profile-body">
                                <div class="profile-info"><strong>Education:</strong> B.Tech, Computer Science</div>
                                <div class="profile-info"><strong>Occupation:</strong> Software Engineer</div>
                                <div class="profile-info"><strong>Religion:</strong> Hindu</div>
                                <div class="profile-info"><strong>Status:</strong> <span class="status-badge status-active">Active</span></div>
                                <div class="profile-actions">
                                    <button class="btn btn-primary btn-small" style="flex: 1;">View Details</button>
                                    <button class="btn btn-warning btn-small" style="flex: 1;">Edit</button>
                                </div>
                            </div>
                        </div>

                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">V</div>
                                <h3 style="margin: 0;">Vijay Kumar</h3>
                                <p style="font-size: 14px; margin-top: 5px;">31 years, Bangalore</p>
                            </div>
                            <div class="profile-body">
                                <div class="profile-info"><strong>Education:</strong> MBA, Finance</div>
                                <div class="profile-info"><strong>Occupation:</strong> Business Analyst</div>
                                <div class="profile-info"><strong>Religion:</strong> Hindu</div>
                                <div class="profile-info"><strong>Status:</strong> <span class="status-badge status-active">Active</span></div>
                                <div class="profile-actions">
                                    <button class="btn btn-primary btn-small" style="flex: 1;">View Details</button>
                                    <button class="btn btn-warning btn-small" style="flex: 1;">Edit</button>
                                </div>
                            </div>
                        </div>

                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">M</div>
                                <h3 style="margin: 0;">Meena Sundaram</h3>
                                <p style="font-size: 14px; margin-top: 5px;">26 years, Trichy</p>
                            </div>
                            <div class="profile-body">
                                <div class="profile-info"><strong>Education:</strong> M.Sc, Mathematics</div>
                                <div class="profile-info"><strong>Occupation:</strong> Teacher</div>
                                <div class="profile-info"><strong>Religion:</strong> Hindu</div>
                                <div class="profile-info"><strong>Status:</strong> <span class="status-badge status-pending">Pending</span></div>
                                <div class="profile-actions">
                                    <button class="btn btn-success btn-small" style="flex: 1;">Approve</button>
                                    <button class="btn btn-danger btn-small" style="flex: 1;">Reject</button>
                                </div>
                            </div>
                        </div>

                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">S</div>
                                <h3 style="margin: 0;">Suresh Babu</h3>
                                <p style="font-size: 14px; margin-top: 5px;">33 years, Salem</p>
                            </div>
                            <div class="profile-body">
                                <div class="profile-info"><strong>Education:</strong> B.E, Mechanical</div>
                                <div class="profile-info"><strong>Occupation:</strong> Engineer</div>
                                <div class="profile-info"><strong>Religion:</strong> Hindu</div>
                                <div class="profile-info"><strong>Status:</strong> <span class="status-badge status-active">Active</span></div>
                                <div class="profile-actions">
                                    <button class="btn btn-primary btn-small" style="flex: 1;">View Details</button>
                                    <button class="btn btn-warning btn-small" style="flex: 1;">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matches Page -->
            <div class="page" id="matches">
                <div class="header">
                    <h1>Match Management</h1>
                    <div class="user-info">
                        <button class="btn btn-primary">Create Manual Match</button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>TOTAL MATCHES</h3>
                        <div class="stat-value">1,245</div>
                        <div class="stat-change positive">↑ 18% this month</div>
                    </div>
                    <div class="stat-card">
                        <h3>SUCCESSFUL MATCHES</h3>
                        <div class="stat-value">432</div>
                        <div class="stat-change positive">↑ 15% this month</div>
                    </div>
                    <div class="stat-card">
                        <h3>PENDING MATCHES</h3>
                        <div class="stat-value">156</div>
                        <div class="stat-change">No change</div>
                    </div>
                </div>

                <div class="data-section">
                    <div class="section-header">
                        <h2>Recent Matches</h2>
                        <input type="text" class="search-bar" placeholder="Search matches...">
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Profile 1</th>
                                <th>Profile 2</th>
                                <th>Match Score</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Priya Sharma (26)</td>
                                <td>Rajesh Kumar (29)</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="flex: 1; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 85%; height: 100%; background: #48bb78;"></div>
                                        </div>
                                        <span style="font-weight: 600; color: #48bb78;">85%</span>
                                    </div>
                                </td>
                                <td>Oct 3, 2025</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">View</button>
                                        <button class="btn btn-danger btn-small">Remove</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Lakshmi Devi (24)</td>
                                <td>Suresh Babu (33)</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="flex: 1; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 92%; height: 100%; background: #48bb78;"></div>
                                        </div>
                                        <span style="font-weight: 600; color: #48bb78;">92%</span>
                                    </div>
                                </td>
                                <td>Oct 1, 2025</td>
                                <td><span class="status-badge status-active">Accepted</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">View</button>
                                        <button class="btn btn-success btn-small">Connect</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Anitha Raj (28)</td>
                                <td>Vijay Kumar (31)</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="flex: 1; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 78%; height: 100%; background: #ed8936;"></div>
                                        </div>
                                        <span style="font-weight: 600; color: #ed8936;">78%</span>
                                    </div>
                                </td>
                                <td>Sep 29, 2025</td>
                                <td><span class="status-badge status-active">Accepted</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">View</button>
                                        <button class="btn btn-success btn-small">Connect</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="chart-container">
                    <h2 style="margin-bottom: 20px; color: #2d3748;">Match Success Rate</h2>
                    <div class="chart-placeholder">📊 Chart: Monthly Match Success Rates</div>
                </div>
            </div>

            <!-- Settings Page -->
            <div class="page" id="settings">
                <div class="header">
                    <h1>System Settings</h1>
                    <div class="user-info">
                        <button class="btn btn-success">Save Changes</button>
                    </div>
                </div>

                <div class="data-section">
                    <h2 style="margin-bottom: 25px; color: #2d3748;">General Settings</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" class="form-control" value="Tamil Matrimony">
                        </div>
                        <div class="form-group">
                            <label>Admin Email</label>
                            <input type="email" class="form-control" value="admin@tamilmatrimony.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Support Phone</label>
                            <input type="tel" class="form-control" value="+91 9876543210">
                        </div>
                        <div class="form-group">
                            <label>Time Zone</label>
                            <select class="form-control">
                                <option>Asia/Kolkata (IST)</option>
                                <option>Asia/Dubai</option>
                                <option>America/New_York</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="data-section">
                    <h2 style="margin-bottom: 25px; color: #2d3748;">Match Algorithm Settings</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Minimum Match Score (%)</label>
                            <input type="number" class="form-control" value="70" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label>Auto-Match Frequency</label>
                            <select class="form-control">
                                <option>Daily</option>
                                <option selected>Weekly</option>
                                <option>Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Match Criteria Priority</label>
                        <textarea class="form-control" rows="3">
                            1. Education Level
                            2. Occupation
                            3. Location
                            4. Religious Background
                            5. Age Compatibility
                            </textarea>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>