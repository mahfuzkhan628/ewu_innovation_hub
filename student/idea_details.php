<?php
// Include session guard and database configuration
include "../includes/session.php";
include "../config/database.php";

// Get logged-in student's user ID from session
$student_id = $_SESSION['user_id'];

/*
   SQL QUERY: Retrieve all ideas submitted by this specific student.
   Ordered by submission date (newest first).
*/
$sql = "SELECT idea_id, title, category, description, status, submitted_at 
        FROM ideas 
        WHERE student_id = ? 
        ORDER BY submitted_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Submitted Ideas - EWU Innovation Hub</title>
    
    <!-- EWU Logo Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png">
    
    <!-- Bootstrap CSS -->
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
        .accordion-button {
            background-color: rgba(15, 23, 42, 0.6) !important;
            color: #ffffff !important;
            border: none;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(6, 182, 212, 0.15) !important;
            color: #06b6d4 !important;
            box-shadow: none;
        }
        .accordion-button::after {
            filter: invert(1);
        }
        .accordion-item {
            background-color: transparent !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 8px !important;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .accordion-body {
            background-color: rgba(15, 23, 42, 0.8) !important;
            color: #cbd5e1 !important;
        }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Student Sidebar Included -->
        <?php include "../includes/student_sidebar.php"; ?>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-cyan">My Submitted Ideas 📂</h1>
                    <p class="text-white-50">Track the status and faculty feedback for your submitted innovations.</p>
                </div>
                <a href="submit_idea.php" class="btn btn-primary px-3">💡 Submit New Idea</a>
            </div>

            <!-- Submitted Ideas List -->
            <div class="card bg-dark text-white p-4 shadow-sm">
                <?php if ($result->num_rows > 0): ?>
                    <div class="accordion" id="ideasAccordion">
                        <?php 
                        $counter = 0;
                        while ($idea = $result->fetch_assoc()): 
                            $counter++;
                            $collapse_id = "collapse" . $idea['idea_id'];
                            $heading_id = "heading" . $idea['idea_id'];
                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="<?php echo $heading_id; ?>">
                                    <button class="accordion-button <?php echo $counter > 1 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapse_id; ?>" aria-expanded="<?php echo $counter === 1 ? 'true' : 'false'; ?>" aria-controls="<?php echo $collapse_id; ?>">
                                        <div class="d-flex w-100 justify-content-between align-items-center me-3 flex-wrap gap-2">
                                            <div>
                                                <strong>#<?php echo $idea['idea_id']; ?>: <?php echo htmlspecialchars($idea['title']); ?></strong>
                                                <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($idea['category']); ?></span>
                                            </div>
                                            <div>
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
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="<?php echo $collapse_id; ?>" class="accordion-collapse collapse <?php echo $counter === 1 ? 'show' : ''; ?>" aria-labelledby="<?php echo $heading_id; ?>" data-bs-parent="#ideasAccordion">
                                    <div class="accordion-body">
                                        <h6 class="text-cyan mb-2">Description & Problem Statement:</h6>
                                        <p style="white-space: pre-line;"><?php echo htmlspecialchars($idea['description']); ?></p>
                                        <hr class="border-secondary">
                                        <div class="small text-white-50">
                                            📅 Submitted on: <?php echo date('F d, Y \a\t h:i A', strtotime($idea['submitted_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <h4 class="text-white-50">No ideas submitted yet! 🚀</h4>
                        <p class="text-white-50 mb-4">Have a great innovation in mind? Share it with our faculty mentors now.</p>
                        <a href="submit_idea.php" class="btn btn-primary px-4">Submit Your First Idea</a>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<?php
// Close SQL statement and database connection
$stmt->close();
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>