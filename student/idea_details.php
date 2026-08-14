<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$idea_id = $_GET['id'] ?? 0;

/*
   SQL QUERY: Fetch specific idea details owned by logged-in student.
*/
$sql_idea = "SELECT idea_id, title, category, description, status, submitted_at 
              FROM ideas 
              WHERE idea_id = ? AND student_id = ?";
$stmt_idea = $conn->prepare($sql_idea);
$stmt_idea->bind_param("ii", $idea_id, $student_id);
$stmt_idea->execute();
$idea = $stmt_idea->get_result()->fetch_assoc();
$stmt_idea->close();

// Redirect if idea not found or doesn't belong to current student
if (!$idea) {
    header("Location: my_ideas.php");
    exit();
}

/*
   SQL QUERY: Fetch review comments and decision by Faculty for this idea.
   Performs JOIN with 'users' table to get faculty name and department.
*/
$sql_review = "SELECT r.comment, r.decision, r.reviewed_at, u.name AS faculty_name, u.department 
               FROM reviews r 
               JOIN users u ON r.faculty_id = u.user_id 
               WHERE r.idea_id = ? 
               ORDER BY r.reviewed_at DESC LIMIT 1";
$stmt_review = $conn->prepare($sql_review);
$stmt_review->bind_param("i", $idea_id);
$stmt_review->execute();
$review = $stmt_review->get_result()->fetch_assoc();
$stmt_review->close();
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
        body { background-color: #0f172a; color: #f8fafc; min-height: 100vh; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card.bg-dark {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
        }
        .text-cyan { color: #06b6d4 !important; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include "../includes/student_sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
                <h1 class="h2 text-cyan">Idea Details #<?php echo $idea['idea_id']; ?></h1>
                <a href="my_ideas.php" class="btn btn-outline-secondary btn-sm">← Back to My Ideas</a>
            </div>

            <!-- Idea Details Card -->
            <div class="card bg-dark text-white p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="mb-1"><?php echo htmlspecialchars($idea['title']); ?></h3>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span>
                    </div>
                    <div>
                        <?php 
                        $status = $idea['status'];
                        if ($status == 'approved') {
                            echo '<span class="badge bg-success fs-6">Approved</span>';
                        } elseif ($status == 'rejected') {
                            echo '<span class="badge bg-danger fs-6">Rejected</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark fs-6">Pending Review</span>';
                        }
                        ?>
                    </div>
                </div>

                <h6 class="text-cyan mt-4">Description:</h6>
                <p style="white-space: pre-line;" class="text-white-50"><?php echo htmlspecialchars($idea['description']); ?></p>

                <div class="text-end text-white-50 small mt-3 border-top border-secondary pt-2">
                    Submitted Date: <?php echo date('F d, Y \a\t h:i A', strtotime($idea['submitted_at'])); ?>
                </div>
            </div>

            <!-- Review Feedback Card -->
            <?php if ($review): ?>
                <div class="card bg-dark text-white p-4 shadow-sm">
                    <h5 class="text-cyan mb-3">Faculty Review Feedback</h5>
                    <p class="mb-2"><strong>Reviewer:</strong> <?php echo htmlspecialchars($review['faculty_name']); ?> (<?php echo htmlspecialchars($review['department']); ?>)</p>
                    <p class="mb-2"><strong>Decision:</strong> <span class="badge bg-info text-dark"><?php echo ucfirst($review['decision']); ?></span></p>
                    <p class="mb-0"><strong>Comments:</strong> <?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>