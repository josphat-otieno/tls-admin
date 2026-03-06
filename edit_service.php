<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Function to handle service thumbnail upload
function handleServiceThumbnailUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'upload_failed'];
    }

    $upload_dir = 'uploads/services/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_image'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('service_thumb_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

// Function to delete old service thumbnail
function deleteOldServiceThumbnail($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }
}

// Add new service
if(isset($_POST['add_service'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $thumbnail_path = null;

    if (isset($_FILES['service_thumbnail']) && $_FILES['service_thumbnail']['error'] === 0) {
        $upload_result = handleServiceThumbnailUpload($_FILES['service_thumbnail']);
        if (isset($upload_result['success'])) {
            $thumbnail_path = $upload_result['path'];
        }
    }
    
    $thumbnail_value = $thumbnail_path ? "'" . mysqli_real_escape_string($con, $thumbnail_path) . "'" : "NULL";
    $sql = "INSERT INTO services (name, description, thumbnail) VALUES ('$name', '$description', $thumbnail_value)";
    
    $add_service = $con->query($sql);
    
    if (!$add_service) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: services.php?status=err");
    } else {
        header("Location: services.php?status=succ");
    }
}

// Update service
if(isset($_POST['update_service'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $old_thumbnail = $_POST['old_thumbnail'] ?? '';
    $thumbnail_path = $old_thumbnail;

    if (isset($_FILES['service_thumbnail']) && $_FILES['service_thumbnail']['error'] === 0) {
        $upload_result = handleServiceThumbnailUpload($_FILES['service_thumbnail']);
        if (isset($upload_result['success'])) {
            if (!empty($old_thumbnail)) {
                deleteOldServiceThumbnail($old_thumbnail);
            }
            $thumbnail_path = $upload_result['path'];
        }
    }
    
    $thumbnail_value = $thumbnail_path ? "'" . mysqli_real_escape_string($con, $thumbnail_path) . "'" : "NULL";
    $sql = "UPDATE services SET name = '$name', description = '$description', thumbnail = $thumbnail_value WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: services.php?status=err");
    } else {
        header("Location: services.php?status=succ");
    }
}

// Delete service
if(isset($_GET['delete_service'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_service']);
    
    // Get thumbnail path before deleting
    $res = $con->query("SELECT thumbnail FROM services WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        deleteOldServiceThumbnail($row['thumbnail']);
    }

    $sql = "DELETE FROM services WHERE id = $id";
    
    $delete = $con->query($sql);
    
    if($delete){
        header("Location: services.php?status=succ");
    } else {
        header("Location: services.php?status=err");
    }
}

ob_end_flush();
?>
