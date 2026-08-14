<?php
// Retrieve active script name for dynamic highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="col-md-3 col-lg-2 d-md-block border-end border-secondary min-vh-100 p-3" style="background: rgba(15, 23, 42, 0.95); min-width: 240px;">
    <!-- Portal Header -->
    <div class="text-center pb-3 mb-4 border-bottom border-secondary">
        <h5 class="text-cyan fw-bold mb-1">EWU Innovation Hub</h5>
        <span class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-20 px-2 py-1">
            Faculty Portal
        </span>
    </div>

    <!-- Faculty User Mini Profile -->
    <div class="p-3 mb-4 rounded-3 text-center" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);">
        <div class="rounded-circle bg-cyan bg-opacity-10 text-cyan d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold" style="width: 48px; height: 48px; border: 1px solid rgba(6, 182, 212, 0.3);">
            <?php echo strtoupper(substr($_SESSION['name'] ?? 'F', 0, 1)); ?>
        </div>
        <h6 class="text-white mb-0 text-truncate"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Faculty Member'); ?></h6>
        <small class="text-white-50 d-block text-truncate"><?php echo htmlspecialchars($_SESSION['department'] ?? 'Department'); ?></small>
    </div>

    <!-- Navigation Menu (Strictly Matching Your Folder Structure) -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link text-white d-flex align-items-center gap-2 <?php echo ($current_page == 'dashboard.php') ? 'active bg-info text-dark fw-bold' : ''; ?>">
                📊 <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="pending_ideas.php" class="nav-link text-white d-flex align-items-center gap-2 <?php echo ($current_page == 'pending_ideas.php' || $current_page == 'review_idea.php') ? 'active bg-info text-dark fw-bold' : ''; ?>">
                ⏳ <span>Pending Ideas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="my_students.php" class="nav-link text-white d-flex align-items-center gap-2 <?php echo ($current_page == 'my_students.php' || $current_page == 'mentorship.php') ? 'active bg-info text-dark fw-bold' : ''; ?>">
                👨‍🎓 <span>My Students / Mentees</span>
            </a>
        </li>
    </ul>

    <hr class="border-secondary my-4">

    <!-- Logout Action -->
    <a href="../logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
        🚪 <span>Logout</span>
    </a>
</aside>