<?php
ob_start();
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
include 'dbConnect.php';

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/testimonials/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Function to handle file upload
function handleProfileImageUpload($file) {
    global $upload_dir;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['error' => 'file_too_large'];
            case UPLOAD_ERR_PARTIAL:
                return ['error' => 'upload_failed'];
            default:
                return ['error' => 'upload_failed'];
        }
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_info = @getimagesize($file['tmp_name']);
    if (!$file_info || !in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_image'];
    }

    // Validate file size (max 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = str_replace('image/', '', $file['type']);
    }
    $filename = uniqid('testimonial_profile_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

// Function to delete old profile image
function deleteOldProfileImage($image_path) {
    if ($image_path && file_exists($image_path)) {
        unlink($image_path);
    }
}

// Add new testimonial
if(isset($_POST['add_testimonial'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $company = mysqli_real_escape_string($con, $_POST['company']);
    $testimony = mysqli_real_escape_string($con, $_POST['testimony']);
    $profile_image = null;
    
    // Handle profile image upload
    if (isset($_FILES['profile_image'])) {
        $upload_result = handleProfileImageUpload($_FILES['profile_image']);
        if (isset($upload_result['error'])) {
            header("Location: testimonials.php?status=err&msg=" . $upload_result['error']);
            exit();
        }
        $profile_image = $upload_result['path'] ?? null;
    }
    
    // Get next display order
    $order_sql = "SELECT MAX(display_order) as max_order FROM testimonials";
    $order_result = mysqli_query($con, $order_sql);
    $order_row = mysqli_fetch_assoc($order_result);
    $display_order = ($order_row['max_order'] ?? 0) + 1;
    
    $image_value = $profile_image ? "'$profile_image'" : "NULL";
    $sql = "INSERT INTO testimonials (name, role, company, profile_image, testimony, display_order) 
            VALUES ('$name', '$role', '$company', $image_value, '$testimony', $display_order)";
    
    if($con->query($sql)){
        header("Location: testimonials.php?status=succ");
        exit();
    } else {
        header("Location: testimonials.php?status=err");
        exit();
    }
}

// Update testimonial
if(isset($_POST['update_testimonial'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $company = mysqli_real_escape_string($con, $_POST['company']);
    $testimony = mysqli_real_escape_string($con, $_POST['testimony']);
    $old_profile_image = mysqli_real_escape_string($con, $_POST['old_profile_image']);
    
    $profile_image = $old_profile_image; // Keep old image by default
    
    // Handle new profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handleProfileImageUpload($_FILES['profile_image']);
        if (isset($upload_result['error'])) {
            header("Location: testimonials.php?status=err&msg=" . $upload_result['error']);
            exit();
        }
        // Delete old image if new one uploaded successfully
        if ($old_profile_image) {
            deleteOldProfileImage($old_profile_image);
        }
        $profile_image = $upload_result['path'] ?? $old_profile_image;
    }
    
    $image_value = $profile_image ? "'$profile_image'" : "NULL";
    $sql = "UPDATE testimonials SET 
            name = '$name',
            role = '$role',
            company = '$company',
            profile_image = $image_value,
            testimony = '$testimony'
            WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: testimonials.php?status=succ");
        exit();
    } else {
        header("Location: testimonials.php?status=err");
        exit();
    }
}

// Toggle active status
if(isset($_GET['toggle_testimonial'])){
    $id = mysqli_real_escape_string($con, $_GET['toggle_testimonial']);
    
    $sql = "UPDATE testimonials SET is_active = NOT is_active WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: testimonials.php?status=succ");
        exit();
    } else {
        header("Location: testimonials.php?status=err");
        exit();
    }
}

// Delete testimonial
if(isset($_GET['delete_testimonial'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_testimonial']);
    
    // Get profile image path before deleting
    $result = $con->query("SELECT profile_image FROM testimonials WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        deleteOldProfileImage($row['profile_image']);
    }
    
    $sql = "DELETE FROM testimonials WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: testimonials.php?status=succ");
        exit();
    } else {
        header("Location: testimonials.php?status=err");
        exit();
    }
}

// Update display order
if(isset($_POST['update_order'])){
    $orders = $_POST['order'];
    
    foreach($orders as $id => $order){
        $id = mysqli_real_escape_string($con, $id);
        $order = mysqli_real_escape_string($con, $order);
        $sql = "UPDATE testimonials SET display_order = '$order' WHERE id = '$id'";
        $con->query($sql);
    }
    
    header("Location: testimonials.php?status=succ");
    exit();
}

ob_end_flush();
?>
