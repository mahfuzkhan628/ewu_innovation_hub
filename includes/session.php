<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

// Student Page Protection
if (strpos($_SERVER['PHP_SELF'], "/student/") !== false && $_SESSION['role'] !== "student") {
    header("Location: ../login.php");
    exit();
}

// Faculty Page Protection
if (strpos($_SERVER['PHP_SELF'], "/faculty/") !== false && $_SESSION['role'] !== "faculty") {
    header("Location: ../login.php");
    exit();
}

?>