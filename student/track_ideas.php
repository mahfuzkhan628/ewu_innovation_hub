<?php
include "../includes/session.php";
include "../config/database.php";

$student_id = $_SESSION['user_id'];

// Get filter inputs
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

/*
   DYNAMIC SQL QUERY BUILDER WITH SEARCH & FILTER
*/
$sql = "SELECT i.idea_id, i.title, i.category, i.description, i.status, i.submitted_at, 
               r.comment AS faculty_comment, r.review_date, u.name AS faculty_name
        FROM ideas i
        LEFT JOIN reviews r ON i.idea_id = r.idea_id
        LEFT JOIN users u ON r.faculty_id = u.user_id
        WHERE i.student_id = ?";

$params = [$student_id];
$types = "i";

// Apply keyword search
if (!empty($search)) {
    $sql .= " AND (i.title LIKE ? OR i.category LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Apply status filter
if (!empty($status_filter)) {
    $sql .= " AND i.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY i.submitted_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Ideas - EWU Innovation Hub</title>
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .main-content { flex-grow: 1; padding: 30px; width: calc(100% - 260px); }
        @media (max-width: 768px) {
            .dashboard-wrapper { flex-direction: column; }
            .main-content { width: 100%; padding: 15px; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <?php include "../includes/student_sidebar.php"; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary">
            <h1 class="h2 text-cyan mb-0">Track My Submissions 📌</h1>
            <a href="submit_idea.php" class="btn btn-info text-dark fw-bold btn-sm">+ Submit New Idea</a>
        </div>

        <!-- SEARCH AND FILTER BAR -->
        <div class="card bg-dark p-3 mb-4 shadow-sm">
            <form method="GET" action="track_ideas.php" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search by project title or category..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- Filter by Status (All) --</option>
                        <option value="pending" <?php if($status_filter=='pending') echo 'selected'; ?>>🟡 Pending</option>
                        <option value="approved" <?php if($status_filter=='approved') echo 'selected'; ?>>🟢 Approved</option>
                        <option value="rejected" <?php if($status_filter=='rejected') echo 'selected'; ?>>🔴 Rejected</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-info text-dark fw-bold w-100">Filter</button>
                    <a href="track_ideas.php" class="btn btn-outline-secondary" title="Reset Filters">🔄</a>
                </div>
            </form>
        </div>

        <!-- IDEAS LIST -->
        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-12">
                        <div class="card bg-dark text-white p-4 shadow-sm border-start border-4 <?php echo ($row['status'] == 'approved') ? 'border-success' : (($row['status'] == 'rejected') ? 'border-danger' : 'border-warning'); ?>">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="text-white mb-0 fw-bold"><?php echo htmlspecialchars($row['title']); ?></h4>
                                <span class="badge bg-<?php echo ($row['status'] == 'approved') ? 'success' : (($row['status'] == 'rejected') ? 'danger' : 'warning'); ?> text-uppercase px-3 py-2">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($row['category']); ?></span>
                                <small class="text-white-50 ms-2">Submitted on: <?php echo date('F d, Y', strtotime($row['submitted_at'])); ?></small>
                            </div>

                            <p class="text-white-50" style="white-space: pre-line;"><?php echo htmlspecialchars($row['description']); ?></p>

                            <!-- Faculty Feedback Section -->
                            <?php if (!empty($row['faculty_comment'])): ?>
                                <div class="mt-3 p-3 bg-slate-800 rounded border border-secondary">
                                    <h6 class="text-info fw-bold mb-1">👨‍🏫 Faculty Feedback (by <?php echo htmlspecialchars($row['faculty_name']); ?>):</h6>
                                    <p class="text-white-50 mb-0 small"><?php echo htmlspecialchars($row['faculty_comment']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card bg-dark text-white p-5 text-center shadow-sm">
                <h4 class="text-white-50 mb-2">No Matching Submissions Found 🔍</h4>
                <p class="text-white-50 mb-0">Try adjusting your search criteria or submit a new project proposal.</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php $stmt->close(); $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>