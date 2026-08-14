<?php
session_start();
include "config/database.php";

// Fetch Public Ideas (Filterable by Search or Category)
$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

$sql = "SELECT i.idea_id, i.title, i.category, i.description, i.status, i.submitted_at, 
               u.name AS student_name, u.department AS student_dept 
        FROM ideas i 
        JOIN users u ON i.student_id = u.user_id 
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (i.title LIKE ? OR i.description LIKE ?)";
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

$sql .= " ORDER BY i.submitted_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ideas_result = $stmt->get_result();

include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 hero-left">
                <h1>
                    Great Minds Can Make
                    <span>Real Innovations</span>
                </h1>
                <p>
                    EWU Innovation Hub is a platform where students can share
                    creative business and research ideas, connect with faculty mentors,
                    and build impactful solutions.
                </p>
                <div class="hero-btn">
                    <a href="register.php" class="btn primary-btn">Submit Your Idea</a>
                    <a href="#ideas" class="btn secondary-btn">Explore Platform</a>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="icon">💡</div>
                    <h3>Innovation Hub</h3>
                    <p>Ideas • Research • Mentorship</p>
                    <div class="mini-box">🚀 Build Future Solutions</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES (ABOUT) SECTION -->
<section class="features" id="about">
    <div class="container">
        <h2>Why Choose Innovation Hub?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <h3>Submit Ideas</h3>
                    <p>Share your innovative business and research concepts.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <h3>Faculty Guidance</h3>
                    <p>Get expert feedback and mentorship from faculty.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <h3>Grow Innovation</h3>
                    <p>Transform ideas into real-world projects.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WORKFLOW SECTION -->
<section class="workflow">
    <div class="container">
        <h2>How It Works</h2>
        <div class="steps">
            <div><span>1</span>Submit Idea</div>
            <div><span>2</span>Faculty Review</div>
            <div><span>3</span>Get Approved</div>
            <div><span>4</span>Mentorship</div>
        </div>
    </div>
</section>

<!-- PUBLIC IDEAS SECTION -->
<section class="public-ideas" id="ideas" style="padding: 60px 0;">
    <div class="container">
        <h2 class="text-center mb-4">All Submitted Ideas</h2>

        <!-- SEARCH & FILTER FORM -->
        <div class="search-filter-box mb-4 p-3 rounded" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.1);">
            <form method="GET" action="index.php#ideas" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search by title or keyword..." value="<?php echo htmlspecialchars($search); ?>">
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
                    <button type="submit" class="btn primary-btn w-100">Filter</button>
                    <a href="index.php#ideas" class="btn btn-outline-secondary" title="Reset">🔄</a>
                </div>
            </form>
        </div>

        <!-- IDEAS DISPLAY GRID -->
        <?php if ($ideas_result && $ideas_result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($idea = $ideas_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card h-100 d-flex flex-column justify-content-between p-4 rounded" style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1);">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span>
                                    <span class="badge <?php echo ($idea['status'] == 'approved') ? 'bg-success' : (($idea['status'] == 'rejected') ? 'bg-danger' : 'bg-warning text-dark'); ?> text-uppercase">
                                        <?php echo htmlspecialchars($idea['status']); ?>
                                    </span>
                                </div>
                                
                                <h3 class="fs-5 fw-bold text-white mb-2"><?php echo htmlspecialchars($idea['title']); ?></h3>
                                <p class="text-white-50 small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($idea['description']); ?>
                                </p>
                            </div>

                            <div class="pt-3 border-top border-secondary d-flex justify-content-between align-items-center text-white-50 small">
                                <span>👤 <?php echo htmlspecialchars($idea['student_name']); ?></span>
                                <span>📅 <?php echo date('M d, Y', strtotime($idea['submitted_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center p-5 rounded" style="background: rgba(255, 255, 255, 0.05);">
                <p class="text-white-50 mb-0">No ideas submitted yet or matching your filter.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$stmt->close();
$conn->close();
include "includes/footer.php";
?>