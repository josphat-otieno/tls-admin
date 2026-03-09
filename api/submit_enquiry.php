<?php
// api/submit_enquiry.php
include_once 'api_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response('error', 'Only POST requests are allowed');
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Try regular $_POST if JSON body is empty
    $input = $_POST;
}

$email = mysqli_real_escape_string($con, $input['email'] ?? '');
$phone = mysqli_real_escape_string($con, $input['phone'] ?? $input['phone_number'] ?? '');
$message = mysqli_real_escape_string($con, $input['message'] ?? '');

if (empty($email) || empty($phone) || empty($message)) {
    response('error', 'Email, phone, and message are required');
}

$sql = "INSERT INTO enquiries (email, phone_number, message) VALUES ('$email', '$phone', '$message')";

if (mysqli_query($con, $sql)) {
    response('success', 'Enquiry submitted successfully');
} else {
    response('error', 'Failed to submit enquiry: ' . mysqli_error($con));
}
?>
