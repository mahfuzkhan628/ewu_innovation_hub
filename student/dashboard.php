<?php
// Include session check & database connection
include "../includes/session.php";
include "../config/database.php";

// Fetch Student Specific Stats
$student_id = $_SESSION['user_id'];

// 1. Total Ideas Submitted
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM ideas WHERE student_id = ?");
$total_stmt->bind_param("i", $student_id);
$total_stmt->execute();
$total_ideas = $total_stmt->get_result()->fetch_assoc()['total'];

// 2. Pending Ideas
$pending_stmt = $conn->prepare("SELECT COUNT(*) AS pending FROM ideas WHERE student_id = ? AND status = 'pending'");
$pending_stmt->bind_param("i", $student_id);
$pending_stmt->execute();
$pending_ideas = $pending_stmt->get_result()->fetch_assoc()['pending'];

// 3. Approved Ideas
$approved_stmt = $conn->prepare("SELECT COUNT(*) AS approved FROM ideas WHERE student_id = ? AND status = 'approved'");
$approved_stmt->bind_param("i", $student_id);
$approved_stmt->execute();
$approved_ideas = $approved_stmt->get_result()->fetch_assoc()['approved'];

// Fetch Recent 5 Submitted Ideas (FIXED: category Spelling Here!)
$recent_stmt = $conn->prepare("SELECT idea_id, title, category, status, submitted_at FROM ideas WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 5");
$recent_stmt->bind_param("i", $student_id);
$recent_stmt->execute();
$recent_ideas = $recent_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EWU Innovation Hub</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .card.bg-dark {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card.bg-dark:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .text-cyan {
            color: #06b6d4 !important;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .sidebar {
                position: relative !important;
                width: 100% !important;
                height: auto !important;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Student Sidebar -->
        <?php include "../includes/student_sidebar.php"; ?>

        <!-- Main Dashboard View Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-cyan">Welcome Back, <?php echo htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
                    <p class="text-white-50">Department of <?php echo htmlspecialchars($_SESSION['department']); ?></p>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="submit_idea.php" class="btn btn-primary shadow-sm">
                        🚀 Submit New Idea
                    </a>
                </div>
            </div>

            <!-- Summary Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card bg-dark text-white border-secondary h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-white-50 mb-2">Total Ideas Submitted</h5>
                            <h2 class="display-5 fw-bold text-info"><?php echo $total_ideas; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-dark text-white border-secondary h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-white-50 mb-2">Pending Review</h5>
                            <h2 class="display-5 fw-bold text-warning"><?php echo $pending_ideas; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-dark text-white border-secondary h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-white-50 mb-2">Approved Innovations</h5>
                            <h2 class="display-5 fw-bold text-success"><?php echo $approved_ideas; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Submissions Table -->
            <div class="card bg-dark text-white border-secondary shadow-sm">
                <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Idea Submissions</h5>
                    <a href="my_ideas.php" class="btn btn-sm btn-outline-info">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead class="table-borderless border-bottom border-secondary">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Submitted Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_ideas->num_rows > 0): ?>
                                    <?php while ($idea = $recent_ideas->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($idea['title']); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span></td>
                                            <td><?php echo date('M d, Y', strtotime($idea['submitted_at'])); ?></td>
                                            <td>
                                                <?php if ($idea['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php elseif ($idea['status'] == 'approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="view_idea.php?id=<?php echo $idea['idea_id']; ?>" class="btn btn-sm btn-outline-light">Details</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-white-50 py-4">
                                            No ideas submitted yet. Click "Submit New Idea" to get started!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>