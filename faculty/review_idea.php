<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

$faculty_id = $_SESSION['user_id'];
$idea_id = $_GET['id'] ?? 0;
$msg = "";
$msg_type = "";

// Form Processing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    $decision = trim($_POST['decision']);
    $comment = trim($_POST['comment']);

    if (empty($decision) || empty($comment)) {
        $msg = "Please provide both a decision and review feedback comment.";
        $msg_type = "danger";
    } else {
        /*
           ========================================================================
           DATABASE TRANSACTION
           Ensures review entry, idea status update, and mentorship assignment 
           are completed as an atomic unit.
           ========================================================================
        */
        $conn->begin_transaction();

        try {
            // SQL 1: Insert evaluation entry in 'reviews' table
            $sql_review = "INSERT INTO reviews (idea_id, faculty_id, comment, decision) VALUES (?, ?, ?, ?)";
            $stmt_rev = $conn->prepare($sql_review);
            $stmt_rev->bind_param("iiss", $idea_id, $faculty_id, $comment, $decision);
            $stmt_rev->execute();
            $stmt_rev->close();

            // SQL 2: Update status in 'ideas' table ('approved' / 'rejected')
            $sql_update = "UPDATE ideas SET status = ? WHERE idea_id = ?";
            $stmt_up = $conn->prepare($sql_update);
            $stmt_up->bind_param("si", $decision, $idea_id);
            $stmt_up->execute();
            $stmt_up->close();

            // SQL 3: If approved, automatically assign student-faculty mentorship mapping
            if ($decision == 'approved') {
                $sql_get_student = "SELECT student_id FROM ideas WHERE idea_id = ?";
                $stmt_st = $conn->prepare($sql_get_student);
                $stmt_st->bind_param("i", $idea_id);
                $stmt_st->execute();
                $student_id = $stmt_st->get_result()->fetch_assoc()['student_id'];
                $stmt_st->close();

                $sql_mentor = "INSERT INTO mentorship (idea_id, student_id, faculty_id) VALUES (?, ?, ?)";
                $stmt_m = $conn->prepare($sql_mentor);
                $stmt_m->bind_param("iii", $idea_id, $student_id, $faculty_id);
                $stmt_m->execute();
                $stmt_m->close();
            }

            // Commit transaction
            $conn->commit();

            $msg = "Evaluation submitted successfully! Project status has been updated.";
            $msg_type = "success";
        } catch (Exception $e) {
            // Rollback on any database failure
            $conn->rollback();
            $msg = "Error submitting evaluation: " . $e->getMessage();
            $msg_type = "danger";
        }
    }
}

/*
   FETCH IDEA DETAILS
*/
$sql_idea = "SELECT i.idea_id, i.title, i.category, i.description, i.status, i.submitted_at, 
                    u.name AS student_name, u.email AS student_email, u.department AS student_dept 
             FROM ideas i 
             JOIN users u ON i.student_id = u.user_id 
             WHERE i.idea_id = ?";
$stmt_idea = $conn->prepare($sql_idea);
$stmt_idea->bind_param("i", $idea_id);
$stmt_idea->execute();
$idea = $stmt_idea->get_result()->fetch_assoc();
$stmt_idea->close();

// Redirect back if idea ID is invalid
if (!$idea) {
    header("Location: pending_ideas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate Idea - EWU Innovation Hub</title>
    
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
            margin-left: 0;  
            flex: 1;          
            padding: 30px;
            width: 100%;
        }
        .card.bg-dark {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
        }
        .text-cyan { color: #06b6d4 !important; }
        .form-control, .form-select {
            background-color: rgba(15, 23, 42, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 23, 42, 0.95) !important;
            border-color: #06b6d4 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.25);
        }
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
            <h1 class="h2 text-cyan mb-0">Evaluate Project Idea Proposal 📝</h1>
            <a href="pending_ideas.php" class="btn btn-outline-secondary btn-sm">← Back to Pending Ideas</a>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Side: Idea Proposal Summary -->
            <div class="col-lg-7">
                <div class="card bg-dark text-white p-4 shadow-sm h-100 border-start border-info border-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="text-white mb-0 fw-bold"><?php echo htmlspecialchars($idea['title']); ?></h3>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span>
                    </div>
                    
                    <div class="text-info small mb-3">
                        👤 Student: <strong><?php echo htmlspecialchars($idea['student_name']); ?></strong> | Dept: <?php echo htmlspecialchars($idea['student_dept']); ?> | Email: <?php echo htmlspecialchars($idea['student_email']); ?>
                    </div>

                    <h6 class="text-white-50 mt-3 fw-bold">Project Description:</h6>
                    <p style="white-space: pre-line;" class="text-white-50 leading-relaxed"><?php echo htmlspecialchars($idea['description']); ?></p>

                    <div class="mt-auto pt-3 border-top border-secondary text-white-50 small">
                        Submitted Date: <?php echo date('F d, Y \a\t h:i A', strtotime($idea['submitted_at'])); ?>
                    </div>
                </div>
            </div>

            <!-- Right Side: Faculty Review Form -->
            <div class="col-lg-5">
                <div class="card bg-dark text-white p-4 shadow-sm h-100">
                    <h4 class="text-cyan mb-3 fw-bold">Evaluation Form</h4>

                    <?php if ($idea['status'] != 'pending'): ?>
                        <div class="alert alert-info mb-0">
                            ℹ️ This idea has already been evaluated with status: <strong class="text-uppercase"><?php echo htmlspecialchars($idea['status']); ?></strong>.
                        </div>
                    <?php else: ?>
                        <form method="POST" action="review_idea.php?id=<?php echo $idea_id; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Decision <span class="text-danger">*</span></label>
                                <select name="decision" class="form-select" required>
                                    <option value="">-- Select Decision --</option>
                                    <option value="approved">✅ Approve Idea & Assign Mentorship</option>
                                    <option value="rejected">❌ Reject Idea</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Feedback / Faculty Comments <span class="text-danger">*</span></label>
                                <textarea name="comment" class="form-control" rows="5" placeholder="Write detailed feedback or suggestions for the student..." required></textarea>
                            </div>

                            <button type="submit" name="submit_review" class="btn btn-info text-dark fw-bold w-100 py-2 mt-2">
                                Submit Evaluation
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>