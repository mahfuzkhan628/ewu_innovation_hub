<?php
// Include session check and database configuration
include "../includes/session.php";
include "../config/database.php";

$message = "";

// Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve logged-in student's ID from session
    $student_id = $_SESSION['user_id'];
    
    // Sanitize and collect form input values
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $idea_file = null;
    $upload_error = '';

    if (!empty($_FILES['idea_file']['name'])) {
        $file = $_FILES['idea_file'];
        $max_size = 10 * 1024 * 1024;

        $allowed_types = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx'
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_error = 'File upload failed.';
        } elseif ($file['size'] > $max_size) {
            $upload_error = 'File size must not exceed 10 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($file['tmp_name']);

            if (!isset($allowed_types[$mime_type])) {
                $upload_error = 'Only PDF, DOC, DOCX, PPT, and PPTX files are allowed.';
            } else {
                $upload_dir = __DIR__ . '/../uploads/ideas/';

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $idea_file = bin2hex(random_bytes(16)) . '.' . $allowed_types[$mime_type];

                if (!move_uploaded_file(
                    $file['tmp_name'],
                    $upload_dir . $idea_file
                )) {
                    $upload_error = 'Unable to save the uploaded file.';
                    $idea_file = null;
                }
            }
        }
    }

    if (
        !empty($title) &&
        !empty($category) &&
        !empty($description) &&
        empty($upload_error)
    ) {
        $sql = "INSERT INTO ideas
                (student_id, title, category, description, idea_file, status)
                VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issss",
            $student_id,
            $title,
            $category,
            $description,
            $idea_file
        );

        if ($stmt->execute()) {
            // Redirect to prevent form resubmission on page refresh (PRG Pattern)
            header("Location: submit_idea.php?status=success");
            exit();
        }

        $message = '<div class="alert alert-danger">
            ❌ Database Error! Could not save your idea.
        </div>';

        $stmt->close();
    } elseif (!empty($upload_error)) {
        $message = '<div class="alert alert-warning">⚠️ ' .
            htmlspecialchars($upload_error) . '</div>';
    } else {
        $message = '<div class="alert alert-warning">
            ⚠️ Please fill in all required fields.
        </div>';
    }
}

// Display success notification if redirected after successful insertion
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  🚀 Idea submitted successfully!
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit New Idea - EWU Innovation Hub</title>
    
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
        .form-control, .form-select {
            background-color: rgba(15, 23, 42, 0.8) !important;
            border:            <?php
            // filepath: c:\xampp\htdocs\ewu_innovation_hub\faculty\download_idea_file.php
            
            include "../includes/session.php";
            include "../config/database.php";
            
            $idea_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$idea_id) {
                http_response_code(400);
                exit("Invalid idea ID.");
            }
            
            $stmt = $conn->prepare("SELECT idea_file FROM ideas WHERE id = ?");
            $stmt->bind_param("i", $idea_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $idea = $result->fetch_assoc();
            $stmt->close();
            
            if (!$idea || empty($idea['idea_file'])) {
                http_response_code(404);
                exit("No file found.");
            }
            
            $file_path = realpath(__DIR__ . "/../uploads/ideas/" . basename($idea['idea_file']));
            $upload_dir = realpath(__DIR__ . "/../uploads/ideas");
            
            if (
                !$file_path ||
                !$upload_dir ||
                strpos($file_path, $upload_dir) !== 0 ||
                !is_file($file_path)
            ) {
                http_response_code(404);
                exit("File not found.");
            }
            
            $mime_type = mime_content_type($file_path);
            
            header("Content-Type: " . $mime_type);
            header("Content-Length: " . filesize($file_path));
            header(
                'Content-Disposition: inline; filename="' .
                basename($file_path) . '"'
            );
            
            readfile($file_path);
            exit; 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #06b6d4 !important;
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.3) !important;
        }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Student Navigation Sidebar -->
        <?php include "../includes/student_sidebar.php"; ?>

        <!-- Main Submission Form Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-cyan">Submit New Innovation Idea 💡</h1>
                    <p class="text-white-50">Share your project or startup concept with faculty mentors.</p>
                </div>
            </div>

            <!-- Display Status Alerts -->
            <?php echo $message; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card bg-dark text-white p-4 shadow-sm">
                        <!-- Submission Form targeting the same file -->
                        <form action="submit_idea.php" method="POST" enctype="multipart/form-data">
                            
                            <!-- Idea Title Field -->
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Idea Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="e.g., AI-based Smart Traffic Management System" required>
                            </div>

                            <!-- Category Selection Field -->
                            <div class="mb-3">
                                <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected disabled>Select Category</option>
                                    <option value="Artificial Intelligence">Artificial Intelligence / ML</option>
                                    <option value="IoT & Hardware">IoT & Embedded Systems</option>
                                    <option value="Software & Web Tech">Software & Mobile Apps</option>
                                    <option value="FinTech & Business">FinTech & Business Innovation</option>
                                    <option value="Cybersecurity">Cybersecurity</option>
                                    <option value="Other">Other / Interdisciplinary</option>
                                </select>
                            </div>

                            <!-- Problem Statement and Description Textarea -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold">Idea Description & Problem Statement <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="6" placeholder="Explain the problem statement, proposed technology stack, and potential impact..." required></textarea>
                            </div>

                            <!-- File Upload Field -->
                            <div class="mb-4">
                                <label for="idea_file" class="form-label fw-semibold">
                                    Upload Idea File
                                </label>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="idea_file"
                                    name="idea_file"
                                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                                >

                                <div class="form-text text-white-50">
                                    Optional. PDF, DOC, DOCX, PPT, or PPTX. Maximum size: 10 MB.
                                </div>
                            </div>

                            <!-- Form Action Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">🚀 Submit Idea</button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Guidelines Panel -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card bg-dark text-white p-3 border-info">
                        <h5 class="text-cyan mb-3">📝 Submission Guidelines</h5>
                        <ul class="text-white-50 small ps-3">
                            <li class="mb-2"><strong>Title Clarity:</strong> Provide a concise and meaningful project title.</li>
                            <li class="mb-2"><strong>Define Problem:</strong> Clearly address the target problem and your proposed solution.</li>
                            <li class="mb-2"><strong>Review Process:</strong> Status remains <code>Pending</code> until assigned faculty reviews your idea.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>