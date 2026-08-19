<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

$faculty_id = $_SESSION['user_id'];

/*
   SQL QUERY: Fetch all student mentees assigned to this faculty member.
   Joins mentorship, ideas, and users (students) tables.
*/
$sql = "SELECT 
            m.mentorship_id, 
            m.assigned_date, 
            i.idea_id,
            i.title AS idea_title, 
            i.category AS idea_category, 
            u.name AS student_name, 
            u.email AS student_email, 
            u.department AS student_dept 
        FROM mentorship m 
        JOIN ideas i ON m.idea_id = i.idea_id 
        JOIN users u ON m.student_id = u.user_id 
        WHERE m.faculty_id = ? 
        ORDER BY m.assigned_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Student Mentees - EWU Innovation Hub</title>
    
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
        .hover-underline:hover { text-decoration: underline !important; }
        
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
            <h1 class="h2 text-cyan mb-0">My Student Mentees 👨‍🎓</h1>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">← Back to Dashboard</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card bg-dark text-white p-4 shadow-sm h-100 border-start border-success border-4">
                            <h5 class="text-white mb-1 fw-bold"><?php echo htmlspecialchars($row['student_name']); ?></h5>
                            <small class="text-info"><?php echo htmlspecialchars($row['student_dept']); ?></small>
                            
                            <div class="mt-3">
                                <span class="text-white-50 small">Approved Project:</span>
                                <!-- Clickable Idea Link -->
                                <div class="fw-semibold text-cyan">
                                    <a href="idea_details.php?id=<?php echo $row['idea_id']; ?>" class="text-cyan text-decoration-none hover-underline">
                                        <?php echo htmlspecialchars($row['idea_title']); ?> 🔗
                                    </a>
                                </div>
                                <span class="badge bg-secondary mt-1"><?php echo htmlspecialchars($row['idea_category']); ?></span>
                            </div>

                            <div class="mt-3">
                                <span class="text-white-50 small">Email Contact:</span>
                                <div>
                                    <a href="mailto:<?php echo htmlspecialchars($row['student_email']); ?>" class="text-white text-decoration-none border-bottom border-secondary">
                                        ✉️ <?php echo htmlspecialchars($row['student_email']); ?>
                                    </a>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top border-secondary text-white-50 small">
                                Assigned: <?php echo date('M d, Y', strtotime($row['assigned_date'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card bg-dark text-white p-5 text-center shadow-sm">
                <h4 class="text-white-50 mb-2">No Mentees Assigned Yet! 🤝</h4>
                <p class="text-white-50 mb-0">Approve student ideas in the Pending Ideas portal to assign them under your mentorship.</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php $stmt->close(); $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>