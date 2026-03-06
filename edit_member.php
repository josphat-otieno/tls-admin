<?php
ob_start();
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
include 'dbConnect.php';

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/member_profiles/';
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
    $filename = uniqid('member_profile_') . '.' . $extension;
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

// Add new member
if(isset($_POST['add_member'])){
    $member_type = mysqli_real_escape_string($con, $_POST['member_type']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $profile_info = mysqli_real_escape_string($con, $_POST['profile_info']);
    $linkedin_link = mysqli_real_escape_string($con, $_POST['linkedin_link']);
    $x_link = mysqli_real_escape_string($con, $_POST['x_link']);
    $profile_image = null;
    
    // Handle profile image upload
    if (isset($_FILES['profile_image'])) {
        $upload_result = handleProfileImageUpload($_FILES['profile_image']);
        if (isset($upload_result['error'])) {
            $redirect = ($member_type == 'board') ? 'board-members.php' : 'team-members.php';
            header("Location: $redirect?status=err&msg=" . $upload_result['error']);
            exit();
        }
        $profile_image = $upload_result['path'] ?? null;
    }
    
    $image_value = $profile_image ? "'$profile_image'" : "NULL";
    $sql = "INSERT INTO members (name, role, member_type, profile_info, linkedin_link, x_link, profile_image) 
            VALUES ('$name', '$role', '$member_type', '$profile_info', '$linkedin_link', '$x_link', $image_value)";
    
    $add_member = $con->query($sql);
    
    if (!$add_member) {
        echo "Error: " . $sql . "<br>" . $con->error;
        $redirect = ($member_type == 'board') ? 'board-members.php' : 'team-members.php';
        header("Location: $redirect?status=err");
    } else {
        $redirect = ($member_type == 'board') ? 'board-members.php' : 'team-members.php';
        header("Location: $redirect?status=succ");
    }
}

// Update member
if(isset($_POST['update_member'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $member_type = mysqli_real_escape_string($con, $_POST['member_type']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $profile_info = mysqli_real_escape_string($con, $_POST['profile_info']);
    $linkedin_link = mysqli_real_escape_string($con, $_POST['linkedin_link']);
    $x_link = mysqli_real_escape_string($con, $_POST['x_link']);
    $old_profile_image = mysqli_real_escape_string($con, $_POST['old_profile_image']);
    
    $profile_image = $old_profile_image; // Keep old image by default
    
    // Handle new profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handleProfileImageUpload($_FILES['profile_image']);
        if (isset($upload_result['error'])) {
            $redirect = ($member_type == 'board') ? 'board-members.php' : 'team-members.php';
            header("Location: $redirect?status=err&msg=" . $upload_result['error']);
            exit();
        }
        // Delete old image if new one uploaded successfully
        if ($old_profile_image) {
            deleteOldProfileImage($old_profile_image);
        }
        $profile_image = $upload_result['path'] ?? $old_profile_image;
    }
    
    $image_value = $profile_image ? "'$profile_image'" : "NULL";
    $sql = "UPDATE members SET 
            name = '$name',
            role = '$role',
            member_type = '$member_type',
            profile_info = '$profile_info',
            linkedin_link = '$linkedin_link',
            x_link = '$x_link',
            profile_image = $image_value
            WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        $redirect = ($member_type == 'board') ? 'board-members.php' : 'team-members.php';
        header("Location: $redirect?status=err");
    } else {
        $redirect = ($member_type == 'board') ? 'board-members.php' : 'team-members.php';
        header("Location: $redirect?status=succ");
    }
}

// Delete member
if(isset($_GET['delete_member'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_member']);
    $type = mysqli_real_escape_string($con, $_GET['type']);
    
    // Get profile image path before deleting
    $result = $con->query("SELECT profile_image FROM members WHERE id = $id AND member_type = '$type'");
    if ($result && $row = $result->fetch_assoc()) {
        deleteOldProfileImage($row['profile_image']);
    }
    
    $sql = "DELETE FROM members WHERE id = $id AND member_type = '$type'";
    
    $delete = $con->query($sql);
    
    if($delete){
        $redirect = ($type == 'board') ? 'board-members.php' : 'team-members.php';
        header("Location: $redirect?status=succ");
    } else {
        $redirect = ($type == 'board') ? 'board-members.php' : 'team-members.php';
        header("Location: $redirect?status=err");
    }
}

ob_end_flush();
?>
