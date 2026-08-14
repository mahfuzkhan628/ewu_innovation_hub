<?php
// Include session verification & database connection
include "../includes/session.php";
include "../config/database.php";

// Get logged-in faculty user ID
$faculty_id = $_SESSION['user_id'];

/* 
   ========================================================================
   DATABASE QUERIES FOR FACULTY DASHBOARD METRICS
   ========================================================================
*/

// Query 1: Total Pending Ideas waiting for faculty evaluation in the system
$sql_pending = "SELECT COUNT(*) AS total_pending FROM ideas WHERE status = 'pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->execute();
$total_pending = $stmt_pending->get_result()->fetch_assoc()['total_pending'] ?? 0;
$stmt_pending->close();

// Query 2: Total reviews completed by this specific faculty member
$sql_my_reviews = "SELECT COUNT(*) AS my_reviews FROM reviews WHERE faculty_id = ?";
$stmt_my_reviews = $conn->prepare($sql_my_reviews);
$stmt_my_reviews->bind_param("i", $faculty_id);
$stmt_my_reviews->execute();
$my_reviews = $stmt_my_reviews->get_result()->fetch_assoc()['my_reviews'] ?? 0;
$stmt_my_reviews->close();

// Query 3: Total student mentees assigned to this faculty
$sql_mentorships = "SELECT COUNT(*) AS total_mentored FROM mentorship WHERE faculty_id = ?";
$stmt_mentorships = $conn->prepare($sql_mentorships);
$stmt_mentorships->bind_param("i", $faculty_id);
$stmt_mentorships->execute();
$total_mentored = $stmt_mentorships->get_result()->fetch_assoc()['total_mentored'] ?? 0;
$stmt_mentorships->close();

// Query 4: Fetch latest 5 pending ideas with student info for quick evaluation table
$sql_recent_pending = "SELECT 
                          i.idea_id, 
                          i.title, 
                          i.category, 
                          i.submitted_at, 
                          u.name AS student_name, 
                          u.department AS student_dept 
                       FROM ideas i 
                       JOIN users u ON i.student_id = u.user_id 
                       WHERE i.status = 'pending' 
                       ORDER BY i.submitted_at DESC 
                       LIMIT 5";

$stmt_recent = $conn->prepare($sql_recent_pending);
$stmt_recent->execute();
$recent_pending_result = $stmt_recent->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - EWU Innovation Hub</title>
    
    <!-- Favicon & Stylesheets -->
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body { background-color: #0f172a; color: #f8fafc; min-height: 100vh; }
        .main-content { padding: 30px; }
        .card.bg-dark {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
        }
        .text-cyan { color: #06b6d4 !important; }
        .table-dark { background-color: transparent !important; }
        .table-dark td, .table-dark th { border-color: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Faculty Sidebar Included -->
        <?php include "../includes/faculty_sidebar.php"; ?>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-cyan mb-1">Faculty Dashboard 👨‍🏫</h1>
                    <p class="text-white-50 mb-0">Welcome, Professor <?php echo htmlspecialchars($_SESSION['name'] ?? 'Faculty'); ?>! Evaluate innovative ideas and mentor students.</p>
                </div>
                <a href="pending_ideas.php" class="btn btn-info text-dark fw-bold px-3">🔍 Review Pending Ideas</a>
            </div>

            <!-- Analytic Widgets Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <a href="pending_ideas.php" class="text-decoration-none">
                        <div class="card bg-dark text-white p-3 shadow-sm h-100 border-start border-warning border-4">
                            <span class="text-white-50 small fw-semibold">Pending Ideas</span>
                            <h2 class="text-warning mb-0 mt-2 fw-bold"><?php echo $total_pending; ?></h2>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white p-3 shadow-sm h-100 border-start border-info border-4">
                        <span class="text-white-50 small fw-semibold">Evaluations Completed</span>
                        <h2 class="text-cyan mb-0 mt-2 fw-bold"><?php echo $my_reviews; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <a href="my_students.php" class="text-decoration-none">
                        <div class="card bg-dark text-white p-3 shadow-sm h-100 border-start border-success border-4">
                            <span class="text-white-50 small fw-semibold">Assigned Mentees</span>
                            <h2 class="text-success mb-0 mt-2 fw-bold"><?php echo $total_mentored; ?></h2>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Submissions Table -->
            <div class="card bg-dark text-white p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-cyan mb-0">Recent Pending Submissions</h5>
                    <a href="pending_ideas.php" class="btn btn-sm btn-outline-info">View All Pending</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Idea Title</th>
                                <th>Student Name</th>
                                <th>Category</th>
                                <th>Submitted Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_pending_result->num_rows > 0): ?>
                                <?php while ($idea = $recent_pending_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($idea['title']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($idea['student_name']); ?>
                                            <br><small class="text-white-50"><?php echo htmlspecialchars($idea['student_dept']); ?></small>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($idea['submitted_at'])); ?></td>
                                        <td class="text-end">
                                            <!-- Linking strictly to review_idea.php -->
                                            <a href="review_idea.php?id=<?php echo $idea['idea_id']; ?>" class="btn btn-sm btn-info text-dark fw-bold">Evaluate</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-white-50 py-4">🎉 All clear! No pending ideas awaiting review.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<?php
// Close SQL resources
$stmt_recent->close();
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>