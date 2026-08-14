<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

$faculty_id = $_SESSION['user_id'];
$idea_id = $_GET['id'] ?? 0;

/*
   FETCH COMPLETE IDEA DETAILS & REVIEW HISTORY
   Fixed: Removed non-existent review_date column from SQL query
*/
$sql = "SELECT 
            i.idea_id, 
            i.title, 
            i.category, 
            i.description, 
            i.status, 
            i.submitted_at, 
            u.name AS student_name, 
            u.email AS student_email, 
            u.department AS student_dept,
            r.comment AS faculty_comment
        FROM ideas i 
        JOIN users u ON i.student_id = u.user_id 
        LEFT JOIN reviews r ON i.idea_id = r.idea_id AND r.faculty_id = ?
        WHERE i.idea_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $faculty_id, $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Redirect back if idea not found
if (!$idea) {
    header("Location: my_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idea Details - EWU Innovation Hub</title>
    
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body { 
            background-color: #0f172a; 
            color: #f8fafc; 
            min-height: 100vh;
            overflow-x: hidden;
        }
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .main-content { 
            flex-grow: 1;
            padding: 30px; 
            width: calc(100% - 260px);
        }
        .card.bg-dark {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
        }
        .text-cyan { color: #06b6d4 !important; }
        
        @media (max-width: 768px) {
            .dashboard-wrapper { flex-direction: column; }
            .main-content { width: 100%; padding: 15px; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Faculty Sidebar -->
    <?php include "../includes/faculty_sidebar.php"; ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary">
            <h1 class="h2 text-cyan mb-0">Project Idea Details 💡</h1>
            <a href="my_students.php" class="btn btn-outline-secondary btn-sm">← Back to My Mentees</a>
        </div>

        <div class="row g-4">
            <!-- Left Side: Project Main Overview -->
            <div class="col-lg-8">
                <div class="card bg-dark text-white p-4 shadow-sm border-start border-info border-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="text-white mb-0 fw-bold"><?php echo htmlspecialchars($idea['title']); ?></h3>
                        <span class="badge bg-secondary fs-6"><?php echo htmlspecialchars($idea['category']); ?></span>
                    </div>

                    <div class="mb-4">
                        <span class="badge bg-<?php echo ($idea['status'] == 'approved') ? 'success' : (($idea['status'] == 'rejected') ? 'danger' : 'warning'); ?> text-uppercase px-3 py-2">
                            Status: <?php echo htmlspecialchars($idea['status']); ?>
                        </span>
                    </div>

                    <h5 class="text-cyan fw-bold border-bottom border-secondary pb-2 mb-3">Description & Project Goal</h5>
                    <p style="white-space: pre-line;" class="text-white-50 leading-relaxed fs-6">
                        <?php echo htmlspecialchars($idea['description']); ?>
                    </p>

                    <div class="mt-4 pt-3 border-top border-secondary text-white-50 small d-flex justify-content-between">
                        <span>📅 Submitted: <?php echo date('F d, Y', strtotime($idea['submitted_at'])); ?></span>
                        <span>Project ID: #<?php echo $idea['idea_id']; ?></span>
                    </div>
                </div>

                <!-- Faculty Review Notes (If available) -->
                <?php if (!empty($idea['faculty_comment'])): ?>
                    <div class="card bg-dark text-white p-4 shadow-sm border-start border-success border-4">
                        <h5 class="text-success fw-bold mb-2"> Your Evaluation Feedback</h5>
                        <p class="text-white-50 mb-0" style="white-space: pre-line;"><?php echo htmlspecialchars($idea['faculty_comment']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Student / Mentee Profile -->
            <div class="col-lg-4">
                <div class="card bg-dark text-white p-4 shadow-sm">
                    <h5 class="text-cyan fw-bold mb-3 border-bottom border-secondary pb-2">Student Information</h5>
                    
                    <div class="mb-3">
                        <label class="text-white-50 small d-block">Student Name</label>
                        <span class="fw-bold fs-5 text-white"><?php echo htmlspecialchars($idea['student_name']); ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small d-block">Department</label>
                        <span class="fw-semibold text-info"><?php echo htmlspecialchars($idea['student_dept']); ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small d-block">Email Address</label>
                        <a href="mailto:<?php echo htmlspecialchars($idea['student_email']); ?>" class="text-cyan text-decoration-none">
                            ✉️ <?php echo htmlspecialchars($idea['student_email']); ?>
                        </a>
                    </div>

                    <div class="mt-4 pt-3 border-top border-secondary">
                        <a href="mailto:<?php echo htmlspecialchars($idea['student_email']); ?>?subject=Regarding Project: <?php echo urlencode($idea['title']); ?>" class="btn btn-info text-dark fw-bold w-100">
                            Send Email to Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>