<?php
// student/dashboard.php

// 1. Session Guard & Database Connection
include "../includes/session.php";
include "../config/database.php";

$student_id = $_SESSION['user_id'];

// 2. Fetch Idea Statistics for this Student
$total_ideas = 0;
$pending_ideas = 0;
$approved_ideas = 0;

$stats_sql = "SELECT status, COUNT(*) as total FROM ideas WHERE student_id = ? GROUP BY status";
$stmt = $conn->prepare($stats_sql);
if ($stmt) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['status'] === 'pending') {
            $pending_ideas = (int)$row['total'];
        } elseif ($row['status'] === 'approved') {
            $approved_ideas = (int)$row['total'];
        }
        $total_ideas += (int)$row['total'];
    }
    $stmt->close();
}

// 3. Fetch Top 5 Recent Ideas for this Student
$recent_ideas = [];
$recent_sql = "SELECT idea_id, title, category, status, submitted_at 
               FROM ideas 
               WHERE student_id = ? 
               ORDER BY submitted_at DESC 
               LIMIT 5";

$stmt = $conn->prepare($recent_sql);
if ($stmt) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $recent_ideas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EWU Innovation Hub</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png?v=1.1">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Fixed Navbar -->
    <nav class="navbar">
        <div class="container-fluid px-4">
            <a href="dashboard.php" class="brand">
                <img src="../assets/images/ewu_logo.png" alt="EWU Logo">
                EWU Innovation Hub
            </a>

            <div class="nav-links">
                <span class="text-secondary fw-semibold">
                    ID: <?php echo htmlspecialchars($_SESSION['university_id'] ?? ''); ?>
                </span>
                <a href="../auth/logout.php" class="login-btn" style="padding: 8px 20px; font-size: 14px;">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        
        <!-- Sidebar Include -->
        <?php include "../includes/student_sidebar.php"; ?>

        <!-- Main Content Area -->
        <main class="dashboard-content">

            <!-- Welcome Header -->
            <div class="welcome-banner">
                <div>
                    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
                    <p>Department of <?php echo htmlspecialchars($_SESSION['department']); ?> • Student Portal</p>
                </div>
                <a href="submit_idea.php" class="quick-submit-btn">
                    + Submit New Idea
                </a>
            </div>

            <!-- Real-Time Metrics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">💡</div>
                    <div class="stat-info">
                        <h3><?php echo $total_ideas; ?></h3>
                        <p>Total Submitted Ideas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $pending_ideas; ?></h3>
                        <p>Pending Faculty Review</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon approved">✅</div>
                    <div class="stat-info">
                        <h3><?php echo $approved_ideas; ?></h3>
                        <p>Approved & Mentored</p>
                    </div>
                </div>
            </div>

            <!-- Recent Ideas Table Card -->
            <div class="recent-card">
                <div class="card-header-flex">
                    <h2>Recent Innovation Submissions</h2>
                    <a href="my_ideas.php">View All Ideas →</a>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Idea Title</th>
                                <th>Category</th>
                                <th>Submission Date</th>
                                <th>Review Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_ideas)): ?>
                                <?php foreach ($recent_ideas as $idea): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($idea['title']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-secondary"><?php echo htmlspecialchars($idea['category']); ?></span>
                                        </td>
                                        <td>
                                            <?php echo date("M d, Y", strtotime($idea['submitted_at'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge-status <?php echo htmlspecialchars($idea['status']); ?>">
                                                <?php echo htmlspecialchars($idea['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="idea_details.php?id=<?php echo $idea['idea_id']; ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 12px; font-size: 13px;">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary">
                                        You haven't submitted any innovation ideas yet. <br>
                                        Click <strong><a href="submit_idea.php" style="color: #0066ff;">+ Submit New Idea</a></strong> to post your first project!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>