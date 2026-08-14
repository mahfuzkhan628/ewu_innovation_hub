<?php
include "../includes/session.php";
include "../config/database.php";

$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

/*
   SQL QUERY WITH DYNAMIC FILTERS FOR FACULTY
*/
$sql = "SELECT i.idea_id, i.title, i.category, i.description, i.submitted_at, 
               u.name AS student_name, u.department AS student_dept 
        FROM ideas i 
        JOIN users u ON i.student_id = u.user_id 
        WHERE i.status = 'pending'";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (i.title LIKE ? OR u.name LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($category_filter)) {
    $sql .= " AND i.category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$sql .= " ORDER BY i.submitted_at ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reviews - EWU Innovation Hub</title>
    <link rel="icon" type="image/png" href="../assets/images/ewu_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body { 
            background-color: #0f172a !important; 
            color: #f8fafc; 
            min-height: 100vh;
            overflow-x: hidden;
        }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .main-content { flex-grow: 1; padding: 30px; width: calc(100% - 260px); }
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
    <?php include "../includes/faculty_sidebar.php"; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary">
            <h1 class="h2 text-cyan mb-0">Pending Proposals ⏳</h1>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">← Dashboard</a>
        </div>

        <!-- SEARCH & CATEGORY FILTER -->
        <div class="card bg-dark p-3 mb-4 shadow-sm">
            <form method="GET" action="pending_ideas.php" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search by proposal title or student..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">-- All Categories --</option>
                        <option value="Artificial Intelligence" <?php if($category_filter=='Artificial Intelligence') echo 'selected'; ?>>Artificial Intelligence</option>
                        <option value="Internet of Things (IoT)" <?php if($category_filter=='Internet of Things (IoT)') echo 'selected'; ?>>Internet of Things (IoT)</option>
                        <option value="Web & Software" <?php if($category_filter=='Web & Software') echo 'selected'; ?>>Web & Software</option>
                        <option value="Cybersecurity" <?php if($category_filter=='Cybersecurity') echo 'selected'; ?>>Cybersecurity</option>
                        <option value="Robotics & Hardware" <?php if($category_filter=='Robotics & Hardware') echo 'selected'; ?>>Robotics & Hardware</option>
                        <option value="Other" <?php if($category_filter=='Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-info text-dark fw-bold px-3 text-nowrap">Filter</button>
                    <a href="pending_ideas.php" class="btn btn-outline-secondary px-3 d-flex align-items-center justify-content-center" title="Reset Filters">🔄</a>
                </div>
            </form>
        </div>

        <!-- PROPOSAL LIST -->
        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card bg-dark text-white p-4 shadow-sm h-100 border-start border-warning border-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="text-white mb-0 fw-bold"><?php echo htmlspecialchars($row['title']); ?></h4>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($row['category']); ?></span>
                            </div>
                            
                            <div class="text-info small mb-3">
                                👤 Student: <strong><?php echo htmlspecialchars($row['student_name']); ?></strong> (<?php echo htmlspecialchars($row['student_dept']); ?>)
                            </div>

                            <p class="text-white-50 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                                <small class="text-white-50"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></small>
                                <a href="review_idea.php?id=<?php echo $row['idea_id']; ?>" class="btn btn-info text-dark fw-bold btn-sm">Evaluate Proposal →</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card bg-dark text-white p-5 text-center shadow-sm">
                <h4 class="text-white-50 mb-2">No Pending Proposals Found! 🎉</h4>
                <p class="text-white-50 mb-0">All submitted ideas have been evaluated or no submissions match your filter.</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php $stmt->close(); $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>