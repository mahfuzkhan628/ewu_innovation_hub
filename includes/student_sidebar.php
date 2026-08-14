<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$active_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar col-md-3 col-lg-2 d-md-block p-3 vh-100 position-fixed">
    <!-- Header -->
    <div class="sidebar-header text-center pb-3 mb-3 border-bottom border-secondary">
        <a href="../index.php" class="text-white text-decoration-none fw-bold fs-5 d-block">
            💡 Innovation Hub
        </a>
        <small class="text-info fw-semibold">Student Portal</small>
    </div>

    <!-- User Profile Badge -->
    <div class="user-profile mb-4 p-2 rounded bg-secondary bg-opacity-25 text-center">
        <div class="fw-bold text-truncate text-white"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Student'); ?></div>
        <small class="text-info d-block"><?php echo htmlspecialchars($_SESSION['department'] ?? 'Department'); ?></small>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="dashboard.php" class="nav-link text-white <?php echo ($active_page == 'dashboard.php') ? 'active' : ''; ?>">
                📊 Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="submit_idea.php" class="nav-link text-white <?php echo ($active_page == 'submit_idea.php') ? 'active' : ''; ?>">
                ➕ Submit New Idea
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="my_ideas.php" class="nav-link text-white <?php echo ($active_page == 'my_ideas.php') ? 'active' : ''; ?>">
                📁 My Submitted Ideas
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="mentorship.php" class="nav-link text-white <?php echo ($active_page == 'mentorship.php') ? 'active' : ''; ?>">
                👨‍🏫 My Mentors
            </a>
        </li>
    </ul>

    <hr class="text-secondary">

    <!-- Logout Link -->
    <div class="logout-btn">
        <a href="../auth/logout.php" class="btn btn-outline-danger w-100 fw-semibold">
            🚪 Logout
        </a>
    </div>
</aside>