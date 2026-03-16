<?php
// api/submit_application.php
include_once 'api_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response('error', 'Only POST requests allowed');
}

$name           = mysqli_real_escape_string($con, trim($_POST['name'] ?? ''));
$email          = mysqli_real_escape_string($con, trim($_POST['email'] ?? ''));
$phone          = mysqli_real_escape_string($con, trim($_POST['phone'] ?? ''));
$job_id         = mysqli_real_escape_string($con, trim($_POST['job_id'] ?? ''));
$portfolio_link = mysqli_real_escape_string($con, trim($_POST['portfolio_link'] ?? ''));
$about_self     = mysqli_real_escape_string($con, trim($_POST['about_self'] ?? ''));

// Basic Validation
if (empty($name) || empty($email) || empty($phone) || empty($job_id)) {
    response('error', 'Missing required fields');
}

// Check if Job ID exists
$job_check = mysqli_query($con, "SELECT id FROM careers WHERE job_id = '$job_id' AND is_active = 1");
if (mysqli_num_rows($job_check) === 0) {
    response('error', 'Invalid or inactive Job ID');
}

// Handle CV Upload
if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
    response('error', 'CV file is required');
}

$upload_dir = '../uploads/cvs/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
$safe_name = preg_replace("/[^a-zA-Z0-9]/", "_", $name);
$filename = $safe_name . '_' . time() . '.' . $file_ext;
$target_file = $upload_dir . $filename;

if (!move_uploaded_file($_FILES['cv']['tmp_name'], $target_file)) {
    response('error', 'Failed to upload CV');
}

$cv_path = 'uploads/cvs/' . $filename; // Relative path for storage

$sql = "INSERT INTO job_applications (job_id, name, email, phone, cv_path, portfolio_link, about_self)
        VALUES ('$job_id', '$name', '$email', '$phone', '$cv_path', '$portfolio_link', '$about_self')";

if (mysqli_query($con, $sql)) {
    response('success', 'Application submitted successfully');
} else {
    // Cleanup file if DB insert fails
    if (file_exists($target_file)) {
        unlink($target_file);
    }
    response('error', 'Failed to save application: ' . mysqli_error($con));
}
?>
