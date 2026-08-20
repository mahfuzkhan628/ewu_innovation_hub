<?php
// filepath: c:\xampp\htdocs\ewu_innovation_hub\faculty\download_idea_file.php

include "../includes/session.php";
include "../config/database.php";

$idea_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idea_id) {
    http_response_code(400);
    exit("Invalid idea ID.");
}

$stmt = $conn->prepare(
    "SELECT idea_file FROM ideas WHERE idea_id = ?"
);
$stmt->bind_param("i", $idea_id);
$stmt->execute();

$idea = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$idea || empty($idea['idea_file'])) {
    http_response_code(404);
    exit("No file found.");
}

$upload_dir = realpath(__DIR__ . "/../uploads/ideas");

if (!$upload_dir) {
    http_response_code(404);
    exit("Upload directory not found.");
}

$file_path = realpath(
    $upload_dir . DIRECTORY_SEPARATOR . basename($idea['idea_file'])
);

if (
    !$file_path ||
    dirname($file_path) !== $upload_dir ||
    !is_file($file_path)
) {
    http_response_code(404);
    exit("File not found.");
}

header("Content-Type: " . mime_content_type($file_path));
header("Content-Length: " . filesize($file_path));
header(
    'Content-Disposition: inline; filename="' . basename($file_path) . '"'
);

readfile($file_path);
exit;

<?php if (!empty($idea['idea_file'])): ?>
    <a
        href="download_idea_file.php?id=<?php echo (int) $idea['id']; ?>"
        target="_blank"
        class="btn btn-sm btn-outline-info"
    >
        📎 View File
    </a>
<?php else: ?>
    <span class="text-muted">No file</span>
<?php endif; ?>

<?php
$sql = "SELECT id, student_id, title, category, description, idea_file, status
        FROM ideas
        ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>";
        echo "ID: " . $row['id'] . " | ";
        echo "Student ID: " . $row['student_id'] . " | ";
        echo "Title: " . $row['title'] . " | ";
        echo "Category: " . $row['category'] . " | ";
        echo "Description: " . $row['description'] . " | ";
        echo "File: " . $row['idea_file'] . " | ";
        echo "Status: " . $row['status'];
        echo "</p>";
    }
} else {
    echo "No ideas found.";
}
?>