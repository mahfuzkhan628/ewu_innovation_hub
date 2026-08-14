<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

// Get logged-in student's user ID from session
$student_id = $_SESSION['user_id'];

/*
   SQL QUERY: Fetch all faculty mentors assigned to this student's ideas.
   Performs INNER JOIN across 'mentorship', 'ideas', and 'users' (faculty) tables.
*/
$sql = "SELECT 
            m.mentorship_id,
            m.assigned_date,
            i.idea_id,
            i.title AS idea_title,
            i.category AS idea_category,
            f.name AS faculty_name,
            f.email AS faculty_email,
            f.department AS faculty_department
        FROM mentorship m
        JOIN ideas i ON m.idea_id = i.idea_id
        JOIN users f ON m.faculty_id = f.user_id
        WHERE m.student_id = ?
        ORDER BY m.assigned_date DESC";

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
    <title>My Mentors - EWU Innovation Hub</title>
    
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
        .mentor-card {
            border-left: 4px solid #06b6d4 !important;
            transition: transform 0.2s ease-in-out;
        }
        .mentor-card:hover { transform: translateY(-3px); }
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
                    <h1 class="h2 text-cyan">My Mentors 👨‍🏫</h1>
                    <p class="text-white-50">Faculty members guiding your innovation projects.</p>
                </div>
            </div>

            <!-- Faculty Mentors List -->
            <?php if ($result->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card bg-dark text-white p-4 shadow-sm mentor-card h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-secondary bg-opacity-25 p-3 me-3 text-cyan fw-bold fs-4">
                                        <?php echo strtoupper(substr($row['faculty_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 text-white"><?php echo htmlspecialchars($row['faculty_name']); ?></h5>
                                        <small class="text-info"><?php echo htmlspecialchars($row['faculty_department']); ?></small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <span class="text-white-50 small">Assigned Project:</span>
                                    <div class="fw-semibold text-cyan"><?php echo htmlspecialchars($row['idea_title']); ?></div>
                                </div>

                                <div class="mb-3">
                                    <span class="text-white-50 small">Contact Email:</span>
                                    <div>
                                        <a href="mailto:<?php echo htmlspecialchars($row['faculty_email']); ?>" class="text-white text-decoration-none border-bottom border-secondary">
                                            ✉️ <?php echo htmlspecialchars($row['faculty_email']); ?>
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-auto pt-3 border-top border-secondary d-flex justify-content-between align-items-center">
                                    <small class="text-white-50"><?php echo date('M d, Y', strtotime($row['assigned_date'])); ?></small>
                                    <a href="mentorship_details.php?id=<?php echo $row['mentorship_id']; ?>" class="btn btn-sm btn-outline-info">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card bg-dark text-white p-5 text-center shadow-sm">
                    <h4 class="text-white-50">No Mentors Assigned Yet! 🤝</h4>
                    <p class="text-white-50 mb-0">Mentorship linkages will appear here once faculty members review and approve your ideas.</p>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php
// Close SQL statement and connection
$stmt->close();
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>