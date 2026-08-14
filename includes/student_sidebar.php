<?php
// includes/student_sidebar.php
// Detect active page to highlight the active menu item
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <!-- User Info Card -->
    <div class="sidebar-user">
        <div class="avatar">
            <?php echo strtoupper(substr($_SESSION['name'] ?? 'S', 0, 1)); ?>
        </div>
        <div class="user-info">
            <h4><?php echo htmlspecialchars($_SESSION['name'] ?? 'Student'); ?></h4>
            <span class="role-badge"><?php echo htmlspecialchars($_SESSION['role'] ?? 'student'); ?></span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="sidebar-menu">
        <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <span>📊</span> Dashboard
            </a>
        </li>
        <li class="<?php echo ($current_page == 'submit_idea.php') ? 'active' : ''; ?>">
            <a href="submit_idea.php">
                <span>💡</span> Submit Idea
            </a>
        </li>
        <li class="<?php echo ($current_page == 'my_ideas.php' || $current_page == 'idea_details.php') ? 'active' : ''; ?>">
            <a href="my_ideas.php">
                <span>📁</span> My Ideas
            </a>
        </li>
        <li class="<?php echo ($current_page == 'mentors.php' || $current_page == 'mentorship_details.php') ? 'active' : ''; ?>">
            <a href="mentors.php">
                <span>👨‍🏫</span> Assigned Mentors
            </a>
        </li>
        <li class="logout-link">
            <a href="../auth/logout.php">
                <span>🚪</span> Logout
            </a>
        </li>
    </ul>
</aside>