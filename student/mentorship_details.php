<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

$student_id = $_SESSION['user_id'];
$mentorship_id = $_GET['id'] ?? 0;

/*
   SQL QUERY: Fetch mentorship record joined with faculty & idea info.
*/
$sql = "SELECT 
            m.mentorship_id,
            m.assigned_date,
            i.title AS idea_title,
            i.description AS idea_description,
            i.category AS idea_category,
            f.name AS faculty_name,
            f.email AS faculty_email,
            f.department AS faculty_department
        FROM mentorship m
        JOIN ideas i ON m.idea_id = i.idea_id
        JOIN users f ON m.faculty_id = f.user_id
        WHERE m.mentorship_id = ? AND m.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $mentorship_id, $student_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: mentors.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentorship Details - EWU Innovation Hub</title>
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
                <h1 class="h2 text-cyan">Mentorship Details</h1>
                <a href="mentors.php" class="btn btn-outline-secondary btn-sm">← Back to Mentors</a>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card bg-dark text-white p-4 shadow-sm h-100">
                        <h5 class="text-cyan mb-3">👨‍🏫 Faculty Mentor Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($data['faculty_name']); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($data['faculty_department']); ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($data['faculty_email']); ?>" class="text-cyan"><?php echo htmlspecialchars($data['faculty_email']); ?></a></p>
                        <p class="text-white-50 small mb-0">Assigned Date: <?php echo date('M d, Y', strtotime($data['assigned_date'])); ?></p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-dark text-white p-4 shadow-sm h-100">
                        <h5 class="text-cyan mb-3">💡 Mentored Idea</h5>
                        <p><strong>Title:</strong> <?php echo htmlspecialchars($data['idea_title']); ?></p>
                        <p><strong>Category:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($data['idea_category']); ?></span></p>
                        <p class="text-white-50"><strong>Description:</strong> <?php echo htmlspecialchars($data['idea_description']); ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>