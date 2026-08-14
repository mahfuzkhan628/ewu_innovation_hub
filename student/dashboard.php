<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

// Retrieve currently logged-in student's user ID from session
$student_id = $_SESSION['user_id'];

/* 
   ========================================================================
   SQL STATISTICAL QUERIES FOR STUDENT DASHBOARD
   ========================================================================
*/

// SQL Query 1: Count total ideas submitted by the logged-in student
$sql_total = "SELECT COUNT(*) AS total FROM ideas WHERE student_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $student_id);
$stmt_total->execute();
$total_ideas = $stmt_total->get_result()->fetch_assoc()['total'] ?? 0;
$stmt_total->close();

// SQL Query 2: Count pending ideas submitted by the student
$sql_pending = "SELECT COUNT(*) AS pending FROM ideas WHERE student_id = ? AND status = 'pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param("i", $student_id);
$stmt_pending->execute();
$pending_ideas = $stmt_pending->get_result()->fetch_assoc()['pending'] ?? 0;
$stmt_pending->close();

// SQL Query 3: Count approved ideas submitted by the student
$sql_approved = "SELECT COUNT(*) AS approved FROM ideas WHERE student_id = ? AND status = 'approved'";
$stmt_approved = $conn->prepare($sql_approved);
$stmt_approved->bind_param("i", $student_id);
$stmt_approved->execute();
$approved_ideas = $stmt_approved->get_result()->fetch_assoc()['approved'] ?? 0;
$stmt_approved->close();

// SQL Query 4: Count total mentorship connections for this student
$sql_mentors = "SELECT COUNT(*) AS mentors FROM mentorship WHERE student_id = ?";
$stmt_mentors = $conn->prepare($sql_mentors);
$stmt_mentors->bind_param("i", $student_id);
$stmt_mentors->execute();
$total_mentors = $stmt_mentors->get_result()->fetch_assoc()['mentors'] ?? 0;
$stmt_mentors->close();

/* 
   SQL Query 5: Fetch latest 5 submitted ideas matching table schema columns
   (idea_id, title, category, status, submitted_at)
*/
$sql_recent = "SELECT idea_id, title, category, status, submitted_at FROM ideas WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 5";
$stmt_recent = $conn->prepare($sql_recent);
$stmt_recent->bind_param("i", $student_id);
$stmt_recent->execute();
$recent_ideas_result = $stmt_recent->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EWU Innovation Hub</title>
    
    <!-- EWU Logo Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png">
    
    <!-- Bootstrap CSS & Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body { background-color: #0f172a; color: #f8fafc; min-height: 100vh; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card.bg-dark {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
        }
        .text-cyan { color: #06b6d4 !important; }
        .table-dark { background-color: transparent !important; }
        .table-dark td, .table-dark th { border-color: rgba(255, 255, 255, 0.1); }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Student Sidebar Included -->
        <?php include "../includes/student_sidebar.php"; ?>

        <!-- Dashboard Main Panel -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-cyan">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['full_name'] ?? 'Student'); ?> 👋</h1>
                    <p class="text-white-50">Manage your innovation projects and mentor interactions.</p>
                </div>
                <a href="submit_idea.php" class="btn btn-primary px-3">💡 Submit New Idea</a>
            </div>

            <!-- Dashboard Analytics Widgets -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-dark text-white p-3 shadow-sm">
                        <span class="text-white-50 small fw-semibold">Total Ideas</span>
                        <h2 class="text-cyan mb-0 mt-1"><?php echo $total_ideas; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-white p-3 shadow-sm">
                        <span class="text-white-50 small fw-semibold">Pending Review</span>
                        <h2 class="text-warning mb-0 mt-1"><?php echo $pending_ideas; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-white p-3 shadow-sm">
                        <span class="text-white-50 small fw-semibold">Approved Ideas</span>
                        <h2 class="text-success mb-0 mt-1"><?php echo $approved_ideas; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="mentors.php" class="text-decoration-none">
                        <div class="card bg-dark text-white p-3 shadow-sm">
                            <span class="text-white-50 small fw-semibold">Faculty Mentors</span>
                            <h2 class="text-info mb-0 mt-1"><?php echo $total_mentors; ?></h2>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Submissions Overview Table -->
            <div class="card bg-dark text-white p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-cyan mb-0">Recent Idea Submissions</h5>
                    <a href="my_ideas.php" class="btn btn-sm btn-outline-info">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_ideas_result->num_rows > 0): ?>
                                <?php while ($idea = $recent_ideas_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($idea['title']); ?></strong></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span></td>
                                        <td>
                                            <?php 
                                            $status = $idea['status'];
                                            if ($status == 'approved') {
                                                echo '<span class="badge bg-success">Approved</span>';
                                            } elseif ($status == 'rejected') {
                                                echo '<span class="badge bg-danger">Rejected</span>';
                                            } else {
                                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($idea['submitted_at'])); ?></td>
                                        <td class="text-end">
                                            <a href="idea_details.php?id=<?php echo $idea['idea_id']; ?>" class="btn btn-sm btn-outline-light">Details</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-white-50 py-4">No ideas submitted yet. Click "Submit New Idea" to get started!</td>
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
// Close SQL statement and database connection
$stmt_recent->close();
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>