<?php
// Include session guard and database configuration
include "../includes/session.php";
include "../config/database.php";

/*
   SQL QUERY: Fetch all ideas with status 'pending' joined with student details.
   Tables: ideas (i), users (u)
*/
$sql = "SELECT 
            i.idea_id, 
            i.title, 
            i.category, 
            i.description, 
            i.submitted_at, 
            u.name AS student_name, 
            u.email AS student_email, 
            u.department AS student_dept 
        FROM ideas i 
        JOIN users u ON i.student_id = u.user_id 
        WHERE i.status = 'pending' 
        ORDER BY i.submitted_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Ideas - EWU Innovation Hub</title>
    
    <!-- Favicon & Stylesheets -->
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
    <!-- Include Faculty Sidebar -->
    <?php include "../includes/faculty_sidebar.php"; ?>

    <!-- Main Content Area -->
    <main class="main-content">
        
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom border-secondary">
            <div>
                <h1 class="h2 text-cyan mb-1">Pending Student Ideas ⏳</h1>
                <p class="text-white-50 mb-0">Review submitted student innovations and provide your evaluation.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">← Back to Dashboard</a>
        </div>

        <!-- Pending Ideas Grid -->
        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($idea = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-6">
                        <div class="card bg-dark text-white p-4 shadow-sm h-100 d-flex flex-column border-start border-warning border-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="text-white mb-0 fw-bold"><?php echo htmlspecialchars($idea['title']); ?></h5>
                                <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($idea['category']); ?></span>
                            </div>
                            
                            <div class="small text-info mb-3">
                                👤 Student: <strong><?php echo htmlspecialchars($idea['student_name']); ?></strong> (<?php echo htmlspecialchars($idea['student_dept']); ?>)
                            </div>

                            <p class="text-white-50 mb-4 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($idea['description']); ?>
                            </p>

                            <div class="mt-auto pt-3 border-top border-secondary d-flex justify-content-between align-items-center">
                                <small class="text-white-50">📅 Submitted: <?php echo date('M d, Y', strtotime($idea['submitted_at'])); ?></small>
                                <a href="review_idea.php?id=<?php echo $idea['idea_id']; ?>" class="btn btn-sm btn-info text-dark fw-bold px-3">
                                    Evaluate Idea 🔍
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card bg-dark text-white p-5 text-center shadow-sm">
                <h4 class="text-white-50 mb-2">No Pending Ideas! 🎉</h4>
                <p class="text-white-50 mb-0">All submitted student ideas have been reviewed.</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php
$stmt->close();
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>