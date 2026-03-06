<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M'); // Increase memory limit for large image processing
include 'dbConnect.php';

// Function to handle background image upload
function handleBackgroundImageUpload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error_codes = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        return ['error' => $error_codes[$file['error']] ?? 'Unknown upload error'];
    }

    $upload_dir = 'uploads/backgrounds/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_image'];
    }
    
    $max_size = 10 * 1024 * 1024; // Increased to 10MB
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('bg_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

// Function to delete background image
function deleteBackgroundImage($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }
}

// Update background image
if(isset($_POST['update_background'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $page_name = mysqli_real_escape_string($con, $_POST['page_name']);
    
    $old_sql = "SELECT * FROM page_backgrounds WHERE id = '$id'";
    $old_result = mysqli_query($con, $old_sql);
    $old_bg = mysqli_fetch_assoc($old_result);
    
    $image_path = $old_bg['image_path'];
    
    if (isset($_FILES['background_image']) && $_FILES['background_image']['name'] !== '') {
        $upload_result = handleBackgroundImageUpload($_FILES['background_image']);
        if (isset($upload_result['success'])) {
            if (!empty($old_bg['image_path'])) {
                deleteBackgroundImage($old_bg['image_path']);
            }
            $image_path = $upload_result['path'];
        } else {
            $msg = $upload_result['error'];
            header("Location: background-images.php?status=err&msg=$msg");
            exit();
        }
    }
    
    if (empty($image_path)) {
        header("Location: background-images.php?status=err&msg=no_image");
        exit();
    }
    
    $sql = "UPDATE page_backgrounds SET 
            image_path = '$image_path'
            WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: background-images.php?status=succ");
    } else {
        header("Location: background-images.php?status=err&msg=db_error");
    }
    exit();
}

ob_end_flush();
?>
